<?php
/**
* CreditWalletRepository.php - Repository file
*
* This file is part of the Credit Wallet User component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\User\Repositories;

use DB;
use App\Yantrana\Base\BaseRepository;
use App\Yantrana\Components\User\Models\{
    User as UserModel,
    UserAuthorityModel,
    CreditWalletTransaction,
    Payment
};
use App\Yantrana\Components\FinancialTransaction\Models\FinancialTransaction;

class CreditWalletRepository extends BaseRepository
{
    /**
     * fetch user wallet transaction list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchUserWalletTransactionList()
    {
        $dataTableConfig = [
            'searchable' => []
        ];

        return CreditWalletTransaction::with('getUserGiftTransaction', 'getUserStickerTransaction', 'getUserBoostTransaction', 'getUserSubscriptionTransaction', 'getUserFinancialTransaction')
            ->select(
                '_id',
                '_uid',
                'created_at',
                'credits',
                'financial_transactions__id',
                'credit_type'
            )
            ->where('credit_wallet_transactions.users__id', getUserID())
            ->dataTables($dataTableConfig)
            ->toArray();
    }

    /**
     * fetch api user wallet transaction list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchApiUserWalletTransactionList()
    {
        $dataTableConfig = [
            'searchable' => []
        ];

        return CreditWalletTransaction::with('getUserGiftTransaction', 'getUserStickerTransaction', 'getUserBoostTransaction', 'getUserSubscriptionTransaction', 'getUserFinancialTransaction')
            ->select(
                '_id',
                '_uid',
                'created_at',
                'credits',
                'financial_transactions__id',
                'credit_type'
            )
            ->where('credit_wallet_transactions.users__id', getUserID())
            ->customTableOptions($dataTableConfig);
    }

    /**
     * fetch user transaction list.
     *
     * @return array
     *---------------------------------------------------------------- */
    public function fetchUserTransactionListData($userId)
    {
        $dataTableConfig = [
            'searchable' => []
        ];

        /*$user_settings = DB::table('payments')->where('user_id',$userId)->dataTables($dataTableConfig)
            ->toArray();

            echo "<pre>";print_r($user_settings);exit();
        foreach ($user_settings as $key => $settings) {

             return  $settings_responce['data'] = array('transaction_id' => $settings->transaction_id,'amount' => $settings->amount,'currency' => $settings->currency,'payment_status' => $settings->payment_status,'created_at' => $settings->created_at,'method' => "card",'package' => "1");
                /////echo "<pre>";print_r($settings_responce);exit();
        }*/
            //echo "<pre>";print_r($user_settings);exit();
        return Payment::
        // leftJoin('user_subscriptions', 'payments.paln_id_detail', '=', 'user_subscriptions.plan_id')->
        where('user_id', $userId)
            ->select(
                __nestedKeyValues([
                    'payments' => [
                        'id',
                        'transaction_id',
                        'amount',
                        'currency',
                        'payment_status',
                        'amount',
                        'created_at',
                        'paln_id_detail',
                        'paln_title_detail',
                        'plan_type_detail',
                        'plan_active_status'
                    ],
                    // 'user_subscriptions' => [
                    //     '_id as sub_id',
                    //     '_uid as sub_uid',
                    //     'expiry_at',
                    // ],
                ])
            )
            // ->where('user_subscriptions.status',1)
            // ->where('user_subscriptions.users__id',$userId)
            ->dataTables($dataTableConfig)
            ->toArray();
    }


    /**
     * Store new coupon using provided data.
     *
     * @param array $inputData
     *
     * @return mixed
     *---------------------------------------------------------------- */
    public function storeTransaction($inputData, $packageData)
    {
        $keyValues = [
            'status',
            'amount',
            'users__id',
            'method',
            'currency_code',
            'is_test',
            '__data'
        ];

        $financialTransaction = new FinancialTransaction;

        // Check if new User added
        if ($financialTransaction->assignInputsAndSave($inputData, $keyValues)) {
            //wallet transaction store data
            $keyValues = [
                'status'         => 1,
                'users__id'        => getUserID(),
                'credits'         => (int) $packageData['credits'],
                'financial_transactions__id' => $financialTransaction->_id,
                'credit_type'     => 2 //Purchased
            ];

            $CreditWalletTransaction = new CreditWalletTransaction;
            // Check if new User added
            if ($CreditWalletTransaction->assignInputsAndSave([], $keyValues)) {
                return true;
            }
        }
        return false;   // on failed
    }

    /**
     * Store new coupon using provided data.
     *
     * @param array $inputData
     *
     * @return mixed
     *---------------------------------------------------------------- */
    public function storeWalletTransaction($inputData)
    {
        $keyValues = [
            'status',
            'users__id',
            'credits' => $inputData['credits'],
            'financial_transactions__id',
            'credit_type'
        ];

        $CreditWalletTransaction = new CreditWalletTransaction;

        // Check if new User added
        if ($CreditWalletTransaction->assignInputsAndSave($inputData, $keyValues)) {
            return $CreditWalletTransaction;
        }

        return false;   // on failed
    }
}
