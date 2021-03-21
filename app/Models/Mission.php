<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model {
    use HasFactory;

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
