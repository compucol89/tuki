<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\Admin;
use App\Models\BasicSettings\Basic;
use App\Models\Customer;
use App\Models\Earning;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\EventCategory;
use App\Models\Journal\Blog;
use App\Models\Language;
use App\Models\Organizer;
use App\Models\ShopManagement\Product;
use App\Models\ShopManagement\ProductOrder;
use App\Models\Transaction;
use App\Rules\ImageMimeTypeRule;
use App\Rules\MatchOldPasswordRule;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class AdminController extends Controller
{
  public function login()
  {
    return view('backend.login');
  }

  public function authentication(Request $request)
  {
    $rules = [
      'username' => 'required',
      'password' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if (
      Auth::guard('admin')->attempt([
        'username' => $request->username,
        'password' => $request->password
      ])
    ) {
      $authAdmin = Auth::guard('admin')->user();

      // check whether the admin's account is active or not
      if ($authAdmin->status == 0) {
        Session::flash('alert', 'Sorry, your account has been deactivated!');

        // logout auth admin as condition not satisfied
        Auth::guard('admin')->logout();

        return redirect()->back();
      } else {
        return redirect()->route('admin.dashboard');
      }
    } else {
      return redirect()->back()->with('alert', __('Oops, username or password does not match!'));
    }
  }

  public function forgetPassword()
  {
    return view('backend.forget-password');
  }

  public function sendMail(Request $request)
  {
    $rules = [
      'email' => [
        'required',
        'email:rfc,dns',
      ]
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    $genericMessage = 'Si tu email está registrado, te enviamos un link para restablecer tu contraseña.';
    $admin = Admin::where('email', $request->email)->first();

    if (!$admin) {
      usleep(random_int(100000, 500000));
      Session::flash('success', $genericMessage);
      return redirect()->back();
    }

    $token = Str::random(64);

    DB::table('password_resets')->where('email', $admin->email)->delete();

    DB::table('password_resets')->insert([
      'email' => $admin->email,
      'token' => Hash::make('admin|' . $token),
      'created_at' => now(),
    ]);

    $link = route('admin.reset_password', ['token' => $token], true);

    $info = DB::table('basic_settings')
      ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

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

    // finally add other informations and send the mail
    try {
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($request->email);

      $mail->isHTML(true);
      $mail->Subject = 'Reset Password - ' . ($info->website_title ?? 'Tukipass');
      $mail->Body = 'Hello ' . $admin->first_name . ',<br/><br/>Recibimos un pedido para restablecer la contraseña de tu cuenta.<br/><br/>Si vos lo pediste, ingresá al siguiente link para definir una nueva contraseña:<br/><br/><a href="' . $link . '">' . $link . '</a><br/><br/>Este link expira en 60 minutos y solo puede usarse una vez.<br/><br/>Si no realizaste este pedido, podés ignorar este mensaje.';

      $mail->send();

      Session::flash('success', $genericMessage);
    } catch (Exception $e) {
      Log::error('Admin password reset mail failed: ' . $e->getMessage());
      DB::table('password_resets')->where('email', $admin->email)->delete();
      Session::flash('warning', 'No pudimos enviar el mail. Intentalo de nuevo más tarde.');
    }

    return redirect()->back();
  }

  public function resetPassword(Request $request)
  {
    return view('backend.reset-password', ['token' => $request->query('token')]);
  }

  public function updateResetPassword(Request $request)
  {
    $request->validate([
      'token' => 'required',
      'password' => 'required|confirmed|min:10',
    ]);

    $token = $request->token;

    $row = null;
    foreach (DB::table('password_resets')->get() as $candidate) {
      try {
        if (Hash::check('admin|' . $token, $candidate->token)) {
          $row = $candidate;
          break;
        }
      } catch (\Exception $e) {
        continue;
      }
    }

    if (!$row) {
      Session::flash('alert', 'El link de recuperación es inválido o ya fue utilizado.');
      return redirect()->route('admin.login');
    }

    if ($row->created_at && \Carbon\Carbon::parse($row->created_at)->diffInMinutes(now()) > 60) {
      DB::table('password_resets')->where('email', $row->email)->delete();
      Session::flash('alert', 'El link de recuperación expiró. Pedí uno nuevo.');
      return redirect()->route('admin.forget_password');
    }

    $admin = Admin::where('email', $row->email)->first();
    if (!$admin) {
      DB::table('password_resets')->where('email', $row->email)->delete();
      Session::flash('alert', 'Cuenta no encontrada.');
      return redirect()->route('admin.login');
    }

    $admin->password = Hash::make($request->password);
    $admin->save();

    DB::table('password_resets')->where('email', $row->email)->delete();

    Log::info('Admin password reset completed', ['admin_id' => $admin->id, 'email' => $admin->email]);

    Session::flash('success', 'Tu contraseña fue actualizada. Iniciá sesión.');
    return redirect()->route('admin.login');
  }

  public function redirectToDashboard()
  {
    $language = Language::query()->where('is_default', '=', 1)->first();

    $information['basic'] = Basic::where('uniqid', 12345)->select('base_currency_symbol', 'base_currency_symbol_position')->first();

    $information['totalEvents'] = Event::query()->count();
    $information['totalEventCategories'] = EventCategory::where('language_id', $language->id)->count();
    $information['totalEventBookings'] = Booking::query()->count();
    $information['totalOrganizers'] = Organizer::query()->count();
    $information['totalBlog'] = Blog::query()->count();
    $information['totalRegisteredUsers'] = Customer::query()->count();
    $information['totalProducts'] = Product::query()->count();
    $information['totalOrders'] = ProductOrder::query()->count();
    $information['transcation_count'] = Transaction::query()->count();

    $information['total_earning'] = Earning::first();


    //income of event bookings 
    $eventBookingTotalIncomes = DB::table('bookings')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(price) as total'))
      ->where('paymentStatus', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();
    //income from tax
    $monthWiseTotaltaxs = DB::table('bookings')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(tax) as total'))
      ->where('paymentStatus', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();

    $TotalEventBookings = DB::table('bookings')
      ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
      ->where('paymentStatus', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();

    //income of Product Order 
    $produtOrderTotalIncomes = DB::table('product_orders')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(total) as total'))
      ->where('payment_status', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();

    $totalProductOrder = DB::table('product_orders')
      ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
      ->where('payment_status', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->get();

    $eventMonths = [];

    $eventIncomes = [];
    $eventTaxes = [];
    $totalBookings = [];

    $productIncome = [];
    $totalOders = [];
    $monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];


    //event icome calculation
    for ($i = 1; $i <= 12; $i++) {
      // get all 12 months name
      $monthNum = $i;
      $monthName = $monthNames[$monthNum - 1];
      array_push($eventMonths, $monthName);

      // get all 12 months's income
      $incomeFound = false;
      foreach ($eventBookingTotalIncomes as $eventIncomeInfo) {
        if ($eventIncomeInfo->month == $i) {
          $incomeFound = true;
          array_push($eventIncomes, $eventIncomeInfo->total);
          break;
        }
      }
      if ($incomeFound == false) {
        array_push($eventIncomes, 0);
      }
      // get all 12 months's taxes
      $taxFound = false;
      foreach ($monthWiseTotaltaxs as $monthWiseTotaltax) {
        if ($monthWiseTotaltax->month == $i) {
          $taxFound = true;
          array_push($eventTaxes, $monthWiseTotaltax->total);
          break;
        }
      }
      if ($taxFound == false) {
        array_push($eventTaxes, 0);
      }


      // get all 12 months's c
      $bookingFound = false;

      foreach ($TotalEventBookings as $eventInfo) {
        if ($eventInfo->month == $i) {
          $bookingFound = true;
          array_push($totalBookings, $eventInfo->total);
          break;
        }
      }

      if ($bookingFound == false) {
        array_push($totalBookings, 0);
      }

      // get all 12 months's 
      $orderFound = false;

      foreach ($produtOrderTotalIncomes as $productInfo) {
        if ($productInfo->month == $i) {
          $orderFound = true;
          array_push($productIncome, $productInfo->total);
          break;
        }
      }

      if ($orderFound == false) {
        array_push($productIncome, 0);
      }
      // get all 12 months's 
      $orderTotalFound = false;

      foreach ($totalProductOrder as $productTotalInfo) {
        if ($productTotalInfo->month == $i) {
          $orderTotalFound = true;
          array_push($totalOders, $productTotalInfo->total);
          break;
        }
      }

      if ($orderTotalFound == false) {
        array_push($totalOders, 0);
      }
    }
    $arry = [];
    foreach ($eventIncomes as $key => $eventIncome) {
      array_push($arry, round($eventIncome + $eventTaxes[$key], 2));
    }

    $information['eventIncomes'] = $arry;
    $information['eventMonths'] = $eventMonths;
    $information['totalBookings'] = $totalBookings;

    $information['productIncome'] = $productIncome;
    $information['totalOders'] = $totalOders;

    return view('backend.admin.dashboard', $information);
  }

  public function changeTheme(Request $request)
  {
    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      ['admin_theme_version' => $request->admin_theme_version]
    );

    return redirect()->back();
  }

  public function editProfile()
  {
    $adminInfo = Auth::guard('admin')->user();

    return view('backend.admin.edit-profile', compact('adminInfo'));
  }

  public function updateProfile(Request $request)
  {
    $admin = Auth::guard('admin')->user();

    $rules = [];

    if (!$request->filled('image') && is_null($admin->image)) {
      $rules['image'] = 'required';
    }
    if ($request->hasFile('image')) {
      $rules['image'] = new ImageMimeTypeRule();
    }

    $rules['username'] = [
      'required',
      Rule::unique('admins')->ignore($admin->id)
    ];

    $rules['email'] = [
      'required',
      'email:rfc,dns',
      Rule::unique('admins')->ignore($admin->id)
    ];

    $rules['first_name'] = 'required';

    $rules['last_name'] = 'required';

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if ($request->hasFile('image')) {
      $imageName = UploadFile::update(public_path('assets/admin/img/admins/'), $request->file('image'), $admin->image);
    }

    $admin->update([
      'first_name' => $request->first_name,
      'last_name' => $request->last_name,
      'image' => $request->hasFile('image') ? $imageName : $admin->image,
      'username' => $request->username,
      'email' => $request->email,
      'phone' => $request->phone,
      'address' => $request->address,
      'details' => $request->details,
    ]);
    Session::flash('success', 'Profile updated successfully!');

    return redirect()->back();
  }

  public function changePassword()
  {
    return view('backend.admin.change-password');
  }

  public function updatePassword(Request $request)
  {
    $rules = [
      'current_password' => [
        'required',
        new MatchOldPasswordRule('admin')
      ],
      'new_password' => 'required|confirmed|min:10',
      'new_password_confirmation' => 'required'
    ];

    $messages = [
      'new_password.confirmed' => 'Password confirmation does not match.',
      'new_password_confirmation.required' => 'The confirm new password field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $admin = Auth::guard('admin')->user();

    $admin->update([
      'password' => Hash::make($request->new_password)
    ]);

    Session::flash('success', __('admin.flash.updated_successfully'));

    return response()->json(['status' => 'success'], 200);
  }

  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();

    return redirect()->route('admin.login');
  }

  //transcation 
  public function transcation(Request $request)
  {
    $filters = [
      'q' => $request->input('q', $request->input('transcation_id')),
      'event_id' => $request->input('event_id'),
      'period' => $request->input('period'),
      'from_date' => $request->input('from_date'),
      'to_date' => $request->input('to_date'),
      'payment_method' => $request->input('payment_method'),
      'payment_status' => $request->input('payment_status'),
      'transcation_type' => $request->input('transcation_type'),
      'gateway_type' => $request->input('gateway_type'),
    ];

    $defaultLanguage = Language::where('is_default', 1)->first();

    $query = Transaction::query();

    if (!empty($filters['q'])) {
      $search = $filters['q'];

      $query->where(function ($query) use ($search) {
        $query->where('transcation_id', 'like', '%' . $search . '%')
          ->orWhere('booking_id', 'like', '%' . $search . '%')
          ->orWhereHas('organizer', function ($query) use ($search) {
            $query->where('username', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%');
          })
          ->orWhereHas('event_booking', function ($query) use ($search) {
            $query->where('booking_id', 'like', '%' . $search . '%')
              ->orWhere('conversation_id', 'like', '%' . $search . '%')
              ->orWhere('fname', 'like', '%' . $search . '%')
              ->orWhere('lname', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhere('phone', 'like', '%' . $search . '%')
              ->orWhereHas('event', function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
              });
          })
          ->orWhereHas('product_order', function ($query) use ($search) {
            $query->where('order_number', 'like', '%' . $search . '%')
              ->orWhere('conversation_id', 'like', '%' . $search . '%')
              ->orWhere('tnxid', 'like', '%' . $search . '%')
              ->orWhere('charge_id', 'like', '%' . $search . '%')
              ->orWhere('billing_fname', 'like', '%' . $search . '%')
              ->orWhere('billing_lname', 'like', '%' . $search . '%')
              ->orWhere('billing_email', 'like', '%' . $search . '%')
              ->orWhere('billing_phone', 'like', '%' . $search . '%');
          });
      });
    }

    if (!empty($filters['event_id'])) {
      $query->whereHas('event_booking', function ($query) use ($filters) {
        $query->where('event_id', $filters['event_id']);
      });
    }

    if (!empty($filters['payment_method'])) {
      $query->where('payment_method', $filters['payment_method']);
    }

    if (!empty($filters['payment_status'])) {
      if ($filters['payment_status'] === 'legacy_paid') {
        $query->where('payment_status', 1);
      } elseif ($filters['payment_status'] === 'legacy_declined') {
        $query->where('payment_status', 2);
      } elseif ($filters['payment_status'] === 'legacy_unpaid') {
        $query->where(function ($query) {
          $query->where('payment_status', 0)->orWhereNull('payment_status')->orWhere('payment_status', '');
        });
      } else {
        $query->where('payment_status', $filters['payment_status']);
      }
    }

    if (!empty($filters['transcation_type'])) {
      $query->where('transcation_type', $filters['transcation_type']);
    }

    if (!empty($filters['gateway_type'])) {
      $query->where('gateway_type', $filters['gateway_type']);
    }

    if ($filters['period'] === 'today') {
      $query->whereDate('created_at', now()->toDateString());
    } elseif ($filters['period'] === 'week') {
      $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    } elseif ($filters['period'] === 'month') {
      $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
    } else {
      if (!empty($filters['from_date'])) {
        $query->whereDate('created_at', '>=', $filters['from_date']);
      }

      if (!empty($filters['to_date'])) {
        $query->whereDate('created_at', '<=', $filters['to_date']);
      }
    }

    $summaryRows = (clone $query)
      ->select('transcation_type', 'grand_total', 'commission', 'payment_method')
      ->get();
    $transactionSummary = [
      'count' => $summaryRows->count(),
      'income' => 0,
      'expenses' => 0,
      'net' => 0,
      'commission' => 0,
      'mercadopago_total' => 0,
      'mercadopago_count' => 0,
    ];

    foreach ($summaryRows as $row) {
      $amount = (float) $row->grand_total - (float) $row->commission;
      $signedAmount = in_array((int) $row->transcation_type, [3, 5], true) ? -$amount : $amount;

      if ($signedAmount >= 0) {
        $transactionSummary['income'] += $signedAmount;
      } else {
        $transactionSummary['expenses'] += abs($signedAmount);
      }

      $transactionSummary['net'] += $signedAmount;
      $transactionSummary['commission'] += (float) $row->commission;

      if (str_replace(' ', '', strtolower((string) $row->payment_method)) === 'mercadopago') {
        $transactionSummary['mercadopago_total'] += $signedAmount;
        $transactionSummary['mercadopago_count']++;
      }
    }

    $eventOptions = DB::table('bookings')
      ->join('transactions', function ($join) {
        $join->on('transactions.booking_id', '=', 'bookings.id')
          ->where('transactions.transcation_type', 1);
      })
      ->join('event_contents', 'event_contents.event_id', '=', 'bookings.event_id')
      ->when($defaultLanguage, function ($query) use ($defaultLanguage) {
        return $query->where('event_contents.language_id', $defaultLanguage->id);
      })
      ->select('bookings.event_id', 'event_contents.title')
      ->distinct()
      ->orderBy('event_contents.title')
      ->get();

    $paymentMethods = Transaction::query()
      ->whereNotNull('payment_method')
      ->where('payment_method', '!=', '')
      ->distinct()
      ->orderBy('payment_method')
      ->pluck('payment_method');

    $transcations = $query->with([
      'organizer',
      'event_booking.event' => function ($query) use ($defaultLanguage) {
        if ($defaultLanguage) {
          $query->where('language_id', $defaultLanguage->id);
        }
      },
      'product_order',
      'method'
    ])->orderBy('id', 'desc')->paginate(15);

    return view('backend.admin.transaction', compact('transcations', 'transactionSummary', 'eventOptions', 'paymentMethods', 'filters'));
  }
  //destroy
  public function destroy(Request $request)
  {
    $transcation = Transaction::where('id', $request->id)->first();
    $transcation->delete();
    Session::flash('success', __('admin.flash.deleted_successfully'));

    return back();
  }

  //destroy
  public function bulk_destroy(Request $request)
  {
    $ids = $request->ids;
    foreach ($ids as $id) {
      $transcation = Transaction::where('id', $id)->first();
      $transcation->delete();
    }
    Session::flash('success', __('admin.flash.deleted_successfully'));

    return response()->json(['status' => 'success'], 200);
  }

  //monthly  earning
  public function monthly_earning(Request $request)
  {
    if ($request->filled('year')) {
      $date = $request->input('year');
    } else {
      $date = date('Y');
    }
    $monthWiseTotalIncomes = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(grand_total) as total'))
      ->where('payment_status', 1)
      ->where(function ($query) {
        return $query->whereNotIn('transcation_type', [3, 4, 5]);
      })
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();

    $monthWiseTotaltaxs = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(tax) as total'))
      ->where('payment_status', 1)
      ->where(function ($query) {
        return $query->whereNotIn('transcation_type', [2, 3, 4, 5]);
      })
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();


    $months = [];
    $incomes = [];
    $taxs = [];
    for ($i = 1; $i <= 12; $i++) {
      // get all 12 months name
      $monthNum = $i;
      $dateObj = DateTime::createFromFormat('!m', $monthNum);
      $monthName = $dateObj->format('F');
      array_push($months, $monthName);

      // get all 12 months's income of equipment booking
      $incomeFound = false;
      foreach ($monthWiseTotalIncomes as $incomeInfo) {
        if ($incomeInfo->month == $i) {
          $incomeFound = true;
          array_push($incomes, $incomeInfo->total);
          break;
        }
      }
      if ($incomeFound == false) {
        array_push($incomes, 0);
      }

      // get all 12 months's income of equipment booking
      $taxFound = false;
      foreach ($monthWiseTotaltaxs as $taxInfo) {
        if ($taxInfo->month == $i) {
          $taxFound = true;
          array_push($taxs, $taxInfo->total);
          break;
        }
      }
      if ($taxFound == false) {
        array_push($taxs, 0);
      }
    }
    $information['months'] = $months;
    $information['incomes'] = $incomes;
    $information['taxs'] = $taxs;

    return view('backend.admin.earning', $information);
  }

  //monthly  income
  public function monthly_profit(Request $request)
  {
    if ($request->filled('year')) {
      $date = $request->input('year');
    } else {
      $date = date('Y');
    }
    $monthWiseTotalIncomes = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(commission) as total'))
      ->where('payment_status', 1)
      ->where('organizer_id', '!=', null)
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();

    $monthWiseTotalProfits = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(tax) as total'))
      ->where('payment_status', 1)
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();

    $monthWiseTotalAdminProfits = DB::table('transactions')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(grand_total) as total'))
      ->where('payment_status', 1)
      ->where('organizer_id', '=', null)
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();



    $months = [];
    $incomes = [];
    $taxs = [];
    $admin_profit = [];
    for ($i = 1; $i <= 12; $i++) {
      // get all 12 months name
      $monthNum = $i;
      $dateObj = DateTime::createFromFormat('!m', $monthNum);
      $monthName = $dateObj->format('M');
      array_push($months, $monthName);

      // get all 12 months's income of event booking
      $incomeFound = false;
      foreach ($monthWiseTotalIncomes as $incomeInfo) {
        if ($incomeInfo->month == $i) {
          $incomeFound = true;
          array_push($incomes, $incomeInfo->total);
          break;
        }
      }
      if ($incomeFound == false) {
        array_push($incomes, 0);
      }

      // get all 12 months's tax's of event booking
      $taxFound = false;
      foreach ($monthWiseTotalProfits as $profitInfo) {
        if ($profitInfo->month == $i) {
          $taxFound = true;
          array_push($taxs, $profitInfo->total);
          break;
        }
      }
      if ($taxFound == false) {
        array_push($taxs, 0);
      }

      // get all 12 months's tax's of event booking
      $adminProfitFound = false;
      foreach ($monthWiseTotalAdminProfits as $AdminProfit) {
        if ($AdminProfit->month == $i) {
          $adminProfitFound = true;
          array_push($admin_profit, $AdminProfit->total);
          break;
        }
      }
      if ($adminProfitFound == false) {
        array_push($admin_profit, 0);
      }
    }
    $information['months'] = $months;
    $information['incomes'] = $incomes;
    $information['taxs'] = $taxs;
    $information['admin_profit'] = $admin_profit;


    return view('backend.admin.profit', $information);
  }


  //pwa
  public function pwa()
  {
    if (Auth::guard('admin')->check()) {
      return view('backend.pwa.index');
    } else {
      return redirect()->route('admin.login');
    }
  }

  //check_qrcode
  public function check_qrcode(Request $request)
  {

    if (str_contains($request->booking_id, '__')) {
      $ids = explode('__', $request->booking_id);
      $booking_id = $ids[0];
      $unique_id = $ids[1];
      $check = Booking::where([['booking_id', $booking_id]])->first();
      if ($check) {
        // check payment status completed or not 
        if ($check->paymentStatus == 'completed' || $check->paymentStatus == 'free') {
          if (!$check->hasIssuedTicketUniqueId($unique_id)) {
            return response()->json(['alert_type' => 'error', 'message' => 'Unverified', 'booking_id' => $request->booking_id]);
          }

          $scannedTicketArr = $check->scannedTicketIds();
          if (!in_array($unique_id, $scannedTicketArr, true)) {
            $scannedTicketArr[] = $unique_id;
            $check->scanned_tickets = json_encode($scannedTicketArr);
            $check->save();
            return response()->json(['alert_type' => 'success', 'message' => 'Verified', 'booking_id' => $request->booking_id]);
          } else {
            return response()->json(['alert_type' => 'error', 'message' => 'Already Scanned', 'booking_id' => $request->booking_id]);
          }
        } elseif ($check->paymentStatus == 'pending') {
          return response()->json(['alert_type' => 'error', 'message' => 'Payment incomplete', 'booking_id' => $request->booking_id]);
        } elseif ($check->paymentStatus == 'rejected') {
          return response()->json(['alert_type' => 'error', 'message' => 'Payment Rejected', 'booking_id' => $request->booking_id]);
        }
      } else {
        return response()->json(['alert_type' => 'error', 'message' => 'Unverified']);
      }
    } else {
      return response()->json(['alert_type' => 'error', 'message' => 'Unverified']);
    }
  }
}
