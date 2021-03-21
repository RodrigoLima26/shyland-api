<?php


namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\PlayerMission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MIssionController extends Controller {

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getUserActiveMissions(Request $request) {
        $user = User::where('api_token', $request->api_token)->first();

        if($user) {
            return response($user->player->playerMissions()->
                                            whereNull('completed_at')->
                                            whereNull('abandoned_at')->
                                            with(['mission'])->get(), 200);
        }
        else
            return response(['message' => 'Usuário não encontrado'], 404);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getAllUserMissions(Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        if($user) {
            return response($user->player->playerMissions()->
                                            orderBy('created_at', 'desc')->
                                            with(['mission'])->paginate(20), 200);
        }
        else
            return response(['message' => 'Usuário não encontrado'], 404);

    }

    /**
     * @param PlayerMission $playermission
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function cancelMission(PlayerMission $playermission, Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        if($user) {

            $playermission->abandon();

            $new_player_mission = new PlayerMission();

            $mission = Mission::getPlayerMission($user->player->status)->orderByRaw("RAND()")->first();

            $new_player_mission->store(['id_mission' => $mission->id, 'id_player' => $user->player->id]);

            return response($new_player_mission->load(['mission']));
        }
        else
            return response(['message' => 'Usuário não encontrado'], 404);
    }

    /**
     * @param PlayerMission $playermission
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function completeMission(PlayerMission $playermission, Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        if($user) {

            $playermission->complete();

            $user->player->status->levelUp($playermission->mission->confidence);
            $user->player->status->changeStatus($playermission->mission->status, $playermission->mission->exp_status);

            $new_player_mission = new PlayerMission();

            $mission = Mission::getPlayerMission($user->player->status)->orderByRaw("RAND()")->first();

            $new_player_mission->store(['id_mission' => $mission->id, 'id_player' => $user->player->id]);

            return response($new_player_mission->load(['mission']));

        }
        else
            return response(['message' => 'Usuário não encontrado'], 404);
    }

}
