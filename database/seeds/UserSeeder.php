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
        $user->username = 'user';
        $user->name = 'User Test';
        $user->password = bcrypt('123456');
        $user->save();
    }
}
