#Vstack
######framework para facilitar, agilizar e trazer maior integridade nos cruds de seu sistema

## Comandos
Instalação
```
//instale
composer require marcusvbda/vstack
//adicione no providers config\app.php
marcusvbda\vstack\vStackServiceProvider::class

//crie um template templates.admin

//adicione no app.scss
@import "./vendor/marcusvbda/vstack/src/Assets/scss/autoload.scss";

//adicione no app.js
require('../../../vendor/marcusvbda/vstack/src/Assets/js/components/autoload')

php artisan vendor:publish
```

para criar um novo resource, você precisa executar o comando especificando o do resource, model e tabela, respectivamente
```
php artisan vstack:make-resource {resource} {model} {table}
```

para criar um filtro para o resource
```
php artisan vstack:make-filter {resource} {name} {type}
```
os tipos de filtro text-filter, select-filter, check-filter, date-filter, rangedate-filter



para criar um resource card metric
```
php artisan vstack:make-metric {resource} {name} {type}
```
os tipos de metrics custom-content, group-chart, trend-counter, bar-chart, trend-chart




exemplo de um resource COMPLETO
```
<?php

namespace App\Http\Models;

use marcusvbda\vstack\Models\DefaultModel;

class Car extends DefaultModel
{
    protected $table = "cars";
    public $cascadeDeletes = ["images"];
    
    // public $restrictDeletes = [];
    // public static function hasTenant() //default true
    // {
    //     return true;
    // }

    public $appends = ["f_created_at", "f_active", "last_update"];

    protected  $casts = [
        "active" => "boolean"
    ];

    public function getFActiveAttribute()
    {
        return $this->active ? '<span class="badge badge-primary">Ativo</span>' : '<span class="badge badge-danger">Inativo</span>';
    }

    public function getLastUpdateAttribute()
    {
        if (!$this->created_at) return;
        return $this->created_at->diffForHumans();
    }

    public function getFCreatedAtAttribute()
    {
        if (!$this->created_at) return;
        return @$this->created_at->format("d/m/Y - H:i:s");
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function colors()
    {
        return $this->morphMany(Color::class,"model");
    }

    public function images()
    {
        return $this->morphMany(Image::class,"model");
    }
}
```

para executar as filas do vstack
```
php artisan queue:work --queue=mail,resource-import,alert-broadcasts
```
