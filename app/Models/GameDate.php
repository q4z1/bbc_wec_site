<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameDate extends Model
{
    use HasFactory;

    public function regs(){
        return $this->hasMany(Registration::class, 'game_date_id', 'id');
    }
}
