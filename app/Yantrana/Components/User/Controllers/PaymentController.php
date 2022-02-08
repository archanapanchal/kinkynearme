<?php
/**
* UserController.php - Controller file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Controllers;

//require 'vendor/authorizenet/authorizenet/autoload.php';

require_once 'constants/SampleCodeConstants.php';
use App\Yantrana\Base\BaseController;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use academe\academe;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Yantrana\Components\User\Requests\{
    UserSignUpRequest,
    UserLoginRequest,
    UserUpdatePasswordRequest,
    UserForgotPasswordRequest,
    UserResetPasswordRequest,
    UserChangeEmailRequest,
    SendUserGiftRequest,
    ReportUserRequest,
    UserContactRequest
};
use Session;

use App\Yantrana\Components\User\Models\{User,Payment};

use App\Yantrana\Components\UserSetting\Repositories\UserSettingRepository;
use App\Yantrana\Components\User\Models\UserSubscription;
use App\Yantrana\Components\User\Repositories\UserRepository;
use App\Yantrana\Components\Plans\Repositories\ManagePlansRepository;
use App\Yantrana\Components\User\UserEngine;
use App\Yantrana\Support\CommonUnsecuredPostRequest;
use App\Yantrana\Components\Plans\ManagePlansEngine;
use Auth;
use \Illuminate\Support\Facades\DB;
use App\Yantrana\Support\Country\Repositories\CountryRepository;

class PaymentController extends BaseController
{
    public $gateway;

    protected $managePlansRepository;
    protected $userSettingRepository;
    protected $userEngine;
    protected $userRepository;
    protected $managePlanEngine;
    protected $countryRepository;
  
    public function __construct(ManagePlansRepository $managePlansRepository,UserSettingRepository $userSettingRepository,UserEngine $userEngine, ManagePlansEngine $managePlanEngine,UserRepository $userRepository,CountryRepository $countryRepository)
    {
        $this->gateway = Omnipay::create('AuthorizeNetApi_Api');
        $this->gateway->setAuthName(env('ANET_API_LOGIN_ID'));
        $this->gateway->setTransactionKey(env('ANET_TRANSACTION_KEY'));
        $this->gateway->setTestMode(true); //comment this line when move to 'live'
        $this->managePlansRepository  = $managePlansRepository;
        $this->userSettingRepository        = $userSettingRepository;
        $this->userEngine = $userEngine;
        $this->managePlanEngine         = $managePlanEngine;
        $this->countryRepository            = $countryRepository;
        $this->userRepository         = $userRepository;
    }
  
    public function index()
    {
        return view('payment');
    }

    public function charge(Request $request){


        $amount = $request->input('amount');
        $plan_id = $request->input('plan_id');
        $plan_name = $request->input('plan_name');
        $plan_type = $request->input('plan_type');
        $users_id = getUserID();
        //echo "<pre>"; print_r($request->all()); exit;
        //$userSignUpData = Session::get('userSignUpData');

        if (empty($users_id)) {
            if (Session::has('userSignUpData')) {
                $userSignUpData = Session::get('userSignUpData');
                $users_id = $userSignUpData['id'];
            }
        }
        
       /* echo $plan_type;
        echo "<pre>";print_r($users_id);exit();*/

        if ($plan_type == 1) {
            $plan_type = "Monthly";
        }else if($plan_type == 3){
             $plan_type = "7 day";
        } else {
            $plan_type = "Quarterly";
        }
        


            

        $userProfile = $this->userSettingRepository->fetchUserProfile($users_id);
        $UserProfiledata =  User::where('_id', $users_id)->first();
            


        
        $cityData = "";
        if (!empty($userProfile->city)) {
          
            $cityData = $this->userSettingRepository->fetchCity($userProfile->city);
        }

        $country_name = "";

        if (!empty($userProfile->countries__id)) {
            $country = $this->countryRepository->fetchById($userProfile->countries__id, ['name']);
            $country_name = $country->name;
        }

        $first_name = "";
        $last_name = "";
        $email = "";
        if (!empty($UserProfiledata)) {
            $first_name = $UserProfiledata->first_name;
            $last_name = $UserProfiledata->last_name;
            $email = $UserProfiledata->email;
        }

          /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
        $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
        
        // Set the transaction's refId
        $refId = 'ref' . time();
        $InvoiceNumber = time();
            

        // Create the payment object for a payment nonce
        $opaqueData = new AnetAPI\OpaqueDataType();
        $opaqueData->setDataDescriptor($request->input('dataDescriptor'));
        $opaqueData->setDataValue($request->input('dataValue'));


        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setOpaqueData($opaqueData);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($InvoiceNumber);
        $order->setDescription("KNM Media LLC - ".$plan_name." ".$plan_type);

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($first_name);
        $customerAddress->setLastName($last_name);
        //$customerAddress->setCompany("Souveniropolis");
        /*$customerAddress->setAddress("14 Main Street");
        $customerAddress->setCity("Pecan Springs");
        $customerAddress->setState("TX");
        $customerAddress->setZip("44628");*/
        $customerAddress->setCountry($country_name);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($users_id);
        $customerData->setEmail($email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        $merchantDefinedField1 = new AnetAPI\UserFieldType();
        $merchantDefinedField1->setName("customerLoyaltyNum");
        $merchantDefinedField1->setValue("1128836273");

        $merchantDefinedField2 = new AnetAPI\UserFieldType();
        $merchantDefinedField2->setName("favoriteColor");
        $merchantDefinedField2->setValue("blue");

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction"); 
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);
        $transactionRequestType->addToUserFields($merchantDefinedField2);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        

                //echo "<pre>";print_r($response);exit();

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
                
                $transaction_id = $tresponse->getTransId();



                $isPaymentExist = Payment::where('transaction_id', $transaction_id)->first();
                $users_payment_detail = Payment::where('user_id', $users_id)->get()->toArray();
                $usersubscription_detail = UserSubscription::where('users__id', $users_id)->get()->toArray();

                foreach ($usersubscription_detail as $key => $scription_detail) {
                    $plan_payment_status_update = UserSubscription::where('users__id', $scription_detail['users__id'])->update(['status' => 0]);
                }

                foreach ($users_payment_detail as $key => $value) {
                     
                    $plan_payment_status_update = Payment::where('user_id', $value['user_id'])->update(['plan_active_status' => 0]);

                }
               

                $subscribe_plan_started = date("Y-m-d h:i:s");
                $subscribe_plan_expiry = date("Y-m-d H:i:s", strtotime(" +3 months"));
                if ($plan_type == "Monthly") {
                    $subscribe_plan_expiry = date("Y-m-d H:i:s", strtotime(" +1 months"));
                }
                if ($plan_type == "7 day") {
                    $subscribe_plan_expiry = date("Y-m-d H:i:s", strtotime(" +7 day"));
                }
              
                $planCollection = $this->managePlansRepository->fetch($plan_id);
                $PlansInsertDetail = array('_uid' => $planCollection['_uid'], 'created_at' => $planCollection['created_at']->toDateString(), 'updated_at' => $planCollection['updated_at']->toDateString(), 'status' => $planCollection['status'], 'users__id' => $users_id,'expiry_at' => "", 'credit_wallet_transactions__id' => "", 'plan_id' => $plan_id);

                if($isPaymentExist)
                {
                    $payment = new Payment;
                    $payment->transaction_id = $transaction_id;
                    $payment->invoice_number = $InvoiceNumber;
                    $payment->amount = $amount;
                    $payment->user_id = $users_id;
                    $payment->paln_id_detail = $plan_id;
                    $payment->paln_title_detail = $plan_name;
                    $payment->plan_type_detail = '1';
                    $payment->plan_active_status = '1';
                    $payment->currency = 'USD';
                    $payment->payment_status = 'Captured';
                    $payment->save();

                    $subscription = new UserSubscription;
                    $subscription->_uid = $planCollection['_uid'];
                    $subscription->created_at = $subscribe_plan_started;
                    $subscription->updated_at = $planCollection['updated_at']->toDateString();
                    $subscription->status = $planCollection['status'];
                    $subscription->users__id = $users_id;
                    $subscription->expiry_at = $subscribe_plan_expiry;
                    $subscription->plan_id = $plan_id;
                    $subscription->save();
                       
                        
                }
                if(!$isPaymentExist)
                {
                    $payment = new Payment;
                    $payment->transaction_id = $transaction_id;
                    $payment->invoice_number = $InvoiceNumber;
                    $payment->amount = $amount;
                    $payment->user_id = $users_id;
                    $payment->paln_id_detail = $plan_id;
                    $payment->paln_title_detail = $plan_name;
                    $payment->plan_type_detail = '1';
                    $payment->plan_active_status = '1';
                    $payment->currency = 'USD';
                    $payment->payment_status = 'Captured';
                    $payment->save();

                    $subscription = new UserSubscription;
                    $subscription->_uid = $planCollection['_uid'];
                    $subscription->created_at = $subscribe_plan_started;
                    $subscription->updated_at = $planCollection['updated_at']->toDateString();
                    $subscription->status = $planCollection['status'];
                    $subscription->users__id = $users_id;
                    $subscription->expiry_at = $subscribe_plan_expiry;
                    $subscription->plan_id = $plan_id;
                    $subscription->save();
                     
                        
                }

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    return redirect()->route('user.payment_success');
                    //return $this->loadProfileView('payment.payment_success', $processReaction['data']);
                    /*echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
                    echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
                    echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
                    echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
                    echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";*/
                } else {
                    return redirect()->route('user.payment_fail');
                    /*echo "Transaction Failed \n";
                    if ($tresponse->getErrors() != null) {
                        echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                        echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    }*/
                }
                // Or, print errors if the API request wasn't successful
            } else {
                    //return $this->loadProfileView('payment.payment_faild', $processReaction['data']);
                return redirect()->route('user.payment_fail');

                /*echo "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();
            
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                    echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                } else {
                    echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                    echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                }*/
            }      
        } else {
                    //return $this->loadProfileView('payment.payment_faild', $processReaction['data']);

            return redirect()->route('user.payment_fail');
            //echo  "No response returned \n";
        }

        return $response;
    }



    public function payment_success(Request $request){

                $users_id = getUserID();

                $user_detail = DB::table('users')->where('_id',$users_id)->get()->toArray();
                    //echo "<pre>";print_r($user_detail[0]['username']);exit();

                $processReaction = $this->userEngine->prepareUserProfile($user_detail[0]->username);
                $UserSubscription_plan = $this->userEngine->fetchUserSubscription();
                $blockUserCollection = $this->userRepository->fetchAllBlockUser(true);

                $blockUser_array = array();
                foreach ($blockUserCollection as $key => $value) {
                    $blockUser_array[] = array('to_users__id' => $value['to_users__id'],'userFullName' => $value['userFullName'],'profile_picture' => $value['profile_picture'],'countryName' =>$value['countryName']);
                }
                $PlanByType = $this->userEngine->fetchPlanByType();

                
                if (empty($UserSubscription_plan)) {
                    $fetchPlan['data'] = "";
                }else{
                    $fetchPlan = $this->userEngine->fetchPlan($UserSubscription_plan['plan_id']);
                }
                    
                   // echo "<pre>";print_r();exit();
                $processReaction['data']['planbytype'] = "";
                if (!empty($PlanByType['data'])) {
                    $processReaction['data']['planbytype'] = $PlanByType['data']['plans'];
                }

                   // echo "<pre>";print_r($UserSubscription_plan);exit();

                $explode_expiry_at = explode(" ", $UserSubscription_plan['expiry_at']);

                $processReaction['data']['plan_deatail'] = "";
                if (!empty($fetchPlan['data'])) {
                    $processReaction['data']['plan_deatail'] = array('title' => $fetchPlan['data']['planData']['title'], 'price' => $fetchPlan['data']['planData']['price'] , 'plan_type' => $fetchPlan['data']['planData']['plan_type'],'status' => $fetchPlan['data']['planData']['status'],'created_at' => $UserSubscription_plan['created_at']->toDateString(),'expiry_at' => $explode_expiry_at[0],'renewal_sts' => $UserSubscription_plan['renewal_sts']);
                }else{
                    $processReaction['data']['plan_deatail'] = "";
                }

                $processReaction['data']['block_user_collection'] = "";
                if (count($blockUserCollection) != 0) {
                    $processReaction['data']['block_user_collection'] = $blockUser_array;
                }

                // check if record does not exists
                if ($processReaction['reaction_code'] == 18) {
                    return redirect()->route('user.profile_view', ['username' => getUserAuthInfo('profile.username')]);
                }

                    //echo "<pre>";print_r($processReaction['data']);exit();

                $processReaction['data']['is_profile_page'] = true;
                $processReaction['data']['active_tab'] = 6;


                return $this->loadProfileView('payment.payment_success', $processReaction['data']);
    }

    public function payment_fail(Request $request){
                $users_id = getUserID();
                $user_detail = DB::table('users')->where('_id',$users_id)->get()->toArray();
                    //echo "<pre>";print_r($user_detail[0]['username']);exit();

                $processReaction = $this->userEngine->prepareUserProfile($user_detail[0]->username);
                $UserSubscription_plan = $this->userEngine->fetchUserSubscription();
                $blockUserCollection = $this->userRepository->fetchAllBlockUser(true);

                $blockUser_array = array();
                foreach ($blockUserCollection as $key => $value) {
                    $blockUser_array[] = array('to_users__id' => $value['to_users__id'],'userFullName' => $value['userFullName'],'profile_picture' => $value['profile_picture'],'countryName' =>$value['countryName']);
                }
                $PlanByType = $this->userEngine->fetchPlanByType();

                
                if (empty($UserSubscription_plan)) {
                    $fetchPlan['data'] = "";
                }else{
                    $fetchPlan = $this->userEngine->fetchPlan($UserSubscription_plan['plan_id']);
                }
                    
                   // echo "<pre>";print_r();exit();
                $processReaction['data']['planbytype'] = "";
                if (!empty($PlanByType['data'])) {
                    $processReaction['data']['planbytype'] = $PlanByType['data']['plans'];
                }

                $explode_expiry_at = "";

                $processReaction['data']['plan_deatail'] = "";
                if (!empty($fetchPlan['data'])) {
                    $processReaction['data']['plan_deatail'] = array('title' => $fetchPlan['data']['planData']['title'], 'price' => $fetchPlan['data']['planData']['price'] , 'plan_type' => $fetchPlan['data']['planData']['plan_type'],'status' => $fetchPlan['data']['planData']['status'],'created_at' => $UserSubscription_plan['created_at']->toDateString(),'expiry_at' => $explode_expiry_at,'renewal_sts' => $UserSubscription_plan['renewal_sts']);
                }else{
                    $processReaction['data']['plan_deatail'] = "";
                }

                $processReaction['data']['block_user_collection'] = "";
                if (count($blockUserCollection) != 0) {
                    $processReaction['data']['block_user_collection'] = $blockUser_array;
                }

                // check if record does not exists
                if ($processReaction['reaction_code'] == 18) {
                    return redirect()->route('user.profile_view', ['username' => getUserAuthInfo('profile.username')]);
                }

                    //echo "<pre>";print_r($processReaction['data']);exit();

                $processReaction['data']['is_profile_page'] = true;
                $processReaction['data']['active_tab'] = 6;


                return $this->loadProfileView('payment.payment_faild', $processReaction['data']);
    }

  
    
}
