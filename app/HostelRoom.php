<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    protected $fillable = ['hostels_id', 'room_no', 'floor_no', 'amenities', 'total_beds', 'avail_beds', 'price_per_bed'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
