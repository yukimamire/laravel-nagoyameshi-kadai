<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\User;
use App\Models\Review;
use App\Models\Reservation;


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

     use Sortable;

    public $sortable = [
        'created_at',  // ここにソート可能なカラムを追加
        'lowest_price', 
        'rating',
    ];


    public function categories() {
        return $this->belongsToMany(Category::class,'category_restaurant')->withTimestamps();
    }

    public function regular_holidays() {
        return $this->belongsToMany(RegularHoliday::class)->withTimestamps();
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function ratingSortable($query, $direction) {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }

    public function reservation() {
        return $this->hasMany(Reservation::class);
    }

    public function popularSortable($query, $direction) {
        return $query->withCount('reservations')->orderBy('reservations_count','desc')->get();
    }
}

