<?php

namespace App\Yantrana\Components\User;

use App\Yantrana\Base\BaseEngine;
use Stripe\Stripe;
use Stripe\Customer as StripeCustomer;
use Stripe\Charge as StripeCharge;
use Exception;

/**
 * This StripeEngine class for manage globally -
 * mail service in application.
 *---------------------------------------------------------------- */
class StripeEngine extends BaseEngine
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
        if (getStoreSettings('use_test_stripe')) {
            $stripeSecretKey = getStoreSettings('stripe_testing_secret_key');
            $stripePublishKey = getStoreSettings('stripe_testing_publishable_key');
        } else {
            $stripeSecretKey = getStoreSettings('stripe_live_secret_key');
            $stripePublishKey = getStoreSettings('stripe_live_publishable_key');
        }

        //set Stripe Api Secret Key in Stripe static method object
        Stripe::setApiKey($stripeSecretKey);
    }

    /**
     * @param  string $packageData - packageUid
     * @param  string -$stripeToken - Stripe Token

     * request to Stripe checkout
     *---------------------------------------------------------------- */
    public function processStripeRequest($request)
    {
        $successUrl = route('user.credit_wallet.write.stripe.callback_url') . '?session_id={CHECKOUT_SESSION_ID}' . '&packageUid=' . $request['packageUid'];
        $cancelUrl = route('user.credit_wallet.write.stripe.cancel_url') . '?&packageUid=' . $request['packageUid'];

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'name'         => $request['package_name'],
                    'amount'     => $this->calculateStripeAmount($request['amount']),
                    'currency'     => $request['currency'],
                    'quantity' => 1
                ]],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl
            ]);

            //success response with session data
            return $this->engineReaction(1, $session);
        } catch (Exception $e) {
            //failure response with message
            return $this->engineReaction(2, [
                'errorMessage' => $e->getMessage()
            ], __tr('Failed'));
        }
    }

    /**
     * This method use for retrieve stripe payment.
     *
     * @param string $sessionId
     *
     *---------------------------------------------------------------- */
    public function retrieveStripeData($sessionId)
    {
        try {
            //retrieve stripe session data
            $sessionData = \Stripe\Checkout\Session::retrieve($sessionId);

            //if session data is empty 
            if (__isEmpty($sessionData)) {
                //failure response with message
                return $this->engineReaction(2, null, __tr('Session data does not exist.'));
            }

            //fetch payment intent data
            $paymentIntentData = \Stripe\PaymentIntent::retrieve($sessionData->payment_intent);

            //Success response with message
            return $this->engineReaction(1, [
                'paymentData' => json_decode(json_encode($paymentIntentData), true)
            ], __tr('Success'));
        } catch (\Stripe\Error\InvalidRequest $err) {
            //set error message if payment failed
            $errorMessage['errorMessage'] = $err->getMessage();

            //failure response with message
            return $this->engineReaction(2, [
                'errorMessage' => (array) $errorMessage
            ], __tr('Failed Payment'));
        } catch (\Stripe\Error\Card $err) {
            //set error message if payment failed
            $errorMessage['errorMessage'] = $err->getMessage();

            //failure response with message
            return $this->engineReaction(2, [
                'errorMessage' => (array) $errorMessage
            ], __tr('Failed Payment'));
        }
    }

    /**
     * This method use for create stripe payment intent data.
     *
     * @param string $sessionId
     *
     *---------------------------------------------------------------- */
    public function createPaymentIntent($requestData, $paymentMethodId)
    {
        try {
            // Create new PaymentIntent with a PaymentMethod ID from the client.
            $intent = \Stripe\PaymentIntent::create([
                "amount" => $this->calculateStripeAmount($requestData['price']),
                "currency" => getStoreSettings('currency'),
                "payment_method" => $paymentMethodId,
                "confirmation_method" => "manual",
                "confirm" => true,
                // If a mobile client passes `useStripeSdk`, set `use_stripe_sdk=true`
                // to take advantage of new authentication features in mobile SDKs
                "use_stripe_sdk" =>  true,
                "description" => 'Package Name - ' . $requestData['title']
            ]);

            //success response
            return $this->generateStripeApiResponse($intent);
        } catch (Exception $e) {
            //failure response with message
            return $this->engineReaction(2, [
                'errorMessage' => 'Failed'
            ], $e->getMessage());
        }
    }

    /**
     * This method use for create stripe payment intent data.
     *
     * @param string $sessionId
     *
     *---------------------------------------------------------------- */
    public function retrievePaymentIntent($requestData, $paymentIntentId)
    {
        try {
            //retrieve payment intent data
            $intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            $intent->confirm();

            //success response
            return $this->generateStripeApiResponse($intent);
        } catch (Exception $e) {
            //failure response with message
            return $this->engineReaction(2, [
                'errorMessage' => 'Failed'
            ], $e->getMessage());
        }
    }

    /**
     * Calculate Stripe Amount
     *
     * @param number $amount - Stripe Amount
     *
     * request to Stripe checkout
     *---------------------------------------------------------------- */
    protected function generateStripeApiResponse($intent)
    {
        //on switch case get data
        switch ($intent->status) {
            case "requires_action":
            case "requires_source_action":
                // Card requires authentication
                return $this->engineReaction(1, [
                    'requiresAction' => true,
                    'paymentIntentId' => $intent->id,
                    'clientSecret' => $intent->client_secret
                ]);

            case "requires_payment_method":
            case "requires_source":
                // Card was not properly authenticated, suggest a new payment method
                return $this->engineReaction(2, [
                    'error' => "Failed"
                ], __tr('Your card was denied, please provide a new payment method'));

            case "succeeded":
                // Payment is complete, authentication not required
                // To cancel the payment after capture you will need to issue a Refund (https://stripe.com/docs/api/refunds)
                //success response with session data
                return $this->engineReaction(1, ['clientSecret' => $intent->client_secret]);
        }
    }

    /**
     * Calculate Stripe Amount
     *
     * @param number $amount - Stripe Amount
     *
     * request to Stripe checkout
     *---------------------------------------------------------------- */
    protected function calculateStripeAmount($amount)
    {
        return $amount * 100;
    }
}
