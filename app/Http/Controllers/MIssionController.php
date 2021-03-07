<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MIssionController extends Controller {

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getUserActiveMissions(Request $request) {
        $user = User::where('api_token', $request->api_token)->first();

        if($user)
            return response($user->player->playerMissions->load(['mission']), 200);
        else
            return response(['message' => 'Usuário não encontrado'], 404);

    }

}
