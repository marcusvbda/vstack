<?php

namespace marcusvbda\vstack\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand as _ModelMakeCommand;

class ModelMakeCommand extends _ModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'vstack:make-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/model.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
        $replace = [
            "DummyModelTable" => \Str::snake(\Str::pluralStudly(class_basename($this->argument('name'))))
        ];
        return str_replace(array_keys($replace), array_values($replace), $stub);
    }
}
