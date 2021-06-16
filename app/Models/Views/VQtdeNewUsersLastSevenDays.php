<?php


namespace App\Models\Views;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VQtdeNewUsersLastSevenDays extends BaseModel {

    use HasFactory;

    protected $table = 'v_qtd_new_users_last_seven_days';
    public $incrementing = false;
    public $timestamps = false;

}
