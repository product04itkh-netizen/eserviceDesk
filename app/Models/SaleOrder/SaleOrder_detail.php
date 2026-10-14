<?php

namespace App\Models\SaleOrder;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item\Item;

class SaleOrder_detail extends Model
{
  protected $fillable = ['soid','userid','category_id','item_id','quantity','saleprice'];

  public function salleorder(){

    return $this->belongsTo(SaleOrder::class,'soid');
  }

  public function items(){
    return $this->hasOne(Item::class,'id','item_id');
  }

  public function find_stockable($item_id){
    // Item::find() already returns a resolved model or null — ->count()
    // on it is invalid (it's not a query builder); this crashed on every
    // real item.
    $item=Item::find($item_id);
    if(!$item)
      return 'N/A';

    return $item->stockable;
  }

}
