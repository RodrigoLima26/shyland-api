<?php


namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends BaseModel {

    use HasFactory;

    /*  RELATIONSHIPS  */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playerMissions() {

        return $this->hasMany(PlayerMission::class, 'id_player', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bags() {

        return $this->hasMany(Bag::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status() {

        return $this->hasOne(Status::class, 'id_player', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() {

        return $this->hasOne(User::class);
    }

    /**
     * @param $data
     * @return bool
     */
    public function store($data) {
        $this->username = $data['username'];
        $this->birthdate = @$data['birthdate'] ?: $this->birthdate;

        return $this->save();
    }
}
