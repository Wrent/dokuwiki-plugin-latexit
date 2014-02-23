<?php

/**
 * Rowspan handler is responsible for handling rowspan in tables.
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */

/**
 * includes data object representing Rowspan
 */
require_once DOKU_INC . 'lib/plugins/latexit/classes/Rowspan.php';

class RowspanHandler {

    /**
     * All rowspans in table are saved here.
     * @var array of Rowspan 
     */
    private $rowspans;

    /**
     * Init handler.
     */
    public function __construct() {
        $this->rowspans = array();
    }

    /**
     * Insert new Rowspan with a given params.
     * @param int $rowspan Rowspan value itself.
     * @param int $cell_id Cell order in a row.
     */
    public function insertRowspan($rowspan, $cell_id) {
        $rs = new Rowspan($rowspan, $cell_id);
        $this->rowspans[] = $rs;
    }

    /**
     * Decreases a rowspan for given cell order by one.
     * @param int $cell_id Cell order.
     * @return void
     */
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

    /**
     * Return the rowspan for a give cell order.
     * @param int $cell_id Cell order
     * @return int Rowspan value.
     */
    public function getRowspan($cell_id) {
        $i = $this->findRowspan($cell_id);
        if ($i == -1) {
            return 0;
        }
        return $this->rowspans[$i]->getRowspan();
    }

    /**
     * Function used for finding rowspan for a give cell order.
     * @param int $cell_id Cell order
     * @return int Rowspan position in array.
     */
    private function findRowspan($cell_id) {
        $i = 0;
        while ($i < count($this->rowspans) && $cell_id != $this->rowspans[$i]->getCellId()) {
            $i++;
        }
        if ($i >= count($this->rowspans)) {
            //no rowspan with this cell id has been found
            return -1;
        } else {
            return $i;
        }
    }

}
