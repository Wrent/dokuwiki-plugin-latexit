<?php

/**
 * Recursion handler is responsible for preventing the recursive insertion 
 * of subpages to become an undending loop.
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */

/**
 * Class representing the Recurison Handlers
 */
class RecursionHandler {

    /**
     * An instance of Recursion Handler
     * @var RecursionHandler 
     */
    private static $instance;
    /**
     * Array of all pages currently forbiden for recursion.
     * @var array
     */
    private $pages;
    
    /**
     * Handler is singleton, it is only accessible using this function.
     * @return RecursionHandler
     */
    public static function getInstance() {
        if(!isset(RecursionHandler::$instance)) {
            RecursionHandler::$instance = new RecursionHandler();
        }
        return RecursionHandler::$instance;
    }
    
    /**
     * Private constructor.
     */
    private function __construct() {
        $this->pages = array();
    }
    
    /**
     * If the page is already in array, it can't be recursively inserted.
     * @param string $page 
     * @return boolean
     */
    public function disallow($page) {
        return in_array($page, $this->pages);
    }
    
    /**
     * Inserts the page to array.
     * @param string $page
     */
    public function insert($page) {
        $this->pages[] = $page;
    }
    
    /**
     * Removes the page from array and revalues the array.
     * @param type $page
     */
    public function remove($page) {
        $search = array_search($page, $this->pages);
        if ($search !== FALSE) {
            unset($this->pages[$search]);
            $this->pages = array_values($this->pages);
        }
   }
}
