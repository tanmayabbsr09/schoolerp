<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HostelAllot extends Model
{
    protected $fillable = ['hostels_id', 'room_no_id', 'admission_class', 'admission_id', 'date_of_allotment', 'date_of_disallotment', 'remarks'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function admission()
    {
        return $this->hasOne('App\Admission', 'id', 'admission_id');
    }
    public function hostel_room()
    {
        return $this->hasOne('App\HostelRoom', 'id', 'room_no_id');
    }
    public function hostel()
    {
        return $this->hasOne('App\Hostel', 'id', 'hostels_id');
    }
}
