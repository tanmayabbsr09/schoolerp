<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['admission_class', 'event_name', 'first_position', 'second_position', 'third_posiiton', 'remark'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
