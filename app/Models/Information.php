<?php

namespace Blekan\Models;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $table = 'information';

    protected $fillable = [
      'seats',
      'food_type',
      'food_type_en',
      'restaurant_type',
      'restaurant_type_en',
      'other_type',
      'other_type_en',
      'atmosphere',
      'atmosphere_en',
      'payment',
      'payment_en',
      'services',
      'services_en',
    ];

    public function setInformation($seats, $food_type, $food_type_en, $restaurant_type, $restaurant_type_en,
      $other_type, $other_type_en, $atmosphere, $atmosphere_en, $payment, $payment_en, $services, $services_en)
    {
        $this->update([
          'seats' => $seats,
          'food_type' => $food_type,
          'food_type_en' => $food_type_en,
          'restaurant_type' => $restaurant_type,
          'restaurant_type_en' => $restaurant_type_en,
          'other_type' => $other_type,
          'other_type_en' => $other_type_en,
          'atmosphere' => $atmosphere,
          'atmosphere_en' => $atmosphere_en,
          'payment' => $payment,
          'payment_en' => $payment_en,
          'services' => $services,
          'services_en' => $services_en,
        ]);
    }
}
