<?php

namespace App\Models\Problem;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\ProblemAttribute;
use App\Models\Auth\Traits\Method\ProblemMethod;
use App\Models\Service\Service;

class Problem extends Model
{
  use ProblemAttribute,
      ProblemMethod;

  protected $fillable = [
      'problem', 'description'
  ];

  public function service()
  {
      return $this->belongsToMany(Service::class);
  }

  public function services()
  {
      return $this->hasMany(Service::class);
  }

  public function group_problem()
  {
    return $this->belongsToMany(Groupproblem::class);
  }

}
