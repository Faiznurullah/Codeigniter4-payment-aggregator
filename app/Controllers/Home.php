<?php

namespace App\Controllers;

use Xendit\Xendit;

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

        Xendit::setApiKey('API_KEY_SECRET');
      
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
                  'sms',
                  'email',
                  'viber'
              ],
              'invoice_reminder' => [
                  'whatsapp',
                  'sms',
                  'email',
                  'viber'
              ],
              'invoice_paid' => [
                  'whatsapp',
                  'sms',
                  'email',
                  'viber'
              ],
              'invoice_expired' => [
                  'whatsapp',
                  'sms',
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
                  'url' => 'https://google.com/'
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
        return redirect()->to('https://checkout-staging.xendit.co/v2/'. $createInvoice['id']);
     

    }
}
