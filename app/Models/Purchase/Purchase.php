<?php

namespace App\Models\Purchase;
use App\Models\Auth\Traits\Attribute\PurchaseAttribute;
use App\Models\Auth\Traits\Method\PurchaseMethod;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\Vendor;
use App\Models\Service\Service;
use Carbon\Carbon;

class Purchase extends Model
{
  use PurchaseAttribute,
      PurchaseMethod;

  protected $fillable = [
      'userid','date','rma','payment','paydate','note','vendor_id','service_id'
  ];

  public function setDateAttribute($input)
  {
      $this->attributes['date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }

  public function setPaydateAttribute($input)
  {
      if(empty($input))
        $this->attributes['paydate']=null;
      else
        $this->attributes['paydate'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }

  public function getPaydateAttribute($input)
  {
    if(empty($input))
      return null;

      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }

  public function getDtttAttribute(){
    return Carbon::createFromFormat('Y-m-d', $this->attributes['date'])->timestamp;
  }

  public function getDateAttribute($input)
  {
    if(empty($input))
      return null;

      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }

  public function purchase_detail()
  {
    return $this->hasMany(Purchases_detail::class);
  }

  public function vendor(){
    return $this->hasOne(Vendor::class,'id','vendor_id');
  }

  public function service(){
    return $this->hasOne(Service::class,'id','service_id');
  }

  // Kept for any other callers — receiptno() itself does a fresh query per
  // call, so PurchaseController::index() uses the eager-loaded service()
  // relation above instead rather than calling this in a row loop.
  public function receiptno($service_id){
    $service=Service::where('id',$service_id);
    if($service->count()<=0)
      return '';
    return $service->first()->receiptno;
  }

/*
  public function service(){
    return $this->hasOne(Service::class,'id','service_id');
  }
*/

}
