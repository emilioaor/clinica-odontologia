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
        \Illuminate\Support\Facades\DB::statement("
            ALTER TABLE appointment_details ENGINE = InnoDB;
            ALTER TABLE appointments ENGINE = InnoDB;
            ALTER TABLE budget_details ENGINE = InnoDB;
            ALTER TABLE budgets ENGINE = InnoDB;
            ALTER TABLE call_logs ENGINE = InnoDB;
            ALTER TABLE call_status_history ENGINE = InnoDB;
            ALTER TABLE email_attaches ENGINE = InnoDB;
            ALTER TABLE email_spooler ENGINE = InnoDB;
            ALTER TABLE expenses ENGINE = InnoDB;
            ALTER TABLE login_schedules ENGINE = InnoDB;
            ALTER TABLE migrations ENGINE = InnoDB;
            ALTER TABLE notes ENGINE = InnoDB;
            ALTER TABLE notifications ENGINE = InnoDB;
            ALTER TABLE password_resets ENGINE = InnoDB;
            ALTER TABLE patient_history ENGINE = InnoDB;
            ALTER TABLE patient_references ENGINE = InnoDB;
            ALTER TABLE patients ENGINE = InnoDB;
            ALTER TABLE payments ENGINE = InnoDB;
            ALTER TABLE product_user ENGINE = InnoDB;
            ALTER TABLE products ENGINE = InnoDB;
            ALTER TABLE question_attaches ENGINE = InnoDB;
            ALTER TABLE questions ENGINE = InnoDB;
            ALTER TABLE ray_x ENGINE = InnoDB;
            ALTER TABLE suppliers ENGINE = InnoDB;
            ALTER TABLE supplies ENGINE = InnoDB;
            ALTER TABLE supply_request_details ENGINE = InnoDB;
            ALTER TABLE supply_requests ENGINE = InnoDB;
            ALTER TABLE users ENGINE = InnoDB;
            ALTER TABLE weekdays ENGINE = InnoDB;
        ");

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
