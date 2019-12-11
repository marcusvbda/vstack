<?php

namespace marcusvbda\vstack\Fields;

class Card
{
    public $view;
    public $label;
    public $inputs;
    
    public function __construct($label,$inputs)
    {
        $this->label = $label;
        $this->inputs = $inputs;
        $this->makeView();
    }

    private function makeView()
    {
        $card = '<div class="card mb-4">
                    <div class="card-body">';
        if($this->label)
        {
            $card .='<div class="row d-flex justify-content-center mb-3"  >
                        <div class="col-md-10 col-sm-12">';
            $card .=        '<h4>'.$this->label.'</h4>';
            $card.= '   </div>
                    </div>';
        }
        $card.=     '<div class="row d-flex justify-content-center mb-3"  >
                            <div class="col-md-10 col-sm-12">';
        foreach($this->inputs as $input)
        {
            $card.=$input->getView();
        }
        $card.=             '</div>
                        </div>
                    </div>
                </div>';
        $this->view = $card;

    }


}
