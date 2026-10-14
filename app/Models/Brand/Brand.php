<?php

namespace App\Models\Brand;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\BrandAttribute;
use App\Models\Auth\Traits\Method\BrandMethod;
use App\Models\Service\Service;


class Brand extends Model
{
  use BrandAttribute,
      BrandMethod;

  protected $fillable = [
      'name','description'
  ];


  public function service()
  {
    return $this->belongsToMany(Service::class,'service_brand');
  }

}
