<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWeekDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekdays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('weekday', 15)->unique();
            $table->timestamps();
        });

        $weekday = new \App\Weekday();
        $weekday->weekday = 'monday';
        $weekday->save();

        $weekday = new \App\Weekday();
        $weekday->weekday = 'tuesday';
        $weekday->save();

        $weekday = new \App\Weekday();
        $weekday->weekday = 'wednesday';
        $weekday->save();

        $weekday = new \App\Weekday();
        $weekday->weekday = 'thursday';
        $weekday->save();

        $weekday = new \App\Weekday();
        $weekday->weekday = 'friday';
        $weekday->save();

        $weekday = new \App\Weekday();
        $weekday->weekday = 'saturday';
        $weekday->save();

        $weekday = new \App\Weekday();
        $weekday->weekday = 'sunday';
        $weekday->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weekdays');
    }
}
