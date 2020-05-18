<?php

namespace marcusvbda\vstack\Fields;

class Card
{
    public $view;
    public $label;
    public $inputs;

    public function __construct($label, $inputs)
    {
        $this->label = $label;
        $this->inputs = $inputs;
        $this->makeView();
    }

    private function mapInputs()
    {
        $views = collect($this->inputs)->map(function ($x) {
            return $x->getView();
        })->toArray();
        return implode("", $views);
    }

    private function makeView()
    {
        $card = '
        <div>
            <div class="card mb-4">
                <div class="card-body">
                    ' . (@$this->label ? '
                        <div class="row d-flex justify-content-center mb-3">
                            <div class="col-12">
                                <h4>' . $this->label . '</h4>
                            </div>
                        </div>
                    ' : "") . '

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="row-responsive-table">
                                    <table class="table table-striped">
                                        <tbody>' . $this->mapInputs() . '</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        ';
        $this->view = $card;
    }
}
