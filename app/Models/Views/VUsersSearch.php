<?php


namespace App\Models\Views;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VUsersSearch extends BaseModel {

    use HasFactory;

    protected $table = 'v_users_search';
    public $incrementing = false;
    public $timestamps = false;

}
