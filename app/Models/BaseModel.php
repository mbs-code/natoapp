<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaseModel extends Model
{
  use HasFactory;

  protected $dates = [
      'published_at',
      'created_at',
      'updated_at',
      'deleted_at'
  ];
}
