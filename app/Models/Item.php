<?php

namespace Blekan\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {
	protected $table = 'items';
	
	protected $fillable = [ 
			'title',
			'title_en',
			'description',
			'description_en',
			'price' 
	];
	public function setItem($title, $title_en, $description, $description_en, $price) {
		$this->update ( [ 
				'title' => $title,
				'title_en' => $title_en,
				'description' => $description,
				'description_en' => $description_en,
				'price' => $price 
		] );
	}
	public function category() {
		return $this->belongsTo ( 'Blekan\Models\Category' );
	}
}
