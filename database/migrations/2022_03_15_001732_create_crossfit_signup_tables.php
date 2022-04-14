<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrossfitSignupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('crossfit_companies')) {
            Schema::create('crossfit_companies', function (Blueprint $table) {
                $table->id();

                $table->string('legal_business_entity');
                $table->string('affiliate_name');
                $table->string('location_address');
                $table->string('location_city');
                $table->string('location_state');
                $table->string('location_zip');
                $table->double('membership_rate');
                $table->string('source');

                $table->timestamps();
            });

            Schema::create('crossfit_contacts_information', function (Blueprint $table) {
                $table->id();

                $table->bigInteger('crossfit_company_id');
                $table->string('email');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('phone');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crossfit_contact_information');
        Schema::dropIfExists('crossfit_companies');
    }
}
