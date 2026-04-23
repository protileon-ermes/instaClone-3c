<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_url',
        'caption'
    ];

    // O dono do post
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento para o Passo 7 (Curtidas)
    public function likes()
    {
        // Usaremos hasMany porque um post tem muitas curtidas
        return $this->hasMany(Like::class);
    }

    // Relacionamento para o Passo 8 (Comentários)
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}