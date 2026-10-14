<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\VendorAttribute;
use App\Models\Auth\Traits\Method\VendorMethod;

class Vendor extends Model
{
    use VendorAttribute,
        VendorMethod;

    protected $fillable = [
        'companyname', 'contactname', 'contacttitle', 'emails', 'phone', 'status', 'address'
    ];
}
