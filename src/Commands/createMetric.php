<?php

namespace marcusvbda\vstack\Commands;

use Illuminate\Console\Command;

class createMetric extends Command
{
    protected $signature = 'vstack:make-metric {resource} {name} {type}';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $data = $this->arguments();
        $resource = $data["resource"];
        $type = $data["type"];
        $name = $data["name"];
        $totalSteps = 1;
        $bar = $this->output->createProgressBar($totalSteps);
        $this->createMetric($bar, $resource, $type,$name);
        $bar->finish();
    }

    private function createMetric($bar, $resource, $type,$name)
    {
        $bar->start();
        $dir = app_path("\\Http\\Metrics\\".$resource);
        $metric_path = $dir . "\\" . $name . ".php";
        $content =
            '<?php 
namespace App\Http\Metrics\\'.$resource.';
use  marcusvbda\vstack\Metric;
use Illuminate\Http\Request;

class ' . $name . ' extends Metric
{
    public $type   = "'.$type.'";';
    if(in_array($type,["group-graph","simple-counter","trend-graph","custom-content","bar-chart"])) {
        $content .='
    public function calculate(Request $request)
    {
        //return data or html content here...
        return ["lorem ipsum" => 12,"ipsum lorem" => 55];
    }
    
    //time to update content
    public function updateTime()
    {
        return 60; // seconds
    }
    ';
    }
    if(in_array($type,["simple-counter","trend-graph","bar-chart"])) {
        $content .='
    public function ranges()
    {
        return "date-interval";
        //return [];
    }';
    }

        $content.='

    //required and unique
    public function uriKey()
    {
        return "'.strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name)).'";
    }
}'; 
        $this->makeDir($dir);   
        file_put_contents($metric_path, $content);
        $bar->advance();
        echo "\ncreated metric $metric_path.php\n";
    }


    private function makeDir($dir)
    {
        if (!is_dir($dir)) mkdir($dir, 777, true);
    }
}
