<?php

namespace Blekan\Models;

use Illuminate\Database\Eloquent\Model;

class Slideshow extends Model
{
    protected $table = 'slideshow';

    protected $fillable = [
      'src',
      'description',
    ];
}
