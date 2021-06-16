<?php


namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends BaseModel {

    use HasFactory, SoftDeletes;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sender() {
        return $this->hasOne(Player::class, 'id', 'sender_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function player() {
        return $this->hasOne(Player::class, 'id', 'player_id');
    }

    /**
     * @param $data
     */
    public function saveFromFriendship($data) {

        $this->player_id = $data['player_id'];
        $this->sender_id = $data['sender_id'];
        $this->message = $data['message'];
        $this->title = $data['title'];
        $this->friend_request = $data['friend_request'];

        $this->save();
    }

    /**
     * @param $data
     */
    public function saveFromPrivateMessage($data) {

        $this->player_id = $data['player_id'];
        $this->sender_id = $data['sender_id'];
        $this->message = @$data['complaint_request'] ?: $data['message'];
        $this->title = $data['title'];

        if(@$data['complaint_request'])
            $this->complaint_request = true;

        $this->save();
    }

    /**
     * @param $data
     */
    public function saveFromSystemMessage($data) {

        $this->player_id = $data['player_id'];
        $this->message = @$data['complaint_request'] ?: $data['message'];
        $this->title = $data['title'];

        $this->save();
    }

    public function read() {
        $this->read = 1;
        $this->save();
    }
}
