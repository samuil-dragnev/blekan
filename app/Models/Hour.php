<?php

namespace Blekan\Models;

use Illuminate\Database\Eloquent\Model;

class Hour extends Model
{
    protected $table = 'hours';

    protected $fillable = [
      'value',
      'value_en',
    ];
}
