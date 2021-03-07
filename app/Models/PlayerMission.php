<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerMission extends Model
{
    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mission() {
        return $this->hasOne(Mission::class, 'id', 'id_mission');
    }

    /**
     * @param $data
     */
    public function store($data) {

        $this->id_mission = $data['id_mission'];
        $this->id_player = $data['id_player'];

        $this->save();

    }
}
