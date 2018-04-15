<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\User();
        $user->public_id = 'ADM' . time();
        $user->username = 'admin';
        $user->name = 'Admin';
        $user->password = bcrypt('123456');
        $user->level = \App\User::LEVEL_ADMIN;
        $user->save();

        $user = new \App\User();
        $user->public_id = 'DOC' . time();
        $user->username = 'doctor';
        $user->name = 'Doctor';
        $user->password = bcrypt('123456');
        $user->level = \App\User::LEVEL_DOCTOR;
        $user->save();

        $user = new \App\User();
        $user->public_id = 'SEC' . time();
        $user->username = 'secretary';
        $user->name = 'Secretary';
        $user->password = bcrypt('123456');
        $user->level = \App\User::LEVEL_SECRETARY;
        $user->save();

        $user = new \App\User();
        $user->public_id = 'ASS' . time();
        $user->username = 'assistant';
        $user->name = 'Assistant';
        $user->password = bcrypt('123456');
        $user->level = \App\User::LEVEL_ASSISTANT;
        $user->save();
    }
}
