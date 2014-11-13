<?php

/**
 * Rowspan class is used for handling rowspan in tables.
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */

/**
 * Rowspan class representing rowspan itself and storing cell_id.
 * The class exists mainly to encapsulate the data together.
 */
class Rowspan {
    /**
     * Rowspan itself
     * @var int 
     */
    protected $rowspan;
    /**
     * Id of a cell (order in a row), which started rowspan.
     * @var int
     */
    protected $cell_id;
           
    /**
     * Creates new rowspan
     * @param int $rowspan Rowspan itself.
     * @param int $cell_id Id of a cell.
     */
    public function __construct($rowspan, $cell_id) {
        $this->rowspan = $rowspan;
        $this->cell_id = $cell_id;
    }
    /**
     * Returns the rowspan
     * @return int Rowspan
     */
    public function getRowspan() {
        return $this->rowspan;
    }

    /**
     * Returns the cell id
     * @return int Cell order in a row
     */
    public function getCellId() {
        return $this->cell_id;
    }

    /**
     * Sets Rowspan
     * @param int $rowspan
     */
    public function setRowspan($rowspan) {
        $this->rowspan = $rowspan;
    }

    /**
     * Sets cell Id
     * @param int $cell_id
     */
    public function setCellId($cell_id) {
        $this->cell_id = $cell_id;
    }
}
