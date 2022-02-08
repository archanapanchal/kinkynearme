<?php
/**
* CreditWalletController.php - Controller file
*
* This file is part of the Credit Wallet User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Controllers;

use App\Yantrana\Base\BaseController;
use App\Yantrana\Components\User\CreditWalletEngine;
use App\Yantrana\Components\User\Requests\{PaypalTransactionRequest, PaymentProcessRequest};
// form Requests
use App\Yantrana\Support\CommonPostRequest;

class CreditWalletController extends BaseController
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
     * Manage Credit Wallet List.
     *
     * @return json object
     *---------------------------------------------------------------- */
    public function creditWalletView()
    {
        $processReaction = $this->creditWalletEngine->prepareCreditWalletUserData();

        return $this->loadPublicView('user.credit-wallet.credit-wallet', $processReaction['data']);
    }

    /**
     * Show user Transaction List.
     *
     *-----------------------------------------------------------------------*/
    public function getUserWalletTransactionList()
    {
        return $this->creditWalletEngine->prepareUserWalletTransactionList();
    }

    /**
     * Handle complete transaction request.
     *
     * @param PaypalTransactionRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function paypalTransactionComplete(PaypalTransactionRequest $request, $packageUid)
    {
        $processReaction = $this->creditWalletEngine->processPaypalTransaction($request->all(), $packageUid);

        //check reaction code equal to 1
        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
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
     * @param PaymentProcessRequest $request
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function stripeCallbackUrl(PaymentProcessRequest $request)
    {
        $processReaction = $this->creditWalletEngine->prepareStripeRetrieveData($request->all());

        //check reaction code is 1
        if ($processReaction['reaction_code'] == 1) {
            return redirect()->route('user.credit_wallet.read.view')->with([
                'success' => true,
                'message' => __tr('Payment successfully.'),
            ]);
        } else {
            return redirect()->route('user.credit_wallet.read.view')->with([
                'error' => true,
                'message' => __tr('Payment failed.'),
            ]);
        }
    }

    /**
     * Thanks page.
     *
     * @return json response
     *---------------------------------------------------------------- */
    public function stripeCancelCallback()
    {
        return redirect()->route('user.credit_wallet.read.view')->with([
            'error' => true,
            'message' => __tr('Payment failed.'),
        ]);
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

        return $this->responseAction(
            $this->processResponse($processReaction, [], [], true)
        );
    }
}
