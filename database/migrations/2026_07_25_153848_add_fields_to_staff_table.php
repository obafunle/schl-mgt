<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->renameColumn('emergency_contact_name', 'next_of_kin_name');
            $table->renameColumn('emergency_contact_phone', 'next_of_kin_phone');
            $table->renameColumn('emergency_contact_relationship', 'next_of_kin_relationship');
        });
    }

    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->renameColumn('next_of_kin_name', 'emergency_contact_name');
            $table->renameColumn('next_of_kin_phone', 'emergency_contact_phone');
            $table->renameColumn('next_of_kin_relationship', 'emergency_contact_relationship');
        });
    }
};
