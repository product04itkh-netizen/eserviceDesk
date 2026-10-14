<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\CustomerAttribute;
use App\Models\Auth\Traits\Method\CustomerMethod;

class Customer extends Model
{

  use CustomerAttribute,
      CustomerMethod;

  protected $fillable = [
      'first_name', 'last_name','emails', 'phone','status','address'
  ];

  public function getFullNameAttribute($value)
    {
       return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }



}
