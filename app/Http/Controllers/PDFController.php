<?php


namespace App\Http\Controllers;

use App\Models\Views\VQtdeNewUsersLastSevenDays;
use App\Models\Views\VQtdeMissionsCompleteByRank;
use App\Models\Views\VQtdeMissionsCompletedByType;
use App\Models\Views\VQtdePrivateNotificationsLastSevenDays;
use App\Models\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PDFController extends Controller {

    /**
     * @param Request $request
     * @return false|string
     */
    public function newUsersLastSevenDays(Request $request) {

        $users_last_seven_days = VQtdeNewUsersLastSevenDays::get();

        $pdf = PDF::loadView('prints.new-users-last-seven-days', compact('users_last_seven_days'));

        return $pdf->download('new-users-last-seven-days.pdf');

    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function missionsCompletedByRank(Request $request) {

        $missions_by_rank = VQtdeMissionsCompleteByRank::get();

        $pdf = PDF::loadView('prints.missions-by-rank', compact('missions_by_rank'));

        return $pdf->download('missions-by-rank.pdf');

    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function missionsCompletedByType(Request $request) {

        $missions_by_type = VQtdeMissionsCompletedByType::get();

        $pdf = PDF::loadView('prints.missions-by-type', compact('missions_by_type'));

        return $pdf->download('missions-by-type.pdf');

    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function privatedNotificationsLastWeek(Request $request) {

        $notifications = VQtdePrivateNotificationsLastSevenDays::get();

        $pdf = PDF::loadView('prints.privated-notifications-seven-days', compact('notifications'));

        return $pdf->download('privated-notifications-seven-days.pdf');

    }

}
