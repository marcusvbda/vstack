<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use marcusvbda\vstack\Migrations\DefaultMigration;

class CreateResourceTags extends DefaultMigration
{
    public function up()
    {
        $this->createVstacksDefaultTables();
    }

    public function down()
    {
        // 
    }
}
