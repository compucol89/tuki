<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Organizer;
use App\Models\Transaction;
use App\Models\Withdraw;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\PHPMailer;

class WithdrawController extends Controller
{
  private function getWithdrawOrFail($withdrawId)
  {
    return Withdraw::where('id', $withdrawId)->firstOrFail();
  }

  //index
  public function index()
  {
    $search = request()->input('search');

    $collection = Withdraw::with('method')
      ->when($search, function ($query, $keyword) {
        return $query->where('withdraws.withdraw_id', 'like', '%' . $keyword . '%');
      })
      ->orderBy('id', 'desc')->paginate(10);
    $currencyInfo = $this->getCurrencyInfo();
    return view('backend.withdraw.history.index', compact('collection', 'currencyInfo'));
  }
  //delete
  public function delete(Request $request)
  {
    $withdraw = $this->getWithdrawOrFail($request->id);

    if ((int) $withdraw->status !== 0) {
      return redirect()->back()->with('warning', 'Only pending withdraw requests can be deleted.');
    }

    DB::transaction(function () use ($withdraw) {
      $organizer = Organizer::where('id', $withdraw->organizer_id)->lockForUpdate()->firstOrFail();
      $organizer->amount += $withdraw->amount;
      $organizer->save();

      $transaction = Transaction::where('booking_id', $withdraw->id)
        ->where('transcation_type', 3)
        ->first();

      if ($transaction) {
        $transaction->update(['payment_status' => 2]);
      }

      $withdraw->delete();
    });

    return redirect()->back()->with('success', 'Deleted Successfully');
  }

  //approve
  public function approve($id)
  {
    $withdraw = $this->getWithdrawOrFail($id);

    if ((int) $withdraw->status !== 0) {
      return redirect()->back()->with('warning', 'This withdraw request has already been processed.');
    }

    //mail sending
    // get the website title & mail's smtp information from db
    $info = Basic::select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name', 'base_currency_symbol_position', 'base_currency_symbol')
      ->first();

    //preparing mail info
    // get the mail template info from db
    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'withdraw_approve')->first();
    $mailData['subject'] = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    // get the website title info from db
    $website_info = Basic::select('website_title')->first();

    $organizer = $withdraw->organizer()->first();

    // preparing dynamic data
    $organizerName = $organizer->username;
    $organizerEmail = $organizer->email;
    $organizer_amount = $organizer->amount;
    $withdraw_amount = $withdraw->amount;
    $total_charge = $withdraw->total_charge;
    $payable_amount = $withdraw->payable_amount;

    $method = $withdraw->method()->select('name')->first();

    $websiteTitle = $website_info->website_title;

    // replacing with actual data
    $mailBody = str_replace('{organizer_username}', $organizerName, $mailBody);
    $mailBody = str_replace('{withdraw_id}', $withdraw->withdraw_id, $mailBody);

    $mailBody = str_replace('{current_balance}', $info->base_currency_symbol . $organizer_amount, $mailBody);
    $mailBody = str_replace('{withdraw_amount}', $info->base_currency_symbol . $withdraw_amount, $mailBody);
    $mailBody = str_replace('{charge}', $info->base_currency_symbol . $total_charge, $mailBody);
    $mailBody = str_replace('{payable_amount}', $info->base_currency_symbol . $payable_amount, $mailBody);

