<?php

namespace marcusvbda\vstack\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\{InputOption, InputArgument};

class ResourceMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'vstack:make-new-resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = $stub ?? '/stubs/resource.stub';

        return __DIR__ . $stub;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return \Str::plural(trim($this->argument('model')));
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Resources';
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

        $replace = $this->buildModelReplacements($replace);

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
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel(\Str::singular($this->getNameInput()));

        $this->makeModelFile($modelClass);

        $modelName = class_basename($modelClass);
        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => $modelName,
            'DummyResourceName' => \Str::plural($modelName),
            'DummyModelVariable' => lcfirst($modelName),
        ]);
    }

    protected function makeModelFile($modelClass)
    {
        if (!class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('vstack:make-model', ['name' => \Str::singular($modelClass)]);
            }
        }
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . "Http\Models\\" . $model;
        }

        return $model;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'The name of the Model class']
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
