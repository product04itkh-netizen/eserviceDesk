<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item\Item;

class Purchases_detail extends Model
{
    // item_id was missing here originally, so update()'s mass-assignment of
    // it was silently dropped. item_name/saleprice were removed entirely —
    // confirmed against a real schema dump that neither ever existed as a
    // column; New Purchase now uses the same catalog select as Edit.
    protected $fillable = ['purchase_id','userid','category_id','item_id','quantity','purchaseprice'];

    public function purchase(){

      return $this->belongsTo(Purchase::class);
    }

    public function items(){
      return $this->hasOne(Item::class,'id','item_id');
    }


/*
    public function itemname_name($item_id){
      $item=Item::where('id',$item_id);
      if($item->count()<=0)
        return 'N/A';

      return ucfirst($item->first()->name);
    }
*/
}
