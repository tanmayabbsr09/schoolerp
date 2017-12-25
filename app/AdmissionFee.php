<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdmissionFee extends Model
{
    protected $fillable = ['student_id', 'academic_year', 'academic_month', 'fees_master_id'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
