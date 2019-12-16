<?php

namespace marcusvbda\vstack\Commands;

use Illuminate\Console\Command;

class createResource extends Command
{
    protected $signature = 'vstack:make-resource {resource} {model} {table}';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $data = $this->arguments();
        $resource = $data["resource"];
        $table = $data["table"];
        $model = $data["model"];
        $totalSteps = 2;
        $bar = $this->output->createProgressBar($totalSteps);
        $this->createModel($bar, $table, $model);
        $this->createResource($bar, $resource, $table, $model);
        $bar->finish();
    }

    private function createResource($bar, $resource, $table, $model)
    {
        $bar->start();
        $dir = app_path("\\Http\\Resources");
        $resource_path = $dir . "\\" . $resource . ".php";
        $content =
            '<?php
namespace App\Http\Resources;
use marcusvbda\vstack\Resource;
class ' . $resource . ' extends Resource
{
    public $model = \App\Http\Models\\' . $model . '::class;
}';
        $this->makeDir($dir);
        file_put_contents($resource_path, $content);
        $bar->advance();
        echo "\ncreated resource $resource_path.php\n";
    }

    private function createModel($bar, $table, $model)
    {
        $bar->start();
        $dir = app_path("\\Http\\Models");
        $model_path = $dir . "\\" . $model . ".php";
        $content =
            '<?php
namespace App\Http\Models;
use marcusvbda\vstack\Models\DefaultModel;
class ' . $model . ' extends DefaultModel
{
    protected $table = "' . $table . '";
    // public $cascadeDeletes = [];
    // public $restrictDeletes = [];
    // public static function hasTenant() //default true
    // {
    //     return true;
    // }
}';
        $this->makeDir($dir);
        file_put_contents($model_path, $content);
        $bar->advance();
        echo "\ncreated model $model_path.php\n";
    }

    private function makeDir($dir)
    {
        if (!is_dir($dir)) mkdir($dir, 777, true);
    }
}
