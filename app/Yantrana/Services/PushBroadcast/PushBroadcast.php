<?php

namespace App\Yantrana\Services\PushBroadcast;

use Pusher\Pusher;
use Exception;

/*
 * PushBroadcast 
 * 
 *
 *--------------------------------------------------------------------------- */

/**
 * This PushBroadcast class.
 *---------------------------------------------------------------- */
class PushBroadcast
{
    /**
     * $pusher - pusher object
     *-----------------------------------------------------------------------*/
    private $pusher = null;

    /**
     * __construct
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        /**
         * Set up and return PayPal PHP SDK environment with PayPal access credentials.
         * This sample uses SandboxEnvironment. In production, use LiveEnvironment.
         */
        if (getStoreSettings('allow_pusher')) {
            $pusherAppId     = getStoreSettings('pusher_app_id');
            $pusherKey         = getStoreSettings('pusher_app_key');
            $pusherSecret   = getStoreSettings('pusher_app_secret');
            // Pusher call 
            $this->pusher = new Pusher(
                $pusherKey,
                $pusherSecret,
                $pusherAppId,
                [
                    'cluster' => getStoreSettings('pusher_app_cluster_key'),
                    'useTLS' => true
                ]
            );
        }
    }

    /**
     * trigger pusher services
     *-----------------------------------------------------------------------*/
    public function trigger($channels, $event, $data)
    {
        try {
            //trigger channel event to pusher instance
            if (getStoreSettings('allow_pusher')) {
                $this->pusher->trigger($channels, $event, $data);
            }
        } catch (Exception $e) {
            //log error message
            __logDebug($e->getMessage());
        }
    }

    /**
     * account trigger
     *-----------------------------------------------------------------------*/
    public function accountTrigger($event, $data)
    {
        return $this->trigger('channel-' . $data['userUid'], $event, $data);
    }

    /**
     * push via notification request
     *-----------------------------------------------------------------------*/
    public function notifyViaPusher($eventId, $data)
    {
        return $this->accountTrigger($eventId, $data);
    }
}
