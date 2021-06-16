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
     * @param $date
     * @return bool
     */
    public function unban() {

        $this->ban_date = null;
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

    public static function prePhoto() {
        return 'data:image/png;base64,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';
    }
}
