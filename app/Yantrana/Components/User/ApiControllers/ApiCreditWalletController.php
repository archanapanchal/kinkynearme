<?php
/**
* CreditWalletController.php - Controller file
*
* This file is part of the Credit Wallet User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\ApiControllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\User\CreditWalletEngine;
use App\Yantrana\Components\User\Requests\{PaypalTransactionRequest, PaymentProcessRequest};
// form Requests
use App\Yantrana\Support\CommonPostRequest;
use Redirect;

class ApiCreditWalletController extends BaseController
{
    /**
     * @var  CreditWalletEngine $creditWalletEngine - CreditWallet Engine
     */
    protected $creditWalletEngine;

    /**
     * Constructor
     *
     * @param  CreditWalletEngine $creditWalletEngine - CreditWallet Engine
     *
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(CreditWalletEngine $creditWalletEngine)
    {
        $this->creditWalletEngine = $creditWalletEngine;
    }

    /**
     * Show user Transaction List.
     *
     *-----------------------------------------------------------------------*/
    public function getTransactionList()
    {
        return $this->creditWalletEngine->apiCreditWalletTransactionList();
    }

    /**
     * Handle complete transaction request.
     *
     * @param PaypalTransactionRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function getCreditWalletData()
    {
        $processReaction = $this->creditWalletEngine->prepareCreditWalletUserData();

        //check reaction code equal to 1
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Handle complete transaction request.
     *
     * @param PaypalTransactionRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function processApiPaypalCheckout(PaypalTransactionRequest $request, $packageUid)
    {

        $processReaction = $this->creditWalletEngine->processPaypalApiTransaction($request->all(), $packageUid);

        //check reaction code equal to 1
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Razorpay Checkout
     *
     * @param string $orderUid
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function razorpayCheckout(PaypalTransactionRequest $request)
    {
        $processReaction = $this->creditWalletEngine->processRazorpayCheckout($request->all());

        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Handle complete transaction request.
     *
     * @param PaymentProcessRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function paymentProcess(PaymentProcessRequest $request)
    {
        $processReaction = $this->creditWalletEngine->processPayment($request->all());

        //check reaction code equal to 1
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Handle complete transaction request.
     *
     * @param PaypalTransactionRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function createStripePaymentIntent(CommonPostRequest $request)
    {
        $processReaction = $this->creditWalletEngine->processCreateStripePaymentIntent($request->all());

        //check reaction code equal to 1
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Handle complete transaction request.
     *
     * @param PaypalTransactionRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function retrieveStripePaymentIntent(CommonPostRequest $request)
    {
        $processReaction = $this->creditWalletEngine->retrieveStripePaymentIntent($request->all());

        //check reaction code equal to 1
        return $this->processResponse($processReaction, [], [], true);
    }

    /**
     * Handle complete transaction request.
     *
     * @param PaypalTransactionRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function storeStripePayment(CommonPostRequest $request)
    {
        $processReaction = $this->creditWalletEngine->storeStripePaymentData($request['paymentIntentData'], $request['packageUid']);

        //check reaction code equal to 1
        return $this->processResponse($processReaction, [], [], true);
    }
}
