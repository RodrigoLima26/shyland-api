<?php

namespace App\Http\Controllers;

use App\Models\Views\VQtdeNewUsersLastSevenDays;
use App\Models\Views\VQtdeMissionsCompleteByRank;
use App\Models\Views\VQtdeMissionsCompletedByType;
use App\Models\Views\VQtdePrivateNotificationsLastSevenDays;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller {

    public function getDashboard(Request $request) {

        $data = [
            "graphNewUsersLastSevenDays" => VQtdeNewUsersLastSevenDays::get(),
            "qtdMissionsCompletedByRank" => VQtdeMissionsCompleteByRank::get(),
            "qtdMissionsCompletedByType" => VQtdeMissionsCompletedByType::get(),
            "qtdPrivateNotificationsLastWeek" => VQtdePrivateNotificationsLastSevenDays::get()
        ];

        return response($data, 200);

    }

}
