<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\CategoryAttribute;
use App\Models\Auth\Traits\Method\CategoryMethod;
use App\Models\Item\Item;

class Category extends Model
{
  use CategoryAttribute,
      CategoryMethod;

  protected $fillable = [
      'name', 'description'
  ];

/*
  public function item()
  {
    return $this->belongsTo(Item::class,'id','category_id');
  }
*/

}
