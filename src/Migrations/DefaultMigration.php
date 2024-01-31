<?php

namespace marcusvbda\vstack\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DefaultMigration extends Migration
{
    protected function initializeTable(Blueprint $table): Blueprint
    {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_unicode_ci';
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        return $table;
    }

    protected function createVstacksDefaultTables(): void
    {
        Schema::create('resource_configs', function (Blueprint $table) {
            $table = $this->initializeTable($table);
            $table->string('name')->nullable();
            $table->string('resource');
            $table->string('config');
            $table->jsonb('data');
        });

        Schema::create('resource_tags', function (Blueprint $table) {
            $table = $this->initializeTable($table);
            $table->string('name');
            $table->string('color');
            $table->string('model');
            $table = $this->createRelationship($table, 'tenant_id', 'tenants');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('resource_tags_relation', function (Blueprint $table) {
            $table = $this->initializeTable($table);
            $table->string('model');
            $table = $this->createRelationship($table, 'resource_tag_id', 'resource_tags');
            $table->integer('relation_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    protected function createRelationship(Blueprint $table, String $columnName, String $tableName, $options = []): Blueprint
    {
        $table->unsignedBigInteger($columnName)->nullable(data_get($options, 'nullable', false));
        $table->foreign($columnName)
            ->references(data_get($options, 'references', 'id'))
            ->on($tableName)
            ->onDelete(data_get($options, 'onDelete', 'cascade'));
        return $table;
    }

    protected function dropAllTables(): void
    {
        $dbName = config('database.connections.mysql.database');
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            Schema::dropIfExists($table["Tables_in_$dbName"]);
            echo 'Table ' . $table["Tables_in_$dbName"] . ' Droped. <br>';
        }
    }

    public function createLaravelsDefaultTables()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table = $this->initializeTable($table);
            $table->string('queue')->index();
            $table->string('uuid')->unique();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->timestamps();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table = $this->initializeTable($table);
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function up()
    {
        // 
    }

    public function down()
    {
        //    
    }
}
