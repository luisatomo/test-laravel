<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyFilesTypesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('company_files_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 250)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->auditableWithDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('company_files_types');
    }
}
