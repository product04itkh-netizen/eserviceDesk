<?php

namespace App\Models\Shipping;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service\Service;
use Carbon\Carbon;

class Shipping extends Model
{
    protected $table = 'shippings';

    protected $fillable = [
        'shipdate','shipreference'
    ];

    public function setShipdateAttribute($input)
    {
        $this->attributes['shipdate'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
    }

    public function getShipdateAttribute($input){
      if(empty($input))
        return null;
        return Carbon::createFromFormat('Y-m-d', $input)
          ->format(config('app.date_format'));
    }

    public function service(){
        return $this->belongsToMany(Service::class,'service_shipping');
    }
}
