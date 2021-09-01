<?php

use App\Models\Slot;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('repeat_after_days');
            $table->timestamps();
        });

        Slot::create(['repeat_after_days' => 0]);
        Slot::create(['repeat_after_days' => 1]);
        Slot::create(['repeat_after_days' => 4]);
        Slot::create(['repeat_after_days' => 7]);
        Slot::create(['repeat_after_days' => 30]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slots');
    }
}
