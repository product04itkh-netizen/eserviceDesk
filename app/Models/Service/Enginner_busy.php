<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;
use App\Models\Service\Service;

class Enginner_busy extends Model
{
  protected $table = 'enginner_busys';

  protected $fillable = [
      'userid', 'user_id','busy_limit'
    ];

    public function user()
        {
            return $this->belongsTo(User::class);
        }

    public function engineer()
            {
                return $this->hasMany(Service::class,'assignto');
            }
}
