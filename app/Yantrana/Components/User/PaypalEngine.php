<?php

namespace App\Yantrana\Components\User;

use App\Yantrana\Base\BaseEngine;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPal\Api\Payment;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Exception;

/**
 * This PaypalEngine class for manage globally -
 * mail service in application.
 *---------------------------------------------------------------- */
class PaypalEngine extends BaseEngine
{
    /**
     * Constructor.
     *
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        /**
         * Set up and return PayPal PHP SDK environment with PayPal access credentials.
         * This sample uses SandboxEnvironment. In production, use LiveEnvironment.
         */
        if (getStoreSettings('use_test_paypal_checkout')) {
            $clientId = getStoreSettings('paypal_checkout_testing_client_id');
            $clientSecret = getStoreSettings('paypal_checkout_testing_secret_key');
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        } else {
            $clientId = getStoreSettings('paypal_checkout_live_client_id');
            $clientSecret = getStoreSettings('paypal_checkout_live_secret_key');
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        }

        $this->paypalAPI = new PayPalHttpClient($environment);

        /** @var \Paypal\Rest\ApiContext $apiContext */
        $this->apiContext = $this->getApiContext($clientId, $clientSecret);
    }

    /**
     * This method use for get payment details
     * 2. Set up your server to receive a call from the client
     * 3. Call PayPal to get the transaction details
     * @param string $paymentId
     * You can use this function to retrieve an order by passing order ID as an argument.
     * @return paymentReceived
     *---------------------------------------------------------------- */
    public function getOrder($paymentId)
    {
        //try if it is success else throw error
        try {
            //get capture order request
            $request = new OrdersGetRequest($paymentId);

            //execute request for payment response
            $response = $this->paypalAPI->execute($request);

            //success reaction
            return $this->engineReaction(1, [
                'transactionResponse' => json_decode(json_encode($response->result), true)
            ], __tr('Complete'));
        } catch (Exception $e) {
            //failure response with message
            return $this->engineReaction(2, [
                'errorMessage' => $e->getMessage()
            ], __tr('Failed'));
        }
    }

    /**

     * @param  string $order Data - Order ID
     * @param  string -$stripeToken - Stripe Token

     * request to Stripe checkout
     *---------------------------------------------------------------- */
    public function ApiCapturePaypalTransaction($paypalPaymentId)
    {
        //try payment successful
        try {
            // Call API with your client and get a response for your call
            // $response = $client->execute($request);
            $response = Payment::get($paypalPaymentId, $this->apiContext);

            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            return $this->engineReaction(1, [
                'transactionDetail' => $response->toArray(),
            ]);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            // echo $ex->statusCode;
            // echo $ex->getCode();
            // echo $ex->getData();
            // print_r($ex->getMessage());
            return $this->engineReaction(2, null, $ex->getData());
        }
    }


    /**
     * Helper method for getting an APIContext for all calls
     * @param string $clientId Client ID
     * @param string $clientSecret Client Secret
     * @return PayPal\Rest\ApiContext
     */
    function getApiContext($clientId, $clientSecret)
    {
        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );

        return $apiContext;
    }
}
