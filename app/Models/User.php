<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function player() {
        return $this->hasOne(Player::class, 'id', 'id_player');
    }

    // CRUD

    /**
     * @return bool
     */
    public function store($data) {

        $this->password = $data['password'];
        $this->email = $data['email'];
        $this->api_token = $data['api_token'];
        $this->adm = 0;
        $this->id_player = $data['id_player'];

        return $this->save();
    }

    /**
     * @param $data
     */
    public function resetPassword($data) {

        $this->password = $data['password'];
        $this->recovery_token = null;

        $this->save();
    }

    // Helpers

    /**
     * @return mixed
     */
    public function isAdm() {
        return $this->adm;
    }

    /**
     * @param $date
     * @return bool
     */
    public function ban($date) {

        $this->ban_date = $date;
        return $this->save();
    }

    /**
     * @return false
     */
    public function isBanned() {

        if(!$this->ban_date) return false;
        else
            return $this->ban_date->gt(Carbon::now());
    }

    /**
     *
     */
    public function generateRecoveryToken() {
        $this->recovery_token = md5(rand(100, 900));
        $this->save();
    }

    /**
     * @param $data
     */
    public function storeFromOAuth($data) {

        $this->email = $data['email'];
        $this->api_token = $data['api_token'];
        $this->adm = 0;
        $this->id_player = $data['id_player'];

        $this->save();

    }

    /**
     * @param $photo
     */
    public function storePhoto($photo) {
        $this->profile_pic = $photo;
        $this->save();
    }
}
