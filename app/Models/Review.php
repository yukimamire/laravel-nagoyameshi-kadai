<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Restaurant;
use App\Models\User;

class Review extends Model
{
    use HasFactory;

    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
        }

    public function user() {
        return $this->belongsTo(User::class);
    }
    }

