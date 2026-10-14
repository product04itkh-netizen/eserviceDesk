<?php

namespace App\Models\Item;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\ItemAttribute;
use App\Models\Auth\Traits\Method\ItemMethod;
use App\Models\Category\Category;
use App\Models\Purchase\Purchases_detail;

class Item extends Model
{
  use ItemAttribute,
      ItemMethod;

  protected $fillable = [
      'name','category_id', 'saleprice','description'
  ];

  public function category()
  {
    return $this->hasOne(Category::class,'id','category_id');
  }

/*
  public function purchase_detail(){
    return $this->belongsTo(Item::class);
  }
*/
}
