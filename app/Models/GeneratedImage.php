<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedImage extends Model
{
    protected $fillable = [
        'user_id',
        'prompt_id',      // Add this
        'file_name',      // Add this
        'positive_prompt',
        'model_used',
        'width',
        'height',
    ];
    public function user()
    {
    return $this->belongsTo(User::class);
    }
}
