<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeesCategory extends Model
{
    protected $fillable = ['category_name'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    public function sub_category()
    {
        return $this->hasMany('App\FeesSubcategory', 'category_id');
    }
  
}
