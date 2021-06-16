<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller {

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request) {

        $user = User::where('email', $request->email)->
        where('password', md5($request->password))->
        first();

        if(!$user) return response(['message' => 'Usuário não encontrado com estas credenciais'], 404);
        else {
            if($user->ban_date) return response(['message' => 'Este usuário está banido'], 500);
            else if($user->adm == 0) return response(['message' => 'Este usuário não é administrador'], 500);

            return response(['user' => $user->load(['player'])], 200);
        }

    }

}
