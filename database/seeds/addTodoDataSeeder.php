<?php

use Illuminate\Database\Seeder;
use Modules\Todo\Entities\Board;
use Modules\Todo\Entities\Card;

class addTodoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Board::create([
        'name' => 'Daily Tasks',
      ]);
      Card::create([
        'name' => 'Daily Tasks',
        'board_id' => '1'
      ]);
    }
}
