<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeesMaster extends Model
{
    protected $fillable = ['category_id', 'subcategory_id', 'class', 'amount', 'remark', 'is_mandatory'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    /*public function category()
    {
        //return $this->hasOne('App\FeesCategory', 'id');
        return $this->hasOne('App\FeesSubcategory', 'id');
    }*/



}
