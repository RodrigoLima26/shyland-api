<?php

namespace App\Models;

use Carbon\Carbon;
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

    /**
     *
     */
    public function abandon() {
        $this->abandoned_at = Carbon::now()->format('Y-m-d H:s:i');
        $this->save();
    }

    /**
     *
     */
    public function complete() {
        $this->completed_at = Carbon::now()->format('Y-m-d H:s:i');
        $this->save();
    }
}
