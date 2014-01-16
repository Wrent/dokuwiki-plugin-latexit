<?php

require_once DOKU_INC . 'lib/plugins/latexit/classes/Rowspan.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RowspanHandler
 *
 * @author wrent
 */
class RowspanHandler {

    private $rowspans;

    public function __construct() {
        $this->rowspans = array();
    }

    public function insertRowspan($rowspan, $cell_id) {
        $rs = new Rowspan($rowspan, $cell_id);
        $this->rowspans[] = $rs;
    }

    public function decreaseRowspan($cell_id) {
        $i = $this->findRowspan($cell_id);
        if ($i == -1) {
            return;
        }
        $rs = $this->rowspans[$i]->getRowspan() - 1;
        $this->rowspans[$i]->setRowspan($rs);

        //remove from array
        if ($rs == 0) {
            unset($this->rowspans[$i]);
            $this->rowspans = array_values($this->rowspans);
        }
    }

    public function getRowspan($cell_id) {
        $i = $this->findRowspan($cell_id);
        if ($i == -1) {
            return 0;
        }
        return $this->rowspans[$i]->getRowspan();
    }

    private function findRowspan($cell_id) {
        $i = 0;
        while ($i < count($this->rowspans) && $cell_id != $this->rowspans[$i]->getCellId()) {
            $i++;
        }
        if ($i >= count($this->rowspans)) {
            return -1;
        } else {
            return $i;
        }
    }

}
