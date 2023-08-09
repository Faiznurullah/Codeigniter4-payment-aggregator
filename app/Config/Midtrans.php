<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use Midtrans\Notification;
use App\Models\Payments;

class Midtrans extends BaseConfig
{
    protected $notification;
    protected $order;
    protected $serverKey;
     
    public function __construct()
    {
        parent::__construct();

        $this->serverKey = 'SB-Mid-server-dN7YmIjuKPkW2Ur9vimeQl1_';
        $this->_handleNotification();
    }
    public function isSignatureKeyVerified()
    {
        return ($this->_createLocalSignatureKey() == $this->notification->signature_key);
    }
    public function isSuccess()
    {
        $statusCode = $this->notification->status_code;
        $transactionStatus = $this->notification->transaction_status;
        $fraudStatus = !empty($this->notification->fraud_status) ? ($this->notification->fraud_status == 'accept') : true;

        return ($statusCode == 200 && $fraudStatus && ($transactionStatus == 'capture' || $transactionStatus == 'settlement'));
    }
    public function isExpire()
    {
        return ($this->notification->transaction_status == 'expire');
    }

    public function isCancelled()
    {
        return ($this->notification->transaction_status == 'cancel');
    }

    public function getNotification()
    {
        return $this->notification;
    }

    public function getOrder()
    {
        return $this->order;
    }
    protected function _createLocalSignatureKey()
    {
        return hash('sha512',
            $this->notification->order_id . $this->notification->status_code .
            $this->notification->gross_amount . $this->serverKey);
    }
    protected function _handleNotification()
    {
        $notification = new Notification();

        $orderNumber = $notification->order_id;
        $order = Payments::where('order_id', $orderNumber)->first();

        $this->notification = $notification;
        $this->order = $order;
    }
}
