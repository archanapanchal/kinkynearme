<?php

namespace App\Yantrana\Components\User\Models;

use App\Yantrana\Base\BaseModel;
use App\Yantrana\Components\Plans\Models\{PlanModel};


class UserSubscription extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_subscriptions';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        '_id'               => 'integer',
        'status'            => 'integer',
        'users__id'         => 'integer',
        'credit_wallet_transactions__id' => 'integer'
    ];


    /**
     * Get the profile record associated with the user.
     */
    public function userPlan()
    {
        return $this->hasOne(PlanModel::class, '_id', 'plan_id');
    }
}
