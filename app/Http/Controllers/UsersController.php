<?php


namespace App\Http\Controllers;


use App\Models\Friend;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\Player;
use App\Models\PlayerMission;
use App\Models\Status;
use App\Models\User;
use App\Models\Views\VUsersSearch;
use App\Notifications\BanUserMessage;
use App\Notifications\RecoverPasswordNotification;
use Carbon\Carbon;
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

            if(count($hasEmail) > 0) return response(['email' => ['E-mail já está sendo utilizado por outro usuário']], 503);
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

                $missions = Mission::where('rank', 'E')->orderByRaw("RAND()")->limit(3)->get();

                foreach($missions as $mission) {
                    $player_mission = new PlayerMission();

                    $player_mission->store(['id_mission' => $mission->id, 'id_player' => $player->id]);
                }

                $status = new Status();
                $status->newPlayerStatus($player->id);

                $user->storePhoto(User::prePhoto());

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

            $missions = Mission::where('rank', 'E')->orderByRaw("RAND()")->limit(3)->get();

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

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getUserById(User $user, Request $request) {

        return response(['user' => $user->load(['player', 'player.status'])], 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function photoUpload(Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        $user->storePhoto($request->profile_pic);

        return response(['user' => $user->load(['player', 'player.status'])], 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getAllUsers(Request $request) {

        $q = $request->q;
        $birthdate = !$request->birthdate || ($request->birthdate == 'undefined') ? null : $request->birthdate;
        $order = !$request->order || ($request->order == 'undefined') ? 'desc' : $request->order;
        $has_ban = $request->onlyban && $request->onlyban != "false" ?: false;

        $users =  VUsersSearch::where('api_token', '<>', $request->api_token)->
                                when($q, function($query) use ($q) {
                                    return $query->where('username', 'like', '%'.$q.'%');
                                })->
                                when($birthdate, function($query) use ($birthdate) {
                                    return $query->where('birthdate', $birthdate);
                                })->
                                when($has_ban, function($query) {
                                    return $query->whereNotNull('ban_date');
                                })->
                                when(!$has_ban, function($query) {
                                    return $query->whereNull('ban_date');
                                })->
                                groupBy('user_id')->
                                orderBy('user_created_at', $order)->
                                paginate(15);

        return response($users, 200);
    }

    /**
     * @param Request $request
     */
    public function getUserFriendship(Request $request) {

        $user = User::where('api_token', $request->api_token)->first();
        $user_friend = User::where('id', $request->user_id)->first();

        $friend = Friend::where(function($query) use ($user) {
            return $query->where('id_player_1', $user->player->id)->
                           orWhere('id_player_2', $user->player->id);
        })->
        where(function($query) use ($user_friend) {
            return $query->where('id_player_1', $user_friend->player->id)->
                           orWhere('id_player_2', $user_friend->player->id);
        })->first();

        $notification = Notification::where(function($query) use ($user) {
            return $query->where('player_id', $user->player->id)->
                           orWhere('sender_id', $user->player->id);
        })->
        where(function($query) use ($user_friend) {
            return $query->where('player_id', $user_friend->player->id)->
                           orWhere('sender_id', $user_friend->player->id);
        })->first();

        return response(['friend' => $friend, 'notification' => $notification], 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getAllUserFriendships(Request $request) {
        $user = User::where('api_token', $request->api_token)->first();

        $friends = Friend::where(function($query) use ($user) {
            return $query->where('id_player_1', $user->player->id)->
            orWhere('id_player_2', $user->player->id);
        })->with(['first_player', 'first_player.user', 'first_player.status', 'second_player', 'second_player.user', 'second_player.status'])->
        get();

        return response($friends, 200);
    }

    /**
     * @param User $visit
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function friendshipNotification(User $user, Request $request) {

        $visit = User::where('api_token', $request->api_token)->first();

        $data = [
            'player_id' => $user->player->id,
            'sender_id' => $visit->player->id,
            'message' => $visit->player->username." deseja ser seu amigo.",
            'title' => "Pedido de amizade de ".$visit->player->username,
            'friend_request' => true
        ];

        $notification = new Notification();

        $notification->saveFromFriendship($data);

        return response($notification, 200);

    }

    /**
     * @param Notification $notification
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function confirmFriendship(Notification $notification, Request $request) {

        $friend = new Friend();

        $friend->store($notification->player_id, $notification->sender_id);

        $notification->delete();

        return response($friend, 200);

    }

    /**
     * @param Notification $notification
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function deleteFriendRequest(Notification $notification, Request $request) {

        $notification->delete();

        return response($notification, 200);

    }

    /**
     * @param Friend $friend
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function unfriend(Friend $friend, Request $request) {

        $friend->delete();

        return response($friend, 200);

    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function banUser(User $user, Request $request) {

        $user->ban(Carbon::now());

        $user->notify(new BanUserMessage());

        return response($user, 200);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function unbanUser(User $user, Request $request) {

        $user->unban();

        return response($user, 200);
    }
}
