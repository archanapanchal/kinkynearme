<?php

namespace App\Yantrana\Components\User;

use App\Yantrana\Base\BaseEngine;
use Razorpay\Api\Api as RazorpayAPI;
use Exception;

/**
 * This MailService class for manage globally -
 * mail service in application.
 *---------------------------------------------------------------- */
class RazorpayEngine extends BaseEngine
{
    /**
     * Constructor.
     *
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        //check razorpay test mode is on
        if (getStoreSettings('use_test_razorpay')) {
            $razorpayKey = getStoreSettings('razorpay_testing_key');
            $razorpaySecret = getStoreSettings('razorpay_testing_secret_key');
        } else {
            $razorpayKey = getStoreSettings('razorpay_live_key');
            $razorpaySecret = getStoreSettings('razorpay_live_secret_key');
        }
        $this->razorpayAPI = new RazorpayAPI($razorpayKey, $razorpaySecret);
    }

    /**
     * This method use for capturing payment.
     *
     * @param string $paymentId
     *
     * @return paymentReceived
     *---------------------------------------------------------------- */
    public function capturePayment($paymentId)
    {
        try {
            // fetch a particular payment
            $payment  = $this->razorpayAPI->payment->fetch($paymentId);

            // Captures a payment
            $paymentReceived  = $this->razorpayAPI->payment->fetch($paymentId)->capture(array('amount' => $payment['amount']));

            return $this->engineReaction(1, [
                'transactionDetail' => $paymentReceived->toArray()
            ], __tr('Complete'));
        } catch (\Exception $e) {
            return $this->engineReaction(2, [
                'errorMessage' => 'Invalid Api Key'
            ], $e->getMessage());
        }
    }
}
