<?php

namespace marcusvbda\vstack;

use App;
use marcusvbda\vstack\Fields\{Card,Text};

class Resource
{
    public $model          = "";
    public $id             = [];

    public function __construct()
    {
        $this->model = App::make($this->model);
        $this->makeId();
    }

    public function singularLabel()
    {
        return $this->id;
    }

    public function resultsPerPage()
    {
        return 10;
    }

    public function menu()
    {
        return "Recursos";
    }

    public function menuIcon()
    {
        return "el-icon-menu";
    }

    public function globallySearchable()
    {
        return false;
    }

    public function label()
    {
        return $this->id;
    }

    public function icon()
    {
        return "";
    }

    public function table()
    {
        return ["name"];
    }

    public function filters()
    {
        return [];
    }

    public function indexLabel()
    {
        return "<span class='".$this->icon()." mr-2'></span>"." Listagem de " . $this->label();
    }

    public function storeButtonlabel()
    {
        return "<span class='el-icon-plus mr-2'></span>Cadastrar";
    }

    public function importButtonlabel()
    {
        return "<span class='el-icon-upload2 mr-2'></span>Importar";
    }

    public function exportButtonlabel()
    {
        return "<span class='el-icon-download mr-2'></span>Exportar";
    }

    public function noResultsFoundText()
    {
        return "Nenhum resultado encontrado";
    }

    public function resultsFoundLabel()
    {
        return "Resultados encontrados : ";
    }

    public function nothingStoredText()
    {
        return "<h4>Nada cadastrado ainda...<h4>";
    }

    public function nothingStoredSubText() 
    {
        return "<span>Clique no botão abaixo para adicionar o primeiro registro ...</span>";
    }

    public function fields()
    {
        $fields = [];
        $columns = array_filter($this->getTableColumns(),function($x)
        {
            if(!in_array($x,["id","confirmation_token","recovery_token","password","deleted_at","updated_at","created_at","remember_token"])) return $x;
        });
        foreach($columns as $column)
        {
            $fields[] = new Text([
                "label" => $column, "field" => $column, "required" => true,
                "placeholder" => "", "rules" => "required|max:255"
            ]);
        }
        return [new Card("Informações",$fields)];
    }

    public function getTableColumns() 
    {
        return $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
    }

    public function lenses()
    {
        return [];
    }

    public function metrics()
    {
        return [];
    }

    public function customMetricOptions()
    {
        return [];
    }

    public function search()
    {
        return [];
    }

    public function route()
    {
        return route("resource.index", ["resource" => strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', (new \ReflectionClass($this))->getShortName()))]);
    }

    private function makeId()
    {
        $aux =  explode("/", $this->route());
        $this->id = $aux[count($aux) - 1];
    }

    public function canViewList()
    {
        return true;
    }

    public function canView()
    {
        return true;
    }

    public function canCreate()
    {
        return true;
    }

    public function canImport()
    {
        return true;
    }

    public function canExport()
    {
        return true;
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return true;
    }

    public function canCustomizeMetrics()
    {
        return false;
    }

    public function customMetricsButtonText()
    {
        return "Editar Cards Customizados";
    }
    
    public function getValidationRule()
    {
        $validation_rules = [];
        foreach ($this->fields() as $card) {
            foreach ($card->inputs as $field) {
                $validation_rules[$field->options["field"]] = $field->options["rules"];
            }
        }
        return $validation_rules;
    }
}
