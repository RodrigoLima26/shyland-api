<?php


namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Player;
use App\Models\PlayerMission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MissionController extends Controller {

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

    public function cancelAutomatedMission(Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        if($user) {

            $playermission = PlayerMission::where('id_player', $user->player->id)->
                                            whereNull('completed_at')->
                                            orderByRaw("RAND()")->first();

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
    public function completeAutomatedMission(Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        if($user) {

            $playermission = PlayerMission::where('id_player', $user->player->id)->
                                            whereNull('completed_at')->
                                            orderByRaw("RAND()")->first();

            $playermission->complete();

            $user->player->status->levelUp($playermission->mission->confidence);
            $user->player->status->changeStatus($playermission->mission->status, $playermission->mission->exp_status);

            $new_player_mission = new PlayerMission();

            $mission = Mission::orderByRaw("RAND()")->first();

            $new_player_mission->store(['id_mission' => $mission->id, 'id_player' => $user->player->id]);

            return response($new_player_mission->load(['mission']));

        }
        else
            return response(['message' => 'Usuário não encontrado'], 404);
    }

    /**
     * @param Player $player
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getAllUserMissionsById(Player $player, Request $request) {

        $playermissions = $player->playerMissions()->
                                   orderBy('created_at', 'desc')->
                                   with(['mission'])->paginate(20);

        return response($playermissions, 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getMissions(Request $request) {

        $status = $request->status && $request->status != 'undefined' ? $request->status : null;
        $rank = $request->rank && $request->rank != 'undefined' ? $request->rank : null;
        $q = $request->q && $request->q != 'undefined' ? $request->q : null;

        $missions = Mission::when($q, function($query) use ($q) {
            return $query->where('description', 'like', '%'.$q.'%')->
                           orWhere('title', 'like', '%'.$q.'%');
        })->when($status, function($query) use ($status) {
            return $query->where('status', $status);
        })->when($rank, function($query) use ($rank) {
            return $query->where('rank', $rank);
        })->paginate(20);

        return response($missions, 200);

    }

    /**
     * @param Mission $mission
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function deleteMissions(Mission $mission, Request $request) {

        $mission->delete();

        $player_missions = PlayerMission::where('id_mission', $mission->id)->get();

        foreach ($player_missions as $player_mission) {
            $player_mission->delete();
        }

        return response($mission, 200);

    }

    /**
     * @param Mission $mission
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getMission(Mission $mission, Request $request) {

        return response($mission, 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function setMission(Request $request) {

        $mission = $request->id ? Mission::where('id', $request->id)->first() : new Mission();

        $mission->store($request->all());

        return response($mission, 200);

    }
}
