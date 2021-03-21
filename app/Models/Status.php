<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model {

    use HasFactory;

    /**
     * @param $exp
     * @return bool
     */
    public function levelUp($exp) {

        $aux = $this->confidence + $exp;

        if($aux >= $this->conf_next_level) {

            $this->level = $this->level + 1;
            $this->confidence = $aux - $this->conf_next_level;
            $this->conf_next_level = $this->conf_next_level + $this->conf_next_level;

            $this->changeAllStatus(1);
        }
        else
            $this->confidence = $aux;

        return $this->save();
    }

    /**
     * @param $status
     * @param $quantity
     * @return bool
     */
    public function changeStatus($status, $quantity) {

        if($status == 'courage')
            $this->courage = $this->courage + $quantity;

        if($status == 'friendship')
            $this->friendship = $this->friendship + $quantity;

        if($status == 'sociability')
            $this->sociability = $this->sociability + $quantity;

        if($status == 'kindness')
            $this->kindness = $this->kindness + $quantity;

        if($status == 'criativity')
            $this->criativity = $this->criativity + $quantity;

        if($status == 'intelligence')
            $this->intelligence = $this->intelligence + $quantity;

        return $this->save();
    }

    /**
     * @param $quantity
     * @return bool
     */
    public function changeAllStatus($quantity) {

        $this->courage = $this->courage + $quantity;
        $this->friendship = $this->friendship + $quantity;
        $this->sociability = $this->sociability + $quantity;
        $this->kindness = $this->kindness + $quantity;
        $this->criativity = $this->criativity + $quantity;
        $this->intelligence = $this->intelligence + $quantity;

        return $this->save();
    }

    /**
     * @return array
     */
    public function statusList() {
        $data = [
            'courage' => $this->courage,
            'friendship' => $this->friendship,
            'sociability' => $this->sociability,
            'kindness' => $this->kindness,
            'criativity' => $this->criativity,
            'intelligence' => $this->intelligence
        ];

        return $data;
    }

    /**
     * @return array
     */
    public function getBestStatus() {

        $data = array_keys($this->statusList());
        $best = ["value" => 0, "status" => ""];

        foreach($data as $key) {
            if($data[$key] > $best["value"])
                $best = ["value" => $data[$key], "status" => $key];
        }

        return $best;
    }

    /**
     * @param $player_id
     */
    public function newPlayerStatus($player_id) {
        $this->id_player = $player_id;
        $this->save();
    }
}
