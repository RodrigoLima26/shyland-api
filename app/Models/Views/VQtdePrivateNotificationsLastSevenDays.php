<?php


namespace App\Models\Views;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VQtdePrivateNotificationsLastSevenDays extends BaseModel {

    use HasFactory;

    protected $table = 'v_qtd_private_messages_last_seven_days';
    public $incrementing = false;
    public $timestamps = false;

}
