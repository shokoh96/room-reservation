<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $appends = ['time_step_values'];

    // Accessor
    public function getTimeStepValuesAttribute()
    {  // 1時間の間に予約できる「分」を取得する
        $time_step_values = [];
        $count = 60 / $this->time_steps;

        for ($i = 0; $i < $count; $i++) {
            $time_step_values[] = $this->time_steps * $i;
        }

        return $time_step_values;
    }
}