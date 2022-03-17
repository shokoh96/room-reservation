<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time_steps = [15, 30, 60];

        foreach ($time_steps as $index => $time_step) {
            $no = $index + 1;

            $room = new Room();
            $room->name = '部屋' . $no;
            $room->time_steps = $time_steps[$index];
            $room->save();
        }
    }
}