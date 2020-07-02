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
        $index = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        switch($type)
        {
            case 'custom-filter' :
                $content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/filter/_new_custom_filter_.example"));
            break;
            case 'select-filter' :
                $content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/filter/_new_select_filter_.example"));
            break;
            default :
                $content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/filter/_new_default_filter_.example"));
            break;
        }
        $content = preg_replace('/\_FILTER_NAME_\b/', $name, $content);
        $content = preg_replace('/\_TYPE_\b/', $type, $content);
        $content = preg_replace('/\_LABEL_\b/', $name, $content);
        $content = preg_replace('/\_INDEX_\b/', $index, $content);
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
