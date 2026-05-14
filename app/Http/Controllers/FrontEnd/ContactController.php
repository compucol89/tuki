<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\BasicSettings\Basic;
use App\Models\ContactPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class ContactController extends Controller
{
  public function contact()
  {
    $language = $this->getLanguage();

    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_contact', 'meta_description_contact')->first();

    $queryResult['pageHeading'] = $this->getPageHeading($language);

    $queryResult['bgImg'] = $this->getBreadcrumb();

    $queryResult['info'] = ContactPage::where('language_id', $language->id)->first();
    $queryResult['contact_info'] = Basic::select('latitude', 'longitude')->first();

    return view('frontend.contact', $queryResult);
  }

  public function sendMail(Request $request)
  {

    $info = DB::table('basic_settings')
      ->select('google_recaptcha_status', 'website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name', 'to_mail')
      ->first();

    $rules = [
      'name' => 'required|max:255',
      'email' => 'required|email:rfc,dns|max:255',
      'subject' => 'required|max:255',
      'message' => 'required|max:5000'
    ];

    if ($info->google_recaptcha_status == 1) {
      $rules['g-recaptcha-response'] = 'required|captcha';
    }

    $msgs = [];

    if ($info->google_recaptcha_status == 1) {
      $msgs['g-recaptcha-response.required'] = __('Por favor, verificá que no sos un robot.');
      $msgs['g-recaptcha-response.captcha'] = __('Error de verificación. Intentalo de nuevo.');
    }

    $validator = Validator::make($request->all(), $rules, $msgs);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    $name = $request->name;
    $to = $info->to_mail;
    $subject = preg_replace('/[\r\n]/', '', $request->subject);

    // Validar destinatario antes de intentar enviar
    if (empty($to)) {
      Log::error('Contact form: to_mail is empty in basic_settings');
      Session::flash('error', __('Error de configuración. Contactá al administrador.'));
      return redirect()->back();
    }

    $message = '<p>' . e($request->message) . '</p> <p><strong>Nombre: </strong>' . e($name) . '<br/><strong>Email: </strong>' . e($request->email) . '</p>';

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    if ($info->smtp_status == 1) {
      $mail->isSMTP();
      $mail->Host       = $info->smtp_host;

      if (!empty($info->smtp_username)) {
        $mail->SMTPAuth   = true;
        $mail->Username   = $info->smtp_username;
        $mail->Password   = $info->smtp_password;
      }

      if ($info->encryption == 'TLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      }

      $mail->Port       = $info->smtp_port;
    }

    try {
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($to);

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $message;

      $mail->send();

      Session::flash('success', __('Tu mensaje fue enviado correctamente.'));
    } catch (Exception $e) {
      Log::error('Contact form mail failed: ' . $e->getMessage());
      Session::flash('error', __('No pudimos enviar tu mensaje. Intentalo de nuevo más tarde.'));
    }

    return redirect()->back();
  }
}
