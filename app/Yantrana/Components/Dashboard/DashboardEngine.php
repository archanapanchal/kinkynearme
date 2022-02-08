<?php
/**
* DashboardEngine.php - Main component file
*
* This file is part of the Dashboard component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Dashboard;

use App\Yantrana\Base\BaseEngine;
use App\Yantrana\Components\Dashboard\Interfaces\DashboardEngineInterface;
use App\Yantrana\Components\Dashboard\Repositories\DashboardRepository;
use Carbon\Carbon;

class DashboardEngine extends BaseEngine implements DashboardEngineInterface
{
    /**
     * @var  DashboardRepository $dashboardRepository - Dashboard Repository
     */
    protected $dashboardRepository;

    /**
     * Constructor
     *
     * @param  DashboardRepository $dashboardRepository - Dashboard Repository
     * @return  void
     *-----------------------------------------------------------------------*/

    function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository     = $dashboardRepository;
    }

    /**
     * Prepare Dashboard
     *
     * @return  void
     *-----------------------------------------------------------------------*/
    public function prepareDashboard()
    {
        //all users
//        $users = $this->dashboardRepository->fetchUsers();

        // All Group By USers
        $users = $this->dashboardRepository->fetchGroupByUsers('status');

        //online users
        $onlineUsers = $this->dashboardRepository->fetchOnlineUsers();
        //abuse reports awaiting moderation
        $abuseReportCount = $this->dashboardRepository->abuseReports(1)->count();
        // all transactions
        $transactions = $this->dashboardRepository->currentYearFinancialTransactions(2)->toArray();
        //monthly transactions
        $monthlyTransactions = collect($transactions)->groupBy('month');
        // all users registered in current year
        $currentYearRegistrations = $this->dashboardRepository->currentYearRegistrations()->toArray();
        //month wise collection
        $monthWiseUsers = collect($currentYearRegistrations)->groupBy(['gender', 'month']);

        $datasets = [
            1 => [
                'label' => "Male",
                'backgroundColor' => "blue",
                'data' => array_fill(0, 12, 0)
            ],
            2 => [
                'label' => "Female",
                'backgroundColor' => "pink",
                'borderColor' => "pink",
                'borderWidth' => 1,
                'data' => array_fill(0, 12, 0)
            ],
            3  => [
                'label' => "Secret",
                'backgroundColor' => "grey",
                'borderColor' => "grey",
                'borderWidth' => 1,
                'data' => array_fill(0, 12, 0)
            ],
        ];

        //counts
        $dashboardCounts = [
            'online' => $onlineUsers->count(),
            'active' => 0,
            'inactive' => 0,
            'blocked' => 0,
            'awaiting_abuse_report_count' => $abuseReportCount,
            'current_month_income' => 0,
            'month_wise_income' => array_fill(0, 12, 0),
            'currency' => getCurrencySymbol(),
            'current_year_registrations' => $datasets
        ];

        if (!__isEmpty($monthWiseUsers)) {
            foreach ($monthWiseUsers as $key => $monthWiseUser) {
                foreach ($monthWiseUser as $key2 => $mtUsers) {
                    $dashboardCounts['current_year_registrations'][$key]['data'][$key2 - 1] = $mtUsers->count();
                }
            }
        }

        $currentMonth = (int) date('m');

        if (!__isEmpty($monthlyTransactions)) {
            foreach ($monthlyTransactions as $key => $trans) {
                if (!__isEmpty($trans)) {

                    $amount = array_sum($trans->pluck('amount')->toArray());
                    //month wise income
                    $dashboardCounts['month_wise_income'][$key - 1] = $amount;
                    //current month earning
                    if ($currentMonth == $key) {
                        $dashboardCounts['current_month_income'] = number_format((float) $amount, 2);
                    }
                }
            }
        }

        //check if users not empty
        if (!__isEmpty($users)) {
            foreach ($users as $key => $status) {

                switch ($key) {
                    case 1:
                        $dashboardCounts['active']     = $status->count();
                        break;
                    case 2:
                        $dashboardCounts['inactive'] = $status->count();
                        break;
                    case 3:
                        $dashboardCounts['blocked']     = $status->count();
                        break;
                    default:
                        break;
                }
            }
        }

        return [
            'dashboardData' => $dashboardCounts
        ];
    }
}
