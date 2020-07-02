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
        $this->createMetric($bar, $resource, $type, $name);
        $bar->finish();
    }

    private function createMetric($bar, $resource, $type, $name)
    {
        $bar->start();
        $dir = app_path("/Http/Metrics/" . $resource);
        $metric_path = $dir . "/" . $name . ".php";
        $index = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        $content = "";
        switch($type)
        {
            case 'custom-content' :
                $content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/metric/_new_custom_metric_.example"));
            break;
            case 'select-filter' :
                $content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/metric/_new_select_filter_.example"));
            break;
            case 'group-chart' :
                $content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/metric/_new_group_chart_metric_.example"));
            break;
            case 'tend-counter' :
                $content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/metric/_new_trend_counter_metric_.example"));
            break;
            case 'bar-chart' :
                $content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/metric/_new_bar_chart_metric_.example"));
            break;
            case 'trend-chart' :
                $content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/metric/_new_bar_chart_metric_.example"));
            break;
        }
        $content = preg_replace('/\_NAME_\b/', $name, $content);
        $content = preg_replace('/\_TYPE_\b/', $type, $content);
        $content = preg_replace('/\_INDEX_\b/', $index, $content);
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
