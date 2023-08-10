<?php

namespace App\Controllers;

use iPaymu\iPaymu;
use App\Models\Payments;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }
    public function coba(){
        
        $mt_rand = mt_rand(1000, 99999999);
        $code = 'Ipaymu-'.$mt_rand;
        $amount = $this->request->getVar('amount');
        $phone = $this->request->getVar('phone');
        $email = $this->request->getVar('email');
        
        
        $apiKey = 'SANDBOX2BDE451B-7DE0-40C0-9876-848F3DFC904BXXXX'; // your api key
        $va = '0000007878776654'; // your va
        $production = false; // set false to sandbox mode
        
        $iPaymu = new iPaymu($apiKey, $va, $production);
        
        // set callback url
        $iPaymu->setURL([
            'ureturn' => 'https://your-website/thankyou_page',
            'unotify' => 'https://c4b8-139-228-49-49.ngrok-free.app/callbacks',
                'ucancel' => 'https://your-website/cancel_page',
            ]);
            
            // set buyer name
            $iPaymu->setBuyer([
                'name' => $email,
                'phone' => $phone,
                'email' => $email,
            ]);
            
            // set your reference id (optional)
            $iPaymu->setReferenceId($code);
            
            // set your expiredPayment
            $iPaymu->setExpired(24, 'hours'); // 24 hours
            
            // set payment method
            // check https://ipaymu.com/api-collection for list payment method
            $iPaymu->setPaymentMethod('va');
            
            // check https://ipaymu.com/api-collection for list payment channel
            $iPaymu->setPaymentChannel('bca');
            
            // payment notes (optional)
            $iPaymu->setComments('Payment TRX01');
            
            $carts = [];
            $carts = $iPaymu->add(
                $code, // product id (string)
                'Jacket', // product name (string)
                $amount, // price (float)
                1, // quantity (int)
                'Size M', // description
                1, // product weight (int) (optional)
                1, // product length (int) (optional)
                1, // product weight (int) (optional)
                1 // product height (int) (optional)
            );
            
            
            $iPaymu->addCart($carts);
            
            $directData = [
                'amount' => $amount,
                'expired' => 24,
                'expiredType' => 'hours',
                'referenceId' => $code,
                'paymentMethod' => 'va', //va, cstore
                'paymentChannel' => 'bni', //bag, mandiri, cimb, bni, 
                
            ];
            
            $data =  $iPaymu->redirectpayment($directData);
            
            $ModelData = new Payments();
            $ModelData->insert([
                'external_id' => $code,
                'session_id' => $data['Data']['SessionID'],
                'amount' => $amount,
                'fees' => 2000,
                'email' => $email,
                'mobile_number' => $phone,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => 'unpaid',
            ]);
            
            //   dd($data);
            return redirect()->to($data['Data']['Url']);
            
        }
        
        public function callbacks(){

           $Modeldata = new Payments();
            
           $sessionID = $this->request->getVar('sid');

           $payment = $Modeldata->getSessionId($sessionID);
           $data = [
               'status' => 'paid',
               'updated_at' => date('Y-m-d H:i:s'),
           ];
         
           $Modeldata->update($payment['id'], $data);
            
           http_response_code(200);
            
        }
    }
    