<?php


namespace App\Models\Views;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VQtdeMissionsCompletedByType extends BaseModel {

    use HasFactory;

    protected $table = 'v_qtd_missions_complete_by_type';
    public $incrementing = false;
    public $timestamps = false;

}
