<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends BaseModel {
    use HasFactory;

    /**
     * @param $data
     */
    public function store($data) {

        $this->name = @$data['name'];
        $this->contact = @$data['contact'];
        $this->message = @$data['message'];

        $this->save();
    }

    /**
     *
     */
    public function read() {
        $this->read = true;
        $this->save();
    }
}
