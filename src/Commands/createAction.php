<?php

namespace marcusvbda\vstack\Commands;

use Illuminate\Console\Command;

class createAction extends Command
{
	protected $signature = 'vstack:make-action {resource} {name}';
	public function __construct()
	{
		parent::__construct();
	}
	public function handle()
	{
		$data = $this->arguments();
		$resource = $data["resource"];
		$name = $data["name"];
		$totalSteps = 1;
		$bar = $this->output->createProgressBar($totalSteps);
		$this->createAction($bar, $resource, $name);
		$bar->finish();
	}

	private function createAction($bar, $resource, $name)
	{
		$bar->start();
		$dir = app_path("/Http/Actions/" . $resource);
		$path = $dir . "\\" . $name . ".php";
		$index = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
		$content = file_get_contents(base_path("vendor/marcusvbda/vstack/src/Commands/examples/_new_action_.example"));
		$content = preg_replace('/\_RESOURCE_NAME_\b/', $resource, $content);
		$content = preg_replace('/\_ACTION_NAME\b/', $name, $content);
		$content = preg_replace('/\_INDEX_\b/', $index, $content);
		$this->makeDir($dir);
		file_put_contents($path, $content);
		$bar->advance();
		echo "\ncreated action $path.php\n";
	}


	private function makeDir($dir)
	{
		if (!is_dir($dir)) mkdir($dir, 777, true);
	}
}