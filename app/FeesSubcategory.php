<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeesSubcategory extends Model
{
    protected $fillable = ['category_id', 'subcategory_name'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    /*public function category()
    {
        //return $this->hasOne('App\FeesCategory', 'id');
        return $this->hasOneThrough('App\FeesSubcategory', 'App\FeesCategory', 'category_id', 'subcategory_id');
    }*/
    
}
