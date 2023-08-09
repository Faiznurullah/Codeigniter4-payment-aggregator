<?php

namespace App\Controllers;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Exception;
use App\Models\Payments;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Config\Midtrans;


class Home extends BaseController
{

    protected $helpers = [];

    public function index(): string
    {
        return view('welcome_message');
    }
    public function payment(){
        
        Config::$serverKey = 'SB-Mid-server-dN7YmIjuKPkW2Ur9vimeQl1_';
        Config::$isProduction = false;
        Config::$is3ds = false;
        
        $mt_rand = mt_rand(1000, 99999999);
        $code = 'XenditQ'.$mt_rand;
        $amount = $this->request->getVar('amount');
        $phone = $this->request->getVar('phone');
        $email = $this->request->getVar('email');
        
        $params = array(
            'transaction_details' => array(
                'order_id' => $code,
                'gross_amount' => $amount,
            ),
            'customer_details' => array(
                'email' => $email,
                'phone' => $phone,
            ),
        );
        
        $ModelData = new Payments();
        $ModelData->insert([
            'order_id' => $code,
            'amount' => $amount,
            'fees' => 2000,
            'email' => $email,
            'mobile_number' => $phone,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'unpaid',
        ]);
        
        $transaction = Snap::createTransaction($params);
        
        $paymentUrl = $transaction->redirect_url;
        
        // Redirect ke halaman pembayaran Midtrans
        return redirect()->to($paymentUrl);
        
        
    }
    public function callbacks(){


        $notification = new Notification;
        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;

        $paymentModel = new Payments();
        $payment = $paymentModel->getByOrderId($orderId);

        if ($payment) {
            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    $paymentModel->update($payment['id'], ['status' => 'paid']);
                    break;
                case 'expire':
                    $paymentModel->update($payment['id'], ['status' => 'expired']);
                    break;
                case 'deny':
                    $paymentModel->update($payment['id'], ['status' => 'failed']);
                    break;
                case 'cancel':
                    $paymentModel->update($payment['id'], ['status' => 'failed']);
                    break;
                default:
                    // Handle other statuses
                    break;
            }
        }

        return $this->response->setStatusCode(200)->setBody('OK');
    
        
        
                                
                            }
                            
                        }
                        