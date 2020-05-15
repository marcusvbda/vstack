<?php

namespace marcusvbda\vstack\Commands;

use Illuminate\Console\Command;

class createFilter extends Command
{
    protected $signature = 'vstack:make-filter {resource} {name} {type}';
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
        $this->createFilter($bar, $resource, $type, $name);
        $bar->finish();
    }

    private function createFilter($bar, $resource, $type, $name)
    {
        $bar->start();
        $dir = app_path("/Http/Filters/" . $resource);
        $filter_path = $dir . "\\" . $name . ".php";
        $content =
            '<?php 
namespace App\Http\Filters\\' . $resource . ';
use  marcusvbda\vstack\Filter;

class ' . $name . ' extends Filter
{
    ' . (($type != 'custom-filter') ? ('
    public $component   = "' . $type . '";
    public $label       = "' . $name . '";
    public $placeholder = "";
    public $index = "' . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name)) . '";') : 'public $component   = "' . $type . '";
    public $label       = "' . $name . '";
    public $index       = "' . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name)) . '";
    ');


        if ($type == "select-filter") {
            $content .= '

    public function __construct()
    {
        $this->options[] = (Object) ["value"=>1,"label"=>"lorem ipsum"];
        parent::__construct();
    }';
        }

        if ($type == "custom-filter") {
            $content .= '

    public function __construct()
    {
        $this->element = "<input class=\'form-control\' v-model=\'filter.".$this->index."\'  @change=\'makeNewRoute\' />";
        parent::__construct();
    }';
        }
        $content .= '
    
    public function apply($query, $value)
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
