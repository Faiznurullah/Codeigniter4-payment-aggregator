<?php

namespace App\Controllers;

use App\Models\Payments;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }
    public function coba()
    {
        
        $mt_rand = mt_rand(1000, 99999999);
        $code = 'Tripay-'.$mt_rand;
        $amount = $this->request->getVar('amount');
        $phone = $this->request->getVar('phone');
        $email = $this->request->getVar('email');
        
        $apiKey       = 'DEV-K67E3D9pejWfcc0519TVLyurVFi2yZrRPaTi01d7X';
        $privateKey   = '4FpMT-Gslpy-t2HEk-vGs6t-sFoHqX';
        $merchantCode = 'T19994X';
        $merchantRef  = $code;
        $amount       = $amount;
        
        $data = [
            'method'         => 'BRIVA',
            'merchant_ref'   => $merchantRef,
            'amount'         => $amount,
            'customer_name'  => $email,
            'customer_email' => $email,
            'customer_phone' => $phone,
            'order_items'    => [
                [
                    'sku'         => 'FB-06',
                    'name'        => 'Test Tripay',
                    'price'       => $amount,
                    'quantity'    => 1,
                    'product_url' => 'https://google.com',
                    'image_url'   => 'https://google.com',
                    ] 
                ],
                'return_url'   => 'https://google.com',
                'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
                'signature'    => hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey)
            ];
            
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_FRESH_CONNECT  => true,
                CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/create',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
                CURLOPT_FAILONERROR    => false,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($data),
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
            ]);
            
            $response = curl_exec($curl);
            $error = curl_error($curl);
            
            curl_close($curl);
            
            echo empty($error) ? $response : $error;
            
            // dd($response);
            
            $data = json_decode($response, true);
            
            $ModelData = new Payments();
            $ModelData->insert([
                'merchant_id' => $code, 
                'amount' => $amount, 
                'email' => $email,
                'mobile_number' => $phone,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => 'unpaid',
            ]);
            
            return redirect()->to($data['data']['checkout_url']);
            
            
        }
        public function callbacks(){
            
            $privateKey = '4FpMT-Gslpy-t2HEk-vGs6t-sFoHq';
            
            $callbackSignature = $_SERVER['HTTP_X_CALLBACK_SIGNATURE'];
            $json = file_get_contents('php://input');
            $signature = hash_hmac('sha256', $json, $privateKey);
            
            if ($signature !== (string) $callbackSignature) {
                return json_encode([
                    'success' => false,
                    'message' => 'Invalid signature',
                ]);
            }
            
            if ('payment_status' !== (string) $_SERVER['HTTP_X_CALLBACK_EVENT']) {
                return json_encode([
                    'success' => false,
                    'message' => 'Unrecognized callback event, no action was taken',
                ]);
            }
            
            $data = json_decode($json);
            
            if (JSON_ERROR_NONE !== json_last_error()) {
                return json_encode([
                    'success' => false,
                    'message' => 'Invalid data sent by tripay',
                ]);
            }
            
            $invoiceId = $data->merchant_ref;
            $tripayReference = $data->reference;
            $status = strtoupper((string) $data->status);
            
            if ($data->is_closed_payment === 1) {
                $invoiceModel = new Payments(); // Replace with your actual model name
            
                $invoice = $invoiceModel->where('merchant_id', $invoiceId) 
                    ->where('status', 'unpaid')
                    ->first();
            
                if (! $invoice) {
                    return json_encode([
                        'success' => false,
                        'message' => 'No invoice found or already paid: ' . $invoiceId,
                    ]);
                }
            
                switch ($status) {
                    case 'PAID':
                        $invoiceModel->update($invoice['id'], ['status' => 'paid']);
                        break;
            
                    case 'EXPIRED':
                        $invoiceModel->update($invoice['id'], ['status' => 'expired']);
                        break;
            
                    case 'FAILED':
                        $invoiceModel->update($invoice['id'], ['status' => 'failed']);
                        break;
            
                    default:
                        return json_encode([
                            'success' => false,
                            'message' => 'Unrecognized payment status',
                        ]);
                }
            
                return json_encode(['success' => true]);
            }
            
            
        }
    }
    