<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerAward extends Model
{
    use HasFactory;

    public function player(){
        return $this->hasOne(Player::class, 'id', 'player_id');
    }

    public function award(){
        return $this->hasOne(Award::class, 'id', 'award_id');
    }
}
