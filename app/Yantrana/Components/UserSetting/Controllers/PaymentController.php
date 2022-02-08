<?php
/**
* UserController.php - Controller file
*
* This file is part of the User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Controllers;

use App\Yantrana\Base\BaseController;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use App\Yantrana\Components\User\Models\Payment;
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
use App\Yantrana\Components\User\Models\UserSubscription;
use App\Yantrana\Components\User\Repositories\UserRepository;
use App\Yantrana\Components\Plans\Repositories\ManagePlansRepository;
use App\Yantrana\Components\User\UserEngine;
use App\Yantrana\Support\CommonUnsecuredPostRequest;
use App\Yantrana\Components\Plans\ManagePlansEngine;
use Auth;
use \Illuminate\Support\Facades\DB;

class PaymentController extends BaseController
{
    public $gateway;

    protected $managePlansRepository;
    protected $userEngine;
    protected $userRepository;
    protected $managePlanEngine;
  
    public function __construct(ManagePlansRepository $managePlansRepository,UserEngine $userEngine, ManagePlansEngine $managePlanEngine,UserRepository $userRepository)
    {
        $this->gateway = Omnipay::create('AuthorizeNetApi_Api');
        $this->gateway->setAuthName(env('ANET_API_LOGIN_ID'));
        $this->gateway->setTransactionKey(env('ANET_TRANSACTION_KEY'));
        $this->gateway->setTestMode(true); //comment this line when move to 'live'
        $this->managePlansRepository  = $managePlansRepository;
        $this->userEngine = $userEngine;
        $this->managePlanEngine         = $managePlanEngine;
        $this->userRepository         = $userRepository;
    }
  
    public function index()
    {
        return view('payment');
    }
  
    public function charge(Request $request)
    {
           

       $users_id = $request->input('users_id_detail');
        try {
            $creditCard = new \Omnipay\Common\CreditCard([
                'number' => $request->input('cc_number'),
                'expiryMonth' => $request->input('expiry_month'),
                'expiryYear' => $request->input('expiry_year'),
                'cvv' => $request->input('cvv'),
            ]);
  
            // Generate a unique merchant site transaction ID.
            $transactionId = rand(100000000, 999999999);
  
            $response = $this->gateway->authorize([
                'amount' => $request->input('amount'),
                'currency' => 'USD',
                'transactionId' => $transactionId,
                'card' => $creditCard,
            ])->send();
  
            if($response->isSuccessful()) {
  
                // Captured from the authorization response.
                $transactionReference = $response->getTransactionReference();
  
                $response = $this->gateway->capture([
                    'amount' => $request->input('amount'),
                    'currency' => 'USD',
                    'transactionReference' => $transactionReference,
                    ])->send();
  
                $transaction_id = $response->getTransactionReference();
                $amount = $request->input('amount');
  
                // Insert transaction data into the database
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
                if ($request->input('plan_type_detail') == 1) {
                    $subscribe_plan_expiry = date("Y-m-d H:i:s", strtotime(" +1 months"));
                } 

                $planCollection = $this->managePlansRepository->fetch($request->input('paln_id_detail'));
                $PlansInsertDetail = array('_uid' => $planCollection['_uid'], 'created_at' => $planCollection['created_at']->toDateString(), 'updated_at' => $planCollection['updated_at']->toDateString(), 'status' => $planCollection['status'], 'users__id' => $users_id,'expiry_at' => "", 'credit_wallet_transactions__id' => "", 'plan_id' => $request->input('paln_id_detail'));



                   
                    
                if($isPaymentExist)
                {
                    $payment = new Payment;
                    $payment->transaction_id = $transaction_id;
                    $payment->amount = $request->input('amount');
                    $payment->user_id = $request->input('users_id_detail');
                    $payment->paln_id_detail = $request->input('paln_id_detail');
                    $payment->paln_title_detail = $request->input('paln_title_detail');
                    $payment->plan_type_detail = $request->input('plan_type_detail');
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
                    $subscription->plan_id = $request->input('paln_id_detail');
                    $subscription->save();

                        echo "<pre>";print_r($subscription);exit();
                       
                        
                }
                if(!$isPaymentExist)
                {
                    $payment = new Payment;
                    $payment->transaction_id = $transaction_id;
                    $payment->amount = $request->input('amount');
                    $payment->user_id = $request->input('users_id_detail');
                    $payment->paln_id_detail = $request->input('paln_id_detail');
                    $payment->paln_title_detail = $request->input('paln_title_detail');
                    $payment->plan_type_detail = $request->input('plan_type_detail');
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
                    $subscription->plan_id = $request->input('paln_id_detail');
                    $subscription->save();
                     
                        
                }


                /*======================================================*/
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

                //return $this->loadView('payment.payment_success');
                //return "Payment is successful. Your transaction id is: ". $transaction_id;
            } else {
                // not successful
                return $response->getMessage();
            }
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
}
