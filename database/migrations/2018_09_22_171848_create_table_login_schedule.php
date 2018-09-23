<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLoginSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE appointment_details ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE appointments ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE budget_details ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE budgets ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE call_logs ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE call_status_history ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE email_attaches ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE email_spooler ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE expenses ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE migrations ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE notes ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE notifications ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE password_resets ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE patient_history ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE patient_references ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE patients ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE payments ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE product_user ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE products ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE question_attaches ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE questions ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE ray_x ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE suppliers ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE supplies ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE supply_request_details ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE supply_requests ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ENGINE = InnoDB");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE weekdays ENGINE = InnoDB");

        Schema::create('login_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('weekday_id')->unsigned();
            $table->time('time_start');
            $table->time('time_end');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('weekday_id')->references('id')->on('weekdays');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_schedules');
    }
}
