<?php
/**
* CreditWalletEngine.php - Main component file
*
* This file is part of the Credit Wallet User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User;

use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\User\Repositories\{CreditWalletRepository, ManageUserRepository};
use App\Yantrana\Components\Configuration\Repositories\ConfigurationRepository;
use App\Yantrana\Components\CreditPackage\Repositories\CreditPackageRepository;
use App\Yantrana\Components\User\PaypalEngine;
use App\Yantrana\Components\User\StripeEngine;
use App\Yantrana\Components\User\RazorpayEngine;

class CreditWalletEngine extends BaseEngine
{
    /**
     * @var  CreditWalletRepository $creditWalletRepository - CreditWallet Repository
     */
    protected $creditWalletRepository;

    /**
     * @var ManageUserRepository - Manage User Repository
     */
    protected $manageUserRepository;

    /**
     * @var  ConfigurationRepository $configurationRepository - Configuration Repository
     */
    protected $configurationRepository;

    /**
     * @var PaypalEngine - Paypal Engine
     */
    protected $paypalEngine;

    /**
     * @var StripeEngine - Stripe Engine
     */
    protected $stripeEngine;

    /**
     * @var  CreditPackageRepository $creditPackageRepository - CreditPackage Repository
     */
    protected $creditPackageRepository;

    /**
     * @var RazorpayEngine - Razorpay Engine
     */
    protected $razorpayEngine;

    /**
     * Constructor
     *
     * @param  CreditWalletRepository $creditWalletRepository - CreditWallet Repository
     * @param  ManageUserRepository $manageUserRepository - Manage User Repository
     * @param  ConfigurationRepository $configurationRepository - Configuration Repository
     * @param PaypalEngine  	 $paypalEngine- Paypal Engine
     * @param StripeEngine  	 $stripeEngine- Stripe Engine
     * @param  CreditPackageRepository $creditPackageRepository - CreditPackage Repository
     * @param  RazorpayEngine $razorpayEngine - Razorpay Repository
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(
        CreditWalletRepository $creditWalletRepository,
        ManageUserRepository $manageUserRepository,
        ConfigurationRepository $configurationRepository,
        PaypalEngine $paypalEngine,
        StripeEngine $stripeEngine,
        CreditPackageRepository $creditPackageRepository,
        RazorpayEngine $razorpayEngine
    ) {
        $this->creditWalletRepository     = $creditWalletRepository;
        $this->manageUserRepository     = $manageUserRepository;
        $this->configurationRepository     = $configurationRepository;
        $this->paypalEngine             = $paypalEngine;
        $this->stripeEngine             = $stripeEngine;
        $this->creditPackageRepository     = $creditPackageRepository;
        $this->razorpayEngine             = $razorpayEngine;
    }

    /**
     * Prepare Credit Wallet User Data.
     *
     *
     *---------------------------------------------------------------- */
    public function prepareCreditWalletUserData()
    {
        //get credit package data
        $packageCollection = $this->creditPackageRepository->fetchAllActiveCreditPackage();

        $creditPackages = [];
        // check if user collection exists
        if (!__isEmpty($packageCollection)) {
            foreach ($packageCollection as $key => $package) {
                $packageImageUrl = '';
                $packageImageFolderPath = getPathByKey('package_image', ['{_uid}' => $package->_uid]);
                $packageImageUrl = getMediaUrl($packageImageFolderPath, $package['image']);
                $creditPackages[] = [
                    '_id'                => $package['_id'],
                    '_uid'                => $package['_uid'],
                    'package_name'       => $package['title'],
                    'credit'             => $package['credits'],
                    'price'             => intval($package['price']),
                    'packageImageUrl'    => $packageImageUrl
                ];
            }
        }

        return $this->engineReaction(1, [
            'creditWalletData' => [
                'creditPackages'    => $creditPackages
            ],
            'paymentData' => [
                'currencySymbol'                => getStoreSettings('currency_symbol'),
                'currency'                      => getStoreSettings('currency'),
                'enablePaypalCheckout'          => getStoreSettings('enable_paypal'),
                'useTestPaypalCheckout'         => getStoreSettings('use_test_paypal_checkout'),
                'paypalTestingClientId'         => getStoreSettings('paypal_checkout_testing_client_id'),
                'paypalLiveClientId'            => getStoreSettings('paypal_checkout_live_client_id'),
                'userName'                      => getUserAuthInfo('profile.full_name'),
                'userEmail'                     => getUserAuthInfo('profile.email'),
                'enableRazorpay'                => getStoreSettings('enable_razorpay'),
                'useTestRazorpay'               => getStoreSettings('use_test_razorpay'),
                'razorpayTestKey'               => getStoreSettings('razorpay_testing_key'),
                'razorpayLiveKey'               => getStoreSettings('razorpay_live_key'),
                'enableStripe'                  => getStoreSettings('enable_stripe'),
                'useTestStripe'                 => getStoreSettings('use_test_stripe'),
                'stripeTestPublishableKey'      => getStoreSettings('stripe_testing_publishable_key'),
                'stripeLivePublishableKey'      => getStoreSettings('stripe_live_publishable_key')
            ]
        ]);
    }

    /**
     * get user transaction list data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function prepareUserWalletTransactionList()
    {
        $transactionCollection = $this->creditWalletRepository->fetchUserWalletTransactionList();

        $requireColumns = [
            '_id',
            '_uid',
            'created_at' => function ($key) {
                return formatDate($key['created_at']);
            },
            'credits',
            'credit_type',
            'transactionType' => function ($key) {
                $type = null;
                if (!__isEmpty($key['get_user_financial_transaction'])) {
                    $type = 1;
                } else if (!__isEmpty($key['get_user_gift_transaction'])) {
                    $type = 2;
                } else if (!__isEmpty($key['get_user_sticker_transaction'])) {
                    $type = 3;
                } else if (!__isEmpty($key['get_user_boost_transaction'])) {
                    $type = 4;
                } else if (!__isEmpty($key['get_user_subscription_transaction'])) {
                    $type = 5;
                } else if ($key['credit_type'] == 1) {
                    $type = 6;
                }
                return $type;
            },
            'formattedTransactionType' => function ($key) {
                $type = null;
                if (!__isEmpty($key['get_user_financial_transaction'])) {
                    $type = 1;
                } else if (!__isEmpty($key['get_user_gift_transaction'])) {
                    $type = 2;
                } else if (!__isEmpty($key['get_user_sticker_transaction'])) {
                    $type = 3;
                } else if (!__isEmpty($key['get_user_boost_transaction'])) {
                    $type = 4;
                } else if (!__isEmpty($key['get_user_subscription_transaction'])) {
                    $type = 5;
                } else if ($key['credit_type'] == 1) {
                    $type = 6;
                }
                return isset($type) ? configItem('user_transaction_type', $type) : null;
            },
            'financialTransactionDetail' => function ($key) {
                $financialTransaction = [];
                if (!__isEmpty($key['get_user_financial_transaction'])) {
                    $transactionData = $key['get_user_financial_transaction'];
                    $financialTransaction = [
                        '_id'             => $transactionData['_id'],
                        '_uid'             => $transactionData['_uid'],
                        'status'         => configItem('payments.status_codes', $transactionData['status']),
                        'amount'         => priceFormat($transactionData['amount'], true, false),
                        'created_at'     => formatDate($transactionData['created_at']),
                        'currency_code' => $transactionData['currency_code'],
                        'payment_mode'     => configItem('payments.payment_checkout_modes', $transactionData['is_test']),
                        'method'        => $transactionData['method'],
                    ];
                }
                return $financialTransaction;
            }
        ];

        return $this->dataTableResponse($transactionCollection, $requireColumns);
    }

    /**
     * get api user transaction list data.
     *
     *
     * @return object
     *---------------------------------------------------------------- */
    public function apiCreditWalletTransactionList()
    {
        $transactionCollection = $this->creditWalletRepository->fetchApiUserWalletTransactionList();


        $requireColumns = [
            '_id',
            '_uid',
            'created_at' => function ($key) {
                return formatDate($key['created_at']);
            },
            'credits',
            'credit_type',
            'transactionType' => function ($key) {
                $type = null;
                if (!__isEmpty($key['get_user_financial_transaction'])) {
                    $type = 1;
                } else if (!__isEmpty($key['get_user_gift_transaction'])) {
                    $type = 2;
                } else if (!__isEmpty($key['get_user_sticker_transaction'])) {
                    $type = 3;
                } else if (!__isEmpty($key['get_user_boost_transaction'])) {
                    $type = 4;
                } else if (!__isEmpty($key['get_user_subscription_transaction'])) {
                    $type = 5;
                } else if ($key['credit_type'] == 1) {
                    $type = 6;
                }
                return $type;
            },
            'formattedTransactionType' => function ($key) {
                $type = null;
                if (!__isEmpty($key['get_user_financial_transaction'])) {
                    $type = 1;
                } else if (!__isEmpty($key['get_user_gift_transaction'])) {
                    $type = 2;
                } else if (!__isEmpty($key['get_user_sticker_transaction'])) {
                    $type = 3;
                } else if (!__isEmpty($key['get_user_boost_transaction'])) {
                    $type = 4;
                } else if (!__isEmpty($key['get_user_subscription_transaction'])) {
                    $type = 5;
                } else if ($key['credit_type'] == 1) {
                    $type = 6;
                }
                return isset($type) ? configItem('user_transaction_type', $type) : null;
            },
            'financialTransactionDetail' => function ($key) {
                $financialTransaction = [];
                if (!__isEmpty($key['get_user_financial_transaction'])) {
                    $transactionData = $key['get_user_financial_transaction'];
                    $financialTransaction = [
                        '_id'           => $transactionData['_id'],
                        '_uid'          => $transactionData['_uid'],
                        'status'        => configItem('payments.status_codes', $transactionData['status']),
                        'amount'        => priceFormat($transactionData['amount'], true, false),
                        'created_at'    => formatDate($transactionData['created_at']),
                        'currency_code' => $transactionData['currency_code'],
                        'payment_mode'  => configItem('payments.payment_checkout_modes', $transactionData['is_test']),
                        'method'        => $transactionData['method'],
                    ];
                }
                return $financialTransaction;
            }
        ];

        return $this->customTableResponse($transactionCollection, $requireColumns);
    }

    /**
     * Process paypal complete transaction.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function processPaypalTransaction($inputData, $packageUid)
    {
        // process card charge
        $paypalPaymentDetail = $this->paypalEngine->getOrder($inputData['id']);

        //check reaction code is 1 or not
        if ($paypalPaymentDetail['reaction_code'] == 1) {
            $paypalResponse = $paypalPaymentDetail['data']['transactionResponse'];

            //check transaction status is completed or not
            if ($paypalResponse['status'] == "COMPLETED") {
                //store transaction data
                if ($this->storePaymentData($paypalResponse, $packageUid, 'paypalPayment')) {
                    return $this->engineReaction(1, null, __tr('Payment Complete'));
                }
            } else {
                //payment failed response
                return $this->engineReaction(2, null, __tr('Payment Failed'));
            }
        } else {
            //error response
            return $this->engineReaction(2, [
                'errorMessage' => 'Something went wrong, please contact system administrator'
            ], __tr('Payment Failed'));
        }
    }

    /**
     * Process paypal complete transaction.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function processPaypalApiTransaction($inputData, $packageUid)
    {
        // process card charge
        $paypalPaymentData = $this->paypalEngine->ApiCapturePaypalTransaction($inputData['id']);

        //check reaction code is 1 or not
        if ($paypalPaymentData['reaction_code'] == 1) {
            $paymentStatus = array_get($paypalPaymentData, 'data.transactionDetail.payer.status');
            $state = $paypalPaymentData['data']['transactionDetail']['state'];
            $paypalResponse = $paypalPaymentData['data']['transactionDetail'];

            //check transaction status is completed or not
            if ($paymentStatus == 'VERIFIED' and $state == 'approved') {
                //store transaction data
                if ($this->storePaymentData($paypalResponse, $packageUid, 'apiPaypalPayment')) {
                    return $this->engineReaction(1, null, __tr('Payment Complete'));
                }
            } else {
                //payment failed response
                return $this->engineReaction(2, null, __tr('Payment Failed'));
            }
        } else {
            //error response
            return $this->engineReaction(2, [
                'errorMessage' => 'Something went wrong, please contact system administrator'
            ], __tr('Payment Failed'));
        }
    }

    /**
     * Process Payment request
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function processPayment($inputData)
    {
        $paymentMethod = $inputData['select_payment_method'];
        $packageUid = $inputData['select_package'];

        //get package collection
        $packageCollection = $this->creditPackageRepository->fetch($packageUid);

        //if it is empty then throw error 
        if (__isEmpty($packageCollection)) {
            //success function
            return $this->engineReaction(2, null, __tr('Package does not exist.'));
        }

        //check payment method and package data exists
        if ($paymentMethod == 'stripe') {
            $stripeRequestData = [
                'packageUid'     => $packageUid,
                'package_name'     => $packageCollection['title'],
                'amount'        => $packageCollection['price'],
                'currency'        => getStoreSettings('currency'),
            ];
            //check is mobile app request
            if (isMobileAppRequest()) {
                $stripeRequestData['redirectAppUrl'] = base64_encode($inputData['redirectAppUrl']);
            }

            //get stripe session ata
            $stripeSessionData = $this->stripeEngine->processStripeRequest($stripeRequestData);

            //if reaction code is 1 then success response
            if ($stripeSessionData['reaction_code'] == 1) {
                return $this->engineReaction(1, [
                    'stripeSessionData' => $stripeSessionData['data']
                ], __tr('Success'));
            } else {
                //stripe failure response
                return $this->engineReaction(2, [
                    'errorMessage' => $stripeSessionData['data']['errorMessage']
                ], __tr('Failed'));
            }
        }

        //failure response
        return $this->engineReaction(2, null, __tr('Something went wrong, please contact to system administrator'));
    }

    /**
     * Process retrieve stripe payment data
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function prepareStripeRetrieveData($inputData)
    {
        //get stripe payment ata
        $stripePaymentData = $this->stripeEngine->retrieveStripeData($inputData['session_id']);

        //check reaction code is 1
        if ($stripePaymentData['reaction_code'] == 1) {
            $stripeData = $stripePaymentData['data']['paymentData'];
            //store transaction data
            if ($this->storeStripePaymentData($stripeData, $inputData['packageUid'])) {
                return $this->engineReaction(1, null, __tr('Payment Complete'));
            } else {
                //payment failed response
                return $this->engineReaction(2, null, __tr('Payment Failed'));
            }
        }
        //failure response
        return $this->engineReaction(2, null, __tr('Payment failed.'));
    }

    /**
     * Process paypal complete transaction.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function storeStripePaymentData($inputData, $packageUid)
    {
        //get package collection
        $packageCollection = $this->creditPackageRepository->fetch($packageUid);

        //if it is empty then throw error 
        if (__isEmpty($packageCollection)) {
            //success function
            return $this->engineReaction(2, null, __tr('Package does not exist.'));
        }

        if (!__isEmpty($inputData)) {
            $isStripeTestMode = 1;
            if (!getStoreSettings('use_test_stripe')) {
                $isStripeTestMode = 2;
            }
            //collect store data
            $storeData = [
                'status'         => 2,
                'amount'         => $inputData['amount'] / 100,
                'users__id'     => getUserID(),
                'method'         => configItem('payments.payment_methods', 2),
                'currency_code' => getStoreSettings('currency'),
                'is_test'         => $isStripeTestMode,
                '__data' => [
                    'rawPaymentData' => json_encode($inputData),
                    'packageName'    => $packageCollection['title']
                ]
            ];

            //store transaction process
            if ($this->creditWalletRepository->storeTransaction($storeData, $packageCollection)) {
                //success function
                return $this->engineReaction(1, null, __tr('Transaction store successfully.'));
            }
        }
        //error response
        return $this->engineReaction(2, null, __tr('Transaction not stored.'));
    }

    /**
     * Process paypal complete transaction.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function processRazorpayCheckout($inputData)
    {
        // process card charge
        $razorpayChargeRequest = $this->razorpayEngine->capturePayment($inputData['razorpayPaymentId']);

        //check reaction code is 1 or not
        if ($razorpayChargeRequest['reaction_code'] == 1) {
            $razorpayResponse = $razorpayChargeRequest['data']['transactionDetail'];

            //check transaction status is completed or not
            if ($razorpayResponse['captured'] === true) {
                //store transaction data
                if ($this->storePaymentData($razorpayResponse, $inputData['packageUid'], 'razorpayPayment')) {
                    return $this->engineReaction(1, null, __tr('Payment Complete'));
                }
            } else {
                //payment failed response
                return $this->engineReaction(2, null, __tr('Payment Failed'));
            }
        } else {
            //error response
            return $this->engineReaction(2, [
                'errorMessage' => 'Something went wrong, please contact system administrator'
            ], __tr('Payment Failed'));
        }
    }

    /**
     * Process paypal complete transaction.
     *
     * @param array $inputData
     *---------------------------------------------------------------- */
    public function storePaymentData($inputData, $packageUid, $paymentMethod)
    {
        //get package collection
        $packageCollection = $this->creditPackageRepository->fetch($packageUid);

        //if it is empty then throw error 
        if (__isEmpty($packageCollection)) {
            //success function
            return $this->engineReaction(2, null, __tr('Package does not exist.'));
        }

        // check if user collection exists
        if (!__isEmpty($inputData)) {
            $isTestMode  = 1;
            $amount      = $packageCollection['price'];
            $currency      = getStoreSettings('currency');
            $paymentType = null;
            //collect paypal payment data
            if ($paymentMethod == 'paypalPayment') {
                $paymentType = configItem('payments.payment_methods', 1);
                //check is live mode
                if (!getStoreSettings('use_test_paypal_checkout')) {
                    $isTestMode = 2;
                }

                //collect razorpay payment data
            } else if ($paymentMethod == 'razorpayPayment') {
                $paymentType = configItem('payments.payment_methods', 3);
                //check is live mode
                if (!getStoreSettings('use_test_razorpay')) {
                    $isTestMode = 2;
                }
            } else if ($paymentMethod == 'apiPaypalPayment') {
                $paymentType = configItem('payments.payment_methods', 4);
                //check is live mode
                if (!getStoreSettings('use_test_paypal_checkout')) {
                    $isTestMode = 2;
                }
            }

            //collect store data
            $storeData = [
                'status'        => 2, //completed
                'amount'        => $amount,
                'users__id'     => getUserID(),
                'method'        => $paymentType,
                'currency_code' => $currency,
                'is_test'       => $isTestMode,
                '__data'        => [
                    'rawPaymentData' => json_encode($inputData),
                    'packageName'     => $packageCollection['title']
                ]
            ];

            //store transaction process
            if ($financialTransactionId = $this->creditWalletRepository->storeTransaction($storeData, $packageCollection)) {
                //fetch updated user total credits by helper function
                totalUserCredits();
                //success function
                return $this->engineReaction(1, null, __tr('Transaction store successfully.'));
            }
        }
        return $this->engineReaction(2, null, __tr('Transaction not stored.'));
    }


    /**
     * Prepare Credit Wallet Stripe Intent User Data.
     *
     *
     *---------------------------------------------------------------- */
    public function processCreateStripePaymentIntent($inputData)
    {
        //get package collection
        $packageCollection = $this->creditPackageRepository->fetch($inputData['packageUid']);

        //if it is empty then throw error 
        if (__isEmpty($packageCollection)) {
            //success function
            return $this->engineReaction(2, null, __tr('Package does not exist.'));
        }

        //get stripe payment intent data
        $stripePaymentIntentData = $this->stripeEngine->createPaymentIntent($packageCollection, $inputData['paymentMethodId']);

        if ($stripePaymentIntentData['reaction_code'] == 1) {
            return $this->engineReaction(1, $stripePaymentIntentData);
        }

        return $this->engineReaction(2, $stripePaymentIntentData);
    }


    /**
     * Prepare Credit Wallet Stripe Intent User Data.
     *
     *
     *---------------------------------------------------------------- */
    public function retrieveStripePaymentIntent($inputData)
    {
        //get package collection
        $packageCollection = $this->creditPackageRepository->fetch($inputData['packageUid']);

        //if it is empty then throw error 
        if (__isEmpty($packageCollection)) {
            //success function
            return $this->engineReaction(2, null, __tr('Package does not exist.'));
        }

        //get stripe payment intent data
        $retrievePaymentIntentData = $this->stripeEngine->retrievePaymentIntent($packageCollection, $inputData['paymentIntentId']);

        if ($retrievePaymentIntentData['reaction_code'] == 1) {
            return $this->engineReaction(1, $retrievePaymentIntentData);
        }

        return $this->engineReaction(2, $retrievePaymentIntentData);
    }
}
