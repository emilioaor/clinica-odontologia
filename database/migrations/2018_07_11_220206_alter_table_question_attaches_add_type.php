<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableQuestionAttachesAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_attaches', function (Blueprint $table) {
            $table->addColumn('integer', 'type', ['default' => \App\QuestionAttach::TYPE_QUESTION]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question_attaches', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
