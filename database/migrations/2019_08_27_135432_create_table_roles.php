<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 30);
            $table->timestamps();
        });

        Schema::create('user_role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles');
        });

        $roles = ['admin', 'doctor', 'secretary', 'assistant', 'sell_manager'];

        foreach ($roles as $r) {
            $role = new \App\Role();
            $role->code = $r;
            $role->save();
        }

        $roles = \App\Role::all();
        $users = \App\User::query()->withTrashed()->get();
        /** @var \App\User $user */
        foreach ($users as $user) {
            if ($user->level === 1) {
                $code = 'admin';
            } elseif ($user->level === 2) {
                $code = 'doctor';
            } elseif ($user->level === 3) {
                $code = 'secretary';
            } elseif ($user->level === 4) {
                $code = 'assistant';
            } elseif ($user->level === 5) {
                $code = 'sell_manager';
            } else {
                continue;
            }
            foreach ($roles as $role) {
                if ($role->code === $code) {
                    $user->roles()->sync([$role->id]);
                    break;
                }
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('roles');
    }
}
