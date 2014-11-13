<?php

/**
 * Label handler is responsible for keeping all header labels unique.
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */

/**
 * Class representing the LabelHandlers
 */
class LabelHandler {

    /**
     * Instance of a LabelHandler
     * @var LabelHandler
     */
    protected static $instance;
    /**
     * All used labels.
     * @var array 
     */
    protected $labels;
    /**
     * Usage count of each label.
     * @var array 
     */
    protected $count;
    
    /**
     * The handler is singleton, so you can access it only by this function.
     * @return LabelHandler
     */
    public static function getInstance() {
        if(!isset(LabelHandler::$instance)) {
            LabelHandler::$instance = new LabelHandler();
        }
        return LabelHandler::$instance;
    }
    
    /**
     * Private constructor can be called only by getInstance method.
     */
    protected function __construct() {
        $this->labels = array();
        $this->count = array();
    }
    
    /**
     * Inserts new label to array and returns its unique version.
     * @param string $label
     * @return string
     */
    public function newLabel($label) {
        $search = array_search($label, $this->labels);
        //if the occurence is first, just insert
        if($search === FALSE) {
            $this->labels[] = $label;
            $this->count[] = 1;
        } 
        //else increase count and return unique version with count in the end
        else {
            $this->count[$search]++;
            $label .= $this->count[$search];
        }
        return $label;
    }
}
