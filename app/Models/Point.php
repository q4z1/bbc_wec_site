<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Game;

class Point extends Model
{
    use HasFactory;

    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }
}
