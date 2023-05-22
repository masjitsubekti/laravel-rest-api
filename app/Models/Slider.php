<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    protected $table="slider";
    protected $fillable = [
        'title', 
        'deskripsi', 
        'color_title', 
        'color_deskripsi',
        'image', 
        'is_active', 
        'status'
    ];
}
