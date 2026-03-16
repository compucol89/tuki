<?php

namespace App\Http\Controllers;

use App\Models\PaymentInvoice;
use Basel\MyFatoorah\MyFatoorah;
use Exception;
use Illuminate\Http\Request;

class MyFatoorahController extends Controller
{
  public $myfatoorah;

  public function __construct()
  {
    $this->myfatoorah = MyFatoorah::getInstance(true);
  }

  public function index()
  {
    try {
      $result = $this->myfatoorah->sendPayment(
        'Customer Name',
        300,
        [
          'CustomerMobile' => '56562123544',
          'CustomerReference' => '1323',
          'UserDefinedField' => '3241',
          'InvoiceItems' => [
            [
              'ItemName' => 'Order 123',
              'Quantity' => 1,
              'UnitPrice' => 300,
            ],
          ],
        ]
      );

      if ($result && $result['IsSuccess'] === true) {
        return redirect($result['Data']['InvoiceURL']);
      }
    } catch (Exception $e) {
      echo $e->getMessage();

      if (method_exists($e, 'getResponse') && $e->getResponse()) {
        echo $e->getResponse()->getBody()->getContents();
      }
    }
  }

  public function successCallback(Request $request)
  {
    if (array_key_exists('paymentId', $request->all())) {
      $result = $this->myfatoorah->getPaymentStatus('paymentId', $request->paymentId);

      if ($result && $result['IsSuccess'] === true && $result['Data']['InvoiceStatus'] === 'Paid') {
        $this->createInvoice($result['Data']);
        echo 'success payment';
      }
    }
  }

  public function failCallback(Request $request)
  {
    if (array_key_exists('paymentId', $request->all())) {
      $result = $this->myfatoorah->getPaymentStatus('paymentId', $request->paymentId);

      if ($result && $result['IsSuccess'] === true && $result['Data']['InvoiceStatus'] === 'Pending') {
        $error = end($result['Data']['InvoiceTransactions'])['Error'];
        echo 'Error => ' . $error;
      }
    }
  }

  public function createInvoice($request)
  {
    $paymentArray = array_merge($request, end($request['InvoiceTransactions']));
    $paymentArray['order_id'] = $paymentArray['CustomerReference'];
    $paymentArray['client_id'] = $paymentArray['UserDefinedField'];

    PaymentInvoice::create($paymentArray);
  }
}
