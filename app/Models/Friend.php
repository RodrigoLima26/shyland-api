<?php


namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends BaseModel {

    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function first_player() {
        return $this->hasOne(Player::class, 'id', 'id_player_1');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function second_player() {
        return $this->hasOne(Player::class, 'id', 'id_player_2');
    }

    /**
     * @param $user_id
     * @param $sender_id
     */
    public function store($user_id, $sender_id) {

        $this->id_player_1 = $user_id;
        $this->id_player_2 = $sender_id;

        $this->save();
    }
}
