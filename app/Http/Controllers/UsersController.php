<?php


namespace App\Http\Controllers;


use App\Models\Mission;
use App\Models\Player;
use App\Models\PlayerMission;
use App\Models\Status;
use App\Models\User;
use App\Notifications\RecoverPasswordNotification;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class UsersController extends Controller {

    public function index(Request $request) {
        return json_encode($request->all());
    }

    /**
     * @param Request $request
     */
    public function register(Request $request) {
        try {

            $data = $request->all();

            $hasEmail = User::where('email', $request->email)->get();

            if(count($hasEmail) > 0) return response(['email' => 'E-mail já está sendo utilizado por outro usuário'], 500);
            else {
                $player = new Player();

                $player->store(['username' => $data['username'], 'birthdate' => $data['player']['birthdate']]);

                $user = new User();

                $data = [
                    'password' => md5($data['password']),
                    'email' => $data['email'],
                    'api_token' => md5(rand(100, 900)),
                    'id_player' => $player->id
                ];

                $user->store($data);

                $missions = Mission::where('rank', 'E')->orderByRaw("RAND()")->get();

                foreach($missions as $mission) {
                    $player_mission = new PlayerMission();

                    $player_mission->store(['id_mission' => $mission->id, 'id_player' => $player->id]);
                }

                $status = new Status();
                $status->newPlayerStatus($player->id);

                return response($user->load(['player']), 200);
            }
        }
        catch(\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

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

            return response(['user' => $user->load(['player'])], 200);
        }

    }

    public function recover(Request $request) {

        $user = User::where('email', $request->email)->
                      first();

        if(!$user) return response(['message' => 'Usuário não encontrado com este e-mail'], 404);
        else {
            if($user->ban_date) return response(['message' => 'Este usuário está banido'], 500);

            $user->generateRecoveryToken();

            $user->notify(new RecoverPasswordNotification());

            return response(['message' => 'E-mail enviado com sucesso!'], 200);
        }
    }

    /**
     * @param $token
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function recoverConfirm($token, Request $request) {

        $user = User::where('recovery_token', $token)->
                      first();

        if(!$user) return response(['message' => 'Usuário não encontrado'], 404);
        else {
            if($user->ban_date) return response(['message' => 'Este usuário está banido'], 500);

            $user->resetPassword($request->all());

            return response(['message' => 'Senha resetada com sucesso!'], 200);
        }
    }

    /**
     * @param $media
     * @param Request $request
     * @return mixed
     */
    public function socialLogin($media, Request $request) {

        return Socialite::driver($media)->stateless()->redirect();
    }

    /**
     * @param $media
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function socialLoginCallback($media, Request $request) {

        $OAuthUser = Socialite::driver($media)->stateless()->user();

        $user = User::where('email', $OAuthUser->email)->withTrashed()->first();

        if($user) {

            if($user->ban_date) return redirect(env('SHYLAND_APP_URL').'/404');

            if($OAuthUser->avatar && !$user->profile_pic)
                $user->storePhoto($OAuthUser->avatar);

            return redirect(env('SHYLAND_APP_URL').'/oauth/login/'.$user->api_token);
        }
        else {

            $player = new Player();
            $player->store([
                'username' => $OAuthUser->name
            ]);

            $user = new User();
            $user->storeFromOAuth([
                'email' => $OAuthUser->email,
                'id_player' => $player->id,
                'api_token' => md5(rand(100, 900))
            ]);

            $status = new Status();
            $status->newPlayerStatus($player->id);

            $missions = Mission::where('rank', 'E')->orderByRaw("RAND()")->get();

            foreach($missions as $mission) {
                $player_mission = new PlayerMission();

                $player_mission->store(['id_mission' => $mission->id, 'id_player' => $player->id]);
            }

            if($OAuthUser->avatar)
                $user->storePhoto($OAuthUser->avatar);


            return redirect(env('SHYLAND_APP_URL').'/oauth/login/'.$user->api_token);
        }


    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getUserByApiToken(Request $request) {

        $user = User::where('api_token', $request->api_token)->
                      with(['player', 'player.status'])->first();


        return response(['user' => $user], 200);
    }

    /**
     * @param $token
     * @param Request $request
     */
    public function loginWithToken($token, Request $request) {

        $user = User::where('api_token', $token)->
                      with(['player', 'player.status'])->first();

        if(!$user) return response(['message' => 'Usuário não encontrado com estas credenciais'], 404);
        else {
            if($user->ban_date) return response(['message' => 'Este usuário está banido'], 500);

            return response(['user' => $user], 200);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function changePassword(Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        $user->password = md5($request->password);

        $user->save();

        return response(['user' => $user], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateUser(Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        $data = $request->all();

        $user->player->store($data['player']);

        return response(['user' => $user], 200);
    }
}
