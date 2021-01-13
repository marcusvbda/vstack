<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceTagsRelation extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('resource_tags_relation', function (Blueprint $table) {
			$table->unsignedBigInteger('resource_tag_id');
			$table->string('model');
			$table->foreign('resource_tag_id')
				->references('id')
				->on('resource_tags');
			$table->integer('relation_id');
			$table->softDeletes();
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
		Schema::dropIfExists('resource_tags_relation');
	}
}
