#Vstack
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
require('../../../vendor/marcusvbda/vstack/src/Assets/js/components/autoload');
VueApp.start();

php artisan vendor:publish
```

para criar um novo módulo de store vuex basta adicionar o seguinte trecho de código no seu app.js asntes do 'VueApp.start();'
```
VueApp.appendStoreModule("upsell", require("./stores/modules/module_name.module").default);

// Exemplo de modulo 
// const state = {};

// const getters = {};

// const mutations = {};

// export default {
//     namespaced: true,
//     state,
//     getters,
//     mutations,
// };
```

para criar um novo resource, você precisa executar o comando especificando o do resource, model e tabela, respectivamente
```
php artisan vstack:make-resource {resource} {model} {table}
```

para criar um filtro para o resource
```
php artisan vstack:make-filter {resource} {name} {type}
```
os tipos de filtro text-filter, select-filter, check-filter, date-filter, rangedate-filter, custom-filter



exemplo de um resource COMPLETO
```
<?php

namespace App\Http\Resources;

use marcusvbda\vstack\Resource;

use marcusvbda\vstack\Filters\{
    FilterByText,
    FilterByPresetDate,
    FilterByOption
}

use marcusvbda\vstack\Fields\{
    Text, 
    TextArea, 
    Check, 
    BelongsTo, 
    Upload,
};
use marcusvbda\vstack\Fields\Card;


use Auth;

class Cars extends Resource
{
    public $model = \App\Http\Models\Car::class;

    public function singularLabel()
    {
        return "Carro";
    }

    public function label()
    {
        return "Carros";
    }

    public function resultsPerPage()
	{
		return [12, 24, 60, 120];
	}

    public function globallySearchable()
    {
        return true;
    }

    public function icon()
    {
        return "el-icon-s-help";
    }

    public function menu()
    {
        return "Automotores";
    }
    
    public function menuIcon()
    {
        return "el-icon-s-promotion";
    }

    public function table()
    {
        return [
            "name"                  => ["label" => "Nome", "size" => "30%"],
            "brand->name"           => ["label" => "Marca", "sortable" => true, "sortable_index" => "brand_id"],
            "f_active"              => ["label" => "Ativo", "sortable" => true, "sortable_index" => "active"],
            "f_created_at"          => ["label" => "Data de Criação", "sortable_index" => "created_at"],
            "last_update"           => ["label" => "Ultima atualização", "sortable" => false],
        ];
    }    

    public function tableAfterRow($row)
	{
		return "<h1>Isso vai aparecer abaixo de cada linha da tabela de listagem de carros</h1>";
	}

    public function lenses()
    {
        return [
            "Apenas Ativos" => ["field" => "active", "value" => true],
            // "Apenas Inativos" => ["field" => "active", "value" => false],
            "Apenas Inativos" => ["field" => "active", "value" => false, "handler" => function ($q) {
				return $q->where("active",false);
			}],
        ];
    }

    public function fields()
    {
        return [
            new Card("<span class='el-icon-s-order mr-2'></span>Section Card 1",[
                new Text([
                    "label" => "Nome", 
                    "field" => "name", 
                    "required" => true,
                    "placeholder" => "Digite o nome aqui ...", 
                    "rules" => "required|max:255"
                ]),
                new TextArea([
                    "label" => "Descrição Simples", 
                    "field" => "simple_description",
                    "placeholder" => "Digite o texto aqui ...",
                ]),
                new Summernote([
                    "label" => "Descrição Completa", 
                    "field" => "description",
                    "placeholder" => "Digite o texto aqui ...",
                ]),
            ]),
            new Card("Section Card 2",[
                new Check([
                    "label" => "Ativo", 
                    "field" => "active"
                ])
            ]),
            new Card("Section Card 3",[
                new BelongsTo([
                    "label" => "Marca", "field" => "brand_id",
                    "placeholder" => "Selecione a marca",
                    "model" => \App\Http\Models\Brand::class,
                    "rules" => "required",
                ]),
            ]),
            new Card("Section Card 4",[
                new Upload([
                    "label" => "Imagens", 
                    "field" => "images",
                    "preview"  => true, //default false
                    "multiple" => true,
                    // "limit" => 2, //default 5
                    "accept" => "image/*",
                    // "list_type" => "picture-card"//(picture,picture-card) //default picture-card
                ]),
            ])
        ];
    }

