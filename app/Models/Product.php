<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  // <-- энэ мөрийг нэмэх

class Product extends Model
{
    use HasFactory, SoftDeletes;  // <-- SoftDeletes нэмэх

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'is_active',
    ];
}
