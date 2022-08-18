<?php

namespace marcusvbda\vstack\Actions;

use  marcusvbda\vstack\Action;
use Illuminate\Http\Request;
use marcusvbda\vstack\Services\Messages;

class MultipleDelete extends Action
{
    public $id = "multiple-delete";
    public $run_btn = "Excluir";
    public $title = "Excluir";
    public $message = "Essa ação irá excluir os itens selecionados";
    public $success_message = 'Itens excluídos com sucesso';
    public $model = null;
    public $action = null;

    public function __construct($options =[])
    {
        $this->action = function(Request $request) {
            $ids = $request->input('ids');
            $this->model = new $this->model;
            $this->model->whereIn('id', $ids)->delete();
            Messages::send("success", "Os leads foram excluidos");
            return ['success' => true];
        };

        foreach($options as $key => $value){
            $this->{$key} = $value;
        }        
    }

    public function handler(Request $request)
    {
        $action = $this->action;
        return $action($request);
    }
}
