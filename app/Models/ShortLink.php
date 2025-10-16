<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_url',
        'code',
        'clicks'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateUniqueCode($length = 6)
    {
        do {
            $code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
