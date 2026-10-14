<?php

namespace App\Models\SaleOrder;
use App\Models\Auth\Traits\Attribute\SaleAttribute;
use App\Models\Auth\Traits\Method\SaleMethod;
use App\Models\Customer\Customer;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SaleOrder extends Model
{
  use SaleAttribute,
      SaleMethod;

      protected $fillable = [
          'userid','date','ref','payment','paydate','note','customer_id'
      ];

      public function customer()
      {
        return $this->hasOne(Customer::class,'id','customer_id');
      }

      public function setDateAttribute($input)
      {
          $this->attributes['date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
      }

      public function getDtttAttribute(){
        return Carbon::createFromFormat('Y-m-d', $this->attributes['date'])->timestamp;
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

      public function getDateAttribute($input)
      {
        if(empty($input))
          return null;

          return Carbon::createFromFormat('Y-m-d', $input)
            ->format(config('app.date_format'));
      }

      public function saleorder_detail(){
          return $this->hasMany(SaleOrder_detail::class,'soid');
      }

}
