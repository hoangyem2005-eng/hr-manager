<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_name',
        'stored_name',
        'file_path',
        'disk',
        'file_type',
        'mime_type',
        'file_size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