    public function filters()
    {
        return [
            new FilterByText([
                "label" => "Nome",
                "field" => "name",
                // "index" => "email" caso não definido será considerado o column
            ]),
            new FilterByPresetDate([
                "label" => "Periodo de Compra",
                "field" => "created_at",
                // "index" => "created_at"  "email" caso não definido será considerado o column
            ]),
            new FilterByOption([
                "label" => "Categorias",
                "multiple" => true,
                "field" => "categoryId",
                "options" => Category::selectRaw("id as value,title as label")->get()
            ]),
            new FilterByOption([
                "label" => "Nivel",
                "field" => "level",
                "options" => [
                    ["value" => 1, "label" => '⭐'],
                    ["value" => 2, "label" => '⭐⭐'],
                    ["value" => 3, "label" => '⭐⭐⭐'],
                    ["value" => 4, "label" => '⭐⭐⭐⭐'],
                    ["value" => 5, "label" => '⭐⭐⭐⭐⭐'],
                ]
            ]),
        ];
    }

    public function search()
    {
        return ["name"];
    }


    // public function canViewList() //default true
    // public function canView()  //default true
    // public function canCreate() //default true
    // public function canExport() //default true
    // public function canImport() //default true
    // public function canUpdate() //default true
    // public function canDelete() //default true
}
```

para executar as filas do vstack
```
php artisan queue:work --queue=resource-import,resource-export,alert-broadcasts,event-broadcasts
```


# Tipo de inputs e como eles devem ser utilizados

Input de Texto
```
new Text([
    "label" => "Nome",
    //"type" => "text",  
    //"mask" => "## - ## - ##",  
    //"maxlength" => 255, 
    //"show_value_length" => true,
    "field" => "name", 
    "required" => true,
    //"disabled" => false,
    //"step" => '0.1',
    //"placeholder" => "Digite o nome aqui ...", 
    "rules" => "required|max:255",
    //"visible" => true,
    //"append" => " ... ",
    //"prepend" => " ... ",
    "eval" => "console.log('teste)"
])
```

Input de Checkbox
```
new Check([
    "label" => "Habilitado",
    //"active_color" => "green",  
    //"inactive_color" => 'red', 
    "field" => "enabled", 
    //"description" => "Digite o nome aqui ...", 
    //"eval" => "console.log('teste)"
])
```

Input de Checkbox
```
new Check([
    "label" => "Habilitado",
    "active_color" => "green",  
    "inactive_color" => 'red', 
    "field" => "enabled", 
    "placeholder" => "Digite o nome aqui ...", 
    "rules" => "required|max:255",
    "eval" => "console.log('teste)"
])
```

Input de componente Custom
```
new CustomComponent("<meu-componente :form='form' :data='data' :errors='errors' ></meu-componente>",[
    "field" => "address",
	"label" => "Endereço",
	"rules" => ["required"], 
])
```

Input Html Editor
```
new HtmlEditor(new HtmlEditor([
	"label" => "Conteúdo da Página",
	"rules" => ["required"],
	"field" => "body",
	"mode" => "webpage",
	"description" => "Oque será exibido ao acessar a página",
	// "blocks" => [
	// 	"hello_world_teste" => [
	// 		"label"  => "Hello World",
	// 		"attributes" =>  [
	// 			"class" => "gjs-fonts gjs-f-text"
	// 		],
	// 		"content" =>  "<h1>Hello World TESTE</h1>"
	// 	]
	// ]
])
```

Input de Radio
```
new Radio([
	"label" => "Tipo da Resposta",
	"description" => "O tipo da resposta que o operador recebeu para o contato realizado",
	"field" => "type",
	"required" => true,
	"default" => "neutral",
	"options" => [
		["value" => "neutral", "label" => "Resposta Neutra"],
		["value" => "negative", "label" => "Resposta Negativa"],
		["value" => "positive", "label" => "Resposta Positiva"]
	]
])
```

Input Resource Tree
```
new ResourceTree([
    'parent_resource' => 'cursos',
    'resource' => 'questoes',
    'relation' => 'questions',
    'foreign_key' => 'courseId',
    //'label_index' => 'title',
    //'template' => '<title-label></title-label>'
])
```

Input de Tags 
```
new Tags([
    'label' => 'Ids dos Pixels',
    'field' => 'values',
    'rules' => ['required_unless:provider,Google Ads'],
    'custom_message' => ['required_unless' => 'O campo pixels é obrigatório'],
    'description' => 'Ids dos pixels que deseja agrupar',
    'eval' => "v-if='form.provider != `Google Ads`'"
])
```

Input de TextArea
```
new TextArea([
	"label" => "Mensagem",
	"maxlength" => 200,
	"description" => "Máximo de 200 caracteres",
	"field" => 	"message",
	"rules" => ["max:200"]
])
```

Input de TextArea
```
new Upload([
    "label" => "Imagens", 
    "field" => "images",
    "preview"  => true, //default false
    "multiple" => true,
    // "limit" => 2, //default 5
    "accept" => "image/*",
    // "list_type" => "picture-card"//(picture,picture-card) //default picture-card
])
```



Comandos
```
php artisan vstack:make-action {resource} {name}
php artisan vstack:make-resource {resource} {model} {table}
php artisan vstack:make-filter {resource} {name} {type}
```