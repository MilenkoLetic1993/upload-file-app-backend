<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Housing extends Model
{
    use HasFactory;

    const KAGGLE_SOURCE = 'kaggle';

    protected $fillable = ['date', 'area', 'average_price', 'code', 'houses_sold', 'number_of_crimes', 'borough_flag'];
}
