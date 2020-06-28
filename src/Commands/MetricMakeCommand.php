<?php

namespace marcusvbda\vstack\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\{InputOption, InputArgument};

class MetricMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'vstack:make-new-metric';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new Metric class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Metric';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = $stub ?? '/stubs/metric.plain.stub';

        return __DIR__ . $stub;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = trim($this->argument('name')) ?? trim($this->argument('resource')) . "Metrics";
        dd($name);
        return \Str::plural($name);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Metrics';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base resource import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $resourceNamespace = $this->getNamespace($name);

        $replace = [];

        $replace = $this->buildReplacements($replace);
        dd($replace);
        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildReplacements(array $replace)
    {
        $modelClass = \Str::singular($this->getNameInput());
        dd($modelClass);
        $modelName = class_basename($modelClass);
        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => $modelName,
            'DummyResourceName' => \Str::plural($modelName),
            'DummyModelVariable' => lcfirst($modelName),
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'resource', InputArgument::REQUIRED, 'The name of the Resource class'
            ],
            [
                'name', InputArgument::OPTIONAL, 'The name of the Metric class'
            ]
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the resource already exists']
        ];
    }
}
