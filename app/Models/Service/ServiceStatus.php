<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\ServiceAttribute;
use App\Models\Auth\Traits\Method\ServiceMethod;
use App\Models\Service\Service;
use App\Models\Auth\User;
use Carbon\Carbon;

class ServiceStatus extends Model
{
    protected $table = 'services_status';

    protected $fillable = ['userid','service_id','status'];

    public function service()
    {
      return $this->belongsToMany(Service::class)->withTimestamps();
    }

    public function getDtt3Attribute(){
      if(empty($this->attributes['created_at']))
         return null;

      return $this->attributes['created_at'];
    }

    public function user()
    {
        return $this->belongsTo(User::class,'userid','id');
    }
/*
    public function getCreatedAtAttribute($input){
      if(empty($input))
        return null;

      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
    }
    */
}
