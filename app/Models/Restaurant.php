<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
             'name',
            'description',
            'lowest_price',
             'highest_price',
             'postal_code' ,
             'address' ,
             'opening_time' ,
             'closing_time' ,
             'seating_capacity'
    ];

    public function category() {
        return $this->belongsToMany(Restaurant::class,'category_restaurant')->withTimestamps();
    }

}

