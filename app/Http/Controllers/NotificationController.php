<?php


namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Notification;
use App\Models\Player;
use App\Models\PlayerMission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class NotificationController extends Controller {

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getAllNotifications(Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        $friendship = $request->firendship;
        $not_read = $request->not_read;

        $notifications = Notification::when($friendship, function($query) {
            return $query->where('friend_request', 1);
        })->
        when($not_read, function($query) {
            return $query->where('read', 0);
        })->
        where('complaint_request', '<>', '1')->
        where('player_id', $user->player->id)->
        with(['sender', 'sender.user'])->
        orderBy('created_at', 'desc')->paginate(20);

        return response($notifications, 200);
    }

    /**
     * @param Notification $notification
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function deleteNotification(Notification $notification, Request $request) {

        $notification->delete();

        return response($notification, 200);

    }

    /**
     * @param Notification $notification
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function readNotification(Notification $notification, Request $request) {

        $notification->read();

        return response($notification, 200);

    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function replyNotification(User $user, Request $request) {

        $visit = User::where('api_token', $request->api_token)->first();

        $data = [
            'player_id' => $user->player->id,
            'sender_id' => $visit->player->id,
            'message' => $request->message,
            'title' => $request->title ? $request->title : "Mensagem privada de ".$visit->player->username
        ];

        if($request->ban) {
            $data['complaint_request'] = $request->ban;
            $data['title'] = 'Denuncia de '.$visit->player->username;
        }

        $notification = new Notification();

        $notification->saveFromPrivateMessage($data);

        return response($notification, 200);

    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function notReadNotifications(User $user, Request $request) {

        $user = User::where('api_token', $request->api_token)->first();

        $notification_count = $user->player->notifications()->where('complaint_request', '<>', '1')->where('read', '<>', '1')->count();

        return response(["count" => $notification_count], 200);

    }

    /**
     * @param Player $player
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function systemMessage(Player $player, Request $request) {

        $data = [
            'player_id' => $player->id,
            'message' => $request->message,
            'title' => $request->title
        ];

        $notification = new Notification();

        $notification->saveFromSystemMessage($data);

        return response($notification, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getAdminNotifications(Request $request) {
        $notifications = Notification::whereNull('player_id')->
                                       orWhere('complaint_request', 1)->
                                       with(['sender', 'sender.user', 'player', 'player.user'])->
                                       orderBy('created_at', 'desc')->
                                       orderBy('read', 'asc')->
                                       paginate(20);

        return response($notifications, 200);
    }
}
