<?php

namespace App\Models\Transfer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service\Service;
use App\Models\Vendor\Vendor;
use Carbon\Carbon;

class Transfer extends Model
{
  protected $table = 'transfers';

  protected $fillable = [
      'transferdate','returndate','cleardate','vendor_id','type','reason','cnprice','cnno'
  ];

  public function setTransferdateAttribute($input)
  {
      $this->attributes['transferdate'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }

  public function getTransferdateAttribute($input){
    if(empty($input))
      return null;
      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }

  public function vendor(){
      return $this->hasOne(Vendor::class,'id','vendor_id');
  }

  public function setReturndateAttribute($input)
  {
      if(empty($input))
        $this->attributes['returndate']=null;
      else
        $this->attributes['returndate'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }

  public function getReturndateAttribute($input){
    if(empty($input))
      return null;
      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }

  public function setCleardateAttribute($input)
  {
      if(empty($input))
            $this->attributes['cleardate']=null;
      else
            $this->attributes['cleardate'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }

  public function getCleardateAttribute($input){
    if(empty($input))
      return null;
      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }

  public function service(){
      return $this->belongsToMany(Service::class,'service_transfer');
  }
}
