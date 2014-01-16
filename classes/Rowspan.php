<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rowspan
 *
 * @author wrent
 */
class Rowspan {
    private $rowspan;
    private $cell_id;
           
    public function __construct($rowspan, $cell_id) {
        $this->rowspan = $rowspan;
        $this->cell_id = $cell_id;
    }
    public function getRowspan() {
        return $this->rowspan;
    }

    public function getCellId() {
        return $this->cell_id;
    }

    public function setRowspan($rowspan) {
        $this->rowspan = $rowspan;
    }

    public function setCellId($cell_id) {
        $this->cell_id = $cell_id;
    }



}
