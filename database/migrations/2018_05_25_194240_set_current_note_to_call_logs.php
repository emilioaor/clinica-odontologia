<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetCurrentNoteToCallLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Asigna a la llamada la description igual a la nota del ultimo
        // estatus asignado
        $callLogs = \App\CallLog::all();

        foreach ($callLogs as $call) {

            foreach ($call->statusHistory as $statusHistory) {
                $call->description = $statusHistory->note;
            }

            $call->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Asigna a la llamada la description igual a la nota del primer
        // estatus asignado
        $callLogs = \App\CallLog::all();

        foreach ($callLogs as $call) {

            foreach ($call->statusHistory as $statusHistory) {
                $call->description = $statusHistory->note;
                break;
            }

            $call->save();
        }
    }
}
