<?php

use Illuminate\Database\Seeder;

class ElevatorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('elevators')->insert([
        	'name' => 'Elevator 1',
        	'direction' => 'stand',
        	'current_floor' => 0,
        	'queue_down' => '',
        	'queue_up' => ''
        ]);
        DB::table('elevators')->insert([
        	'name' => 'Elevator 2',
        	'direction' => 'stand',
        	'current_floor' => 0,
        	'queue_down' => '',
        	'queue_up' => ''
        ]);
        DB::table('elevators')->insert([
        	'name' => 'Elevator 3',
        	'direction' => 'stand',
        	'current_floor' => 0,
        	'queue_down' => '',
        	'queue_up' => ''
        ]);
    }
}
