<?php

namespace App\Models\Rmacode;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\RmacodeAttribute;
use App\Models\Auth\Traits\Method\RmacodeMethod;
use App\Models\Service\Service;

class Rmacode extends Model
{
    use RmacodeAttribute,
        RmacodeMethod;

    protected $fillable = [
        'userid','code'
    ];


    public function service()
    {
      return $this->belongsTo(Service::class,'id','rmacode');
    }
}