    $mailBody = str_replace('{withdraw_method}', $method->name, $mailBody);
    $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);

    $mailData['body'] = $mailBody;

    $mailData['recipient'] = $organizerEmail;
    //preparing mail info end

    // initialize a new mail
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // if smtp status == 1, then set some value for PHPMailer
    if ($info->smtp_status == 1) {
      $mail->isSMTP();
      $mail->Host       = $info->smtp_host;
      $mail->SMTPAuth   = true;
      $mail->Username   = $info->smtp_username;
      $mail->Password   = $info->smtp_password;

      if ($info->encryption == 'TLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      }

      $mail->Port       = $info->smtp_port;
    }

    // add other informations and send the mail
    try {
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($mailData['recipient']);

      $mail->isHTML(true);
      $mail->Subject = $mailData['subject'];
      $mail->Body = $mailData['body'];

      $mail->send();
      Session::flash('success', 'Withdraw Request Approved Successfully!');
    } catch (Exception $e) {
      Session::flash('warning', 'Mail could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }
    try {
      DB::transaction(function () use ($withdraw) {
        $lockedWithdraw = Withdraw::where('id', $withdraw->id)->lockForUpdate()->firstOrFail();

        if ((int) $lockedWithdraw->status !== 0) {
          throw new \RuntimeException('This withdraw request has already been processed.');
        }

        $lockedWithdraw->status = 1;

        $transaction = Transaction::where('booking_id', $lockedWithdraw->id)
          ->where('transcation_type', 3)
          ->first();

        if ($transaction) {
          $transaction->update(['payment_status' => 1]);
        }

        $lockedWithdraw->save();
      });
    } catch (\RuntimeException $exception) {
      return redirect()->back()->with('warning', $exception->getMessage());
    }

    return redirect()->back();
  }
  //decline
  public function decline($id)
  {
    $withdraw = $this->getWithdrawOrFail($id);

    if ((int) $withdraw->status !== 0) {
      return redirect()->back()->with('warning', 'This withdraw request has already been processed.');
    }

    //mail sending
    // get the website title & mail's smtp information from db
    $info = Basic::select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name', 'base_currency_symbol_position', 'base_currency_symbol')
      ->first();

    //preparing mail info
    // get the mail template info from db
    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'withdraw_rejected')->first();
    $mailData['subject'] = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    // get the website title info from db
    $website_info = Basic::select('website_title')->first();

    $organizer = $withdraw->organizer()->first();

    // preparing dynamic data
    $organizerName = $organizer->username;
    $organizerEmail = $organizer->email;
    $organizer_amount = $organizer->amount + $withdraw->amount;

    $method = $withdraw->method()->select('name')->first();

    $websiteTitle = $website_info->website_title;

    // replacing with actual data
    $mailBody = str_replace('{organizer_username}', $organizerName, $mailBody);
    $mailBody = str_replace('{withdraw_id}', $withdraw->withdraw_id, $mailBody);

    $mailBody = str_replace('{current_balance}', $info->base_currency_symbol . $organizer_amount, $mailBody);
    $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);

    $mailData['body'] = $mailBody;

    $mailData['recipient'] = $organizerEmail;
    //preparing mail info end

    // initialize a new mail
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // if smtp status == 1, then set some value for PHPMailer
    if ($info->smtp_status == 1) {
      $mail->isSMTP();
      $mail->Host       = $info->smtp_host;
      $mail->SMTPAuth   = true;
      $mail->Username   = $info->smtp_username;
      $mail->Password   = $info->smtp_password;

      if ($info->encryption == 'TLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      }
      $mail->Port       = $info->smtp_port;
    }

    // add other informations and send the mail
    try {
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($mailData['recipient']);

      $mail->isHTML(true);
      $mail->Subject = $mailData['subject'];
      $mail->Body = $mailData['body'];

      $mail->send();
      Session::flash('success', 'Withdraw request decline & balance return to vendor account successfully!');
    } catch (Exception $e) {
      Session::flash('warning', 'Mail could not be sent.');
    }
    try {
      DB::transaction(function () use ($withdraw) {
        $lockedWithdraw = Withdraw::where('id', $withdraw->id)->lockForUpdate()->firstOrFail();

        if ((int) $lockedWithdraw->status !== 0) {
          throw new \RuntimeException('This withdraw request has already been processed.');
        }

        $organizer = Organizer::where('id', $lockedWithdraw->organizer_id)->lockForUpdate()->firstOrFail();
        $organizer->amount = $organizer->amount + $lockedWithdraw->amount;
        $organizer->save();

        $transaction = Transaction::where([
          ['booking_id', $lockedWithdraw->id],
          ['transcation_type', 3]
        ])->first();

        if ($transaction) {
          $transaction->update(['payment_status' => 2]);
        }

        $lockedWithdraw->status = 2;
        $lockedWithdraw->save();
      });
    } catch (\RuntimeException $exception) {
      return redirect()->back()->with('warning', $exception->getMessage());
    }

    return redirect()->back();
  }
}
