<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['voucher_no', 'amount', 'flow_type', 'employee_id', 'pay_to', 'voucher_date', 'voucher_details', 'payment_mode'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
}