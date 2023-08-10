<?php

namespace App\Controllers;

use Xendit\Xendit;
use App\Models\TransactionPayment;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }
    public function coba(){

        $mt_rand = mt_rand(1000, 99999999);
        $code = 'XenditQ'.$mt_rand;
        $amount = $this->request->getVar('amount');
        $phone = $this->request->getVar('phone');
        $email = $this->request->getVar('email');

        Xendit::setApiKey('xnd_development_UxDar5q8gLWZ0Xn8HbuFNKfO1azhuu0p43mBFpzB46w4xDnRCPXvJdOs6J3ERABH');
      
        $params = [ 
          'external_id' =>  $code,
          'amount' => $amount,
          'description' => 'Invoice Demo #123',
          'invoice_duration' => 86400,
          'customer' => [
              'given_names' => 'John',
              'surname' => 'Doe',
              'email' => $email,
              'mobile_number' => $phone,
              'addresses' => [
                  [
                      'city' => 'Jakarta Selatan',
                      'country' => 'Indonesia',
                      'postal_code' => '12345',
                      'state' => 'Daerah Khusus Ibukota Jakarta',
                      'street_line1' => 'Jalan Makan',
                      'street_line2' => 'Kecamatan Kebayoran Baru'
                  ]
              ]
          ],
          'customer_notification_preference' => [
              'invoice_created' => [
                  'whatsapp',
                  'email',
                  'viber'
              ],
              'invoice_reminder' => [
                  'whatsapp',
                  'email',
                  'viber'
              ],
              'invoice_paid' => [
                  'whatsapp',
                  'email',
                  'viber'
              ],
              'invoice_expired' => [
                  'whatsapp',
                  'email',
                  'viber'
              ]
          ],
          'success_redirect_url' => 'https://www.google.com',
          'failure_redirect_url' => 'https://www.google.com',
          'currency' => 'IDR',
          'items' => [
              [
                  'name' => 'items',
                  'quantity' => 1,
                  'price' => $amount,
                  'category' => 'items category',
                  'url' => 'https://rakitacademy.com/'
              ]
          ],
          'fees' => [
              [
                  'type' => 'ADMIN',
                  'value' => 2000
              ]
          ]
        ];
        helper('url');
        $createInvoice = \Xendit\Invoice::create($params);

        $ModelData = new TransactionPayment();
        $ModelData->insert([
            'external_id' => $code,
            'amount' => $amount,
            'fees' => 2000,
            'email' => $email,
            'mobile_number' => $phone,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'unpaid',
        ]);

        return redirect()->to('https://checkout-staging.xendit.co/v2/'. $createInvoice['id']);
     

    }
    public function webhook(){

        $xenditXCallbackToken = 'TOKEn';

// Bagian ini untuk mendapatkan Token callback dari permintaan header, 
// yang kemudian akan dibandingkan dengan token verifikasi callback Xendit
$reqHeaders = getallheaders();
$xIncomingCallbackTokenHeader = isset($reqHeaders['x-callback-token']) ? $reqHeaders['x-callback-token'] : $xenditXCallbackToken;

// Untuk memastikan permintaan datang dari Xendit
// Anda harus membandingkan token yang masuk sama dengan token verifikasi callback Anda
// Ini untuk memastikan permintaan datang dari Xendit dan bukan dari pihak ketiga lainnya.
if($xIncomingCallbackTokenHeader === $xenditXCallbackToken){
  // Permintaan masuk diverifikasi berasal dari Xendit
    
  // Baris ini untuk mendapatkan semua input pesan dalam format JSON teks mentah
  $rawRequestInput = file_get_contents("php://input");
  // Baris ini melakukan format input mentah menjadi array asosiatif
  $arrRequestInput = json_decode($rawRequestInput, true);
  print_r($arrRequestInput);

  error_log('Callback received from Xendit: ' . print_r($arrRequestInput, true));
  
  $_id = $arrRequestInput['id'];
  $_externalId = $arrRequestInput['external_id'];
  $_userId = $arrRequestInput['user_id'];
  $_status = $arrRequestInput['status'];
  $_paidAmount = $arrRequestInput['paid_amount'];
  $_paidAt = $arrRequestInput['paid_at'];
  $_paymentChannel = $arrRequestInput['payment_channel'];
  $_paymentDestination = $arrRequestInput['payment_destination'];
  
  $DataModel = new TransactionPayment();
  $payment = $DataModel->getByOrderId($_externalId);
  $data = [
      'status' => $_status,
      'updated_at' => $_paidAt,
  ];

  $DataModel->update($payment['id'], $data);
  

  // Berikan respon ke Xendit bahwa callback sudah diterima
  http_response_code(200);
  
   
}else{
  // Permintaan bukan dari Xendit, tolak dan buang pesan dengan HTTP status 403
  http_response_code(403);
}


    }
}
