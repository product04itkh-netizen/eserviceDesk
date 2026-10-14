<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

class Service_rma extends Model
{
  protected $fillable = [
      'category_id', 'item_name','quantity','purchaseprice','sellingprice','payment','paydate','reference'
    ];

  public function service()
  {
    return $this->belongsToMany(Service::class)->withTimestamps();
  }
}
