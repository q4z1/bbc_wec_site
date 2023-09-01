<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Player;

class Game extends Model
{
    use HasFactory;

    public function pos1()
    {
        return $this->hasOne(Player::class, 'id', 'pos1');
    }

    public function pos2()
    {
        return $this->hasOne(Player::class, 'id', 'pos2');
    }

    public function pos3()
    {
        return $this->hasOne(Player::class, 'id', 'pos3');
    }

    public function pos4()
    {
        return $this->hasOne(Player::class, 'id', 'pos4');
    }

    public function pos5()
    {
        return $this->hasOne(Player::class, 'id', 'pos5');
    }

    public function pos6()
    {
        return $this->hasOne(Player::class, 'id', 'pos6');
    }

    public function pos7()
    {
        return $this->hasOne(Player::class, 'id', 'pos7');
    }

    public function pos8()
    {
        return $this->hasOne(Player::class, 'id', 'pos8');
    }

    public function pos9()
    {
        return $this->hasOne(Player::class, 'id', 'pos9');
    }

    public function pos10()
    {
        return $this->hasOne(Player::class, 'id', 'pos10');
    }
}
