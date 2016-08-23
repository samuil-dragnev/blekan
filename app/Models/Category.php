<?php

namespace Blekan\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
      'title',
      'title_en',
      'description',
      'description_en',
      'oreder_num',
      'is_takeaway',
    ];

    public function setCategory($title, $title_en, $description, $description_en, $oreder_num, $is_takeaway)
    {
        $this->update([
          'title' => $title,
          'title_en' => $title_en,
          'description' => $description,
          'description_en' => $description_en,
          'oreder_num' => $oreder_num,
          'is_takeaway' => $is_takeaway,
        ]);
    }

    public function items()
    {
        return $this->hasMany('Blekan\Models\Item');
    }

    protected $casts = [
        'is_takeaway' => 'boolean',
    ];
}
