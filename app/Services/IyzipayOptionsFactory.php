<?php

namespace App\Services;

use App\Models\PaymentGateway\OnlineGateway;

class IyzipayOptionsFactory
{
  public static function make(): \Iyzipay\Options
  {
    $data = OnlineGateway::where('keyword', 'iyzico')->firstOrFail();
    $information = json_decode($data->information, true);

    $options = new \Iyzipay\Options();
    $options->setApiKey($information['api_key']);
    $options->setSecretKey($information['secret_key']);
    $options->setBaseUrl(
      $information['sandbox_status'] == 1
        ? 'https://sandbox-api.iyzipay.com'
        : 'https://api.iyzipay.com'
    );

    return $options;
  }
}
