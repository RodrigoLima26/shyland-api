<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mission extends Model {

    use HasFactory;
    use SoftDeletes;

    /**
     * @param $data
     */
    public function store($data) {

        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->confidence = $data['confidence'];
        $this->rank = $data['rank'];
        $this->status = $data['status'];
        $this->exp_status = $data['exp_status'];

        $this->save();

    }

    public function scopeGetPlayerMission($query, Status $status) {

        return $query->when($status->level <= 8, function($query) {
            return $query->where('rank', 'E');
        })->when($status->level > 8 && $status->level <= 16, function($query) {
            return $query->whereIn('rank', ['E', 'D']);
        })->when($status->level > 16 && $status->level <= 24, function($query) {
            return $query->whereIn('rank', ['E', 'D', 'C']);
        })->when($status->level > 24 && $status->level <= 32, function($query) {
            return $query->whereIn('rank', ['E', 'D', 'C', 'B']);
        })->when($status->level > 32 && $status->level <= 40, function($query) {
            return $query->whereIn('rank', ['E', 'D', 'C', 'B', 'A']);
        })->when($status->level > 40, function($query) {
            return $query->whereIn('rank', ['E', 'D', 'C', 'B', 'A', 'S']);
        });

    }
}
