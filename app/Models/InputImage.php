<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputImage extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'path',
        'mime_type',
        'source_type',
        'preprocessor',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
