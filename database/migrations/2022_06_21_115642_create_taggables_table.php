<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaggablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taggables', function (Blueprint $table) {
            $table->unsignedBigInteger("tag_id");
            $table->unsignedInteger("taggable_id");
            $table->string("taggable_type");

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags');

            $table->index(['taggable_id', 'taggable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taggables', function (Blueprint $table) {
            $table->dropForeign('taggables_tag_id_foreign');
            $table->dropColumn('tag_id');
        });
        Schema::dropIfExists('taggables');
    }
}
