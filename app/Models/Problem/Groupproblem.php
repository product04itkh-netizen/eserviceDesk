<?php

namespace App\Models\Problem;

use Illuminate\Database\Eloquent\Model;
use App\Models\Problem\Problem;

class Groupproblem extends Model
{
  protected $table = 'group_problems';

  public function problem()
  {
    return $this->belongsToMany(Problem::class);
  }

}
