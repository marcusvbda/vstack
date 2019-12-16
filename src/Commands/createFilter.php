<?php

namespace marcusvbda\vstack\Commands;

use Illuminate\Console\Command;

class createFilter extends Command
{
    protected $signature = 'vstack:make-filter {resource} {name} {type} {index} {label}';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $data = $this->arguments();
        $resource = $data["resource"];
        $type = $data["type"];
        $index = $data["index"];
        $label = $data["label"];
        $name = $data["name"];
        $totalSteps = 1;
        $bar = $this->output->createProgressBar($totalSteps);
        $this->createFilter($bar, $resource, $type, $index,$label,$name);
        $bar->finish();
    }

    private function createFilter($bar, $resource, $type, $index,$label,$name)
    {
        $bar->start();
        $dir = app_path("\\Http\\Filters\\".$resource);
        $filter_path = $dir . "\\" . $name . ".php";
        $content =
            '<?php 
namespace App\Http\Filters\\'.$resource.';
use  marcusvbda\vstack\Filter;

class ' . $name . ' extends Filter
{
    public $component   = "'.$type.'";
    public $label       = "'.$label.'";
    public $index       = "'.$index.'";
    public $placeholder = "";

    public function calculate($query, $value)
    {
        //filter logic here...
        return $query;
    }
}'; 
        $this->makeDir($dir);   
        file_put_contents($filter_path, $content);
        $bar->advance();
        echo "\ncreated filter $filter_path.php\n";
    }


    private function makeDir($dir)
    {
        if (!is_dir($dir)) mkdir($dir, 777, true);
    }
}
