<?php

namespace App\Models\Auth;

use App\Models\Auth\Traits\Scope\UserScope;
use App\Models\Auth\Traits\Method\UserMethod;
use App\Models\Auth\Traits\Attribute\UserAttribute;
use App\Models\Auth\Traits\Relationship\UserRelationship;
use App\Models\Service\Service;
use App\Models\Service\Enginner_busy;

/**
 * Class User.
 */
class User extends BaseUser
{
    use UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope;

    public function enginner_busy(){
      return $this->hasOne(Enginner_busy::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class,'assignto');
    }
}
