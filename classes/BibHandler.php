<?php

/**
 * Bibliography handler is responsible for getting the bibliography info from Zotero portal.
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */

/**
 * Class representing the Bibliography handler.
 */
class BibHandler {

    /**
     * Zotero $type id
     * @var int
     */
    protected $id;
    /**
     * Group or User
     * @var string
     */
    protected $type;
    /**
     * Zotero access key
     * @var string 
     */
    protected $key;
    /**
     * Zotero local repository location
     * @var string 
     */
    protected $repository;
    /**
     * Bibliography entries itself.
     * @var array
     */
    protected $bib_entries;
    /**
     * An instance of BibHandler
     * @var BibHandler 
     */
    protected static $instance;
    
    /**
     * BibHandler is singleton, so this function is used to retrive the link to it.
     * @return BibHandler
     */
    public static function getInstance() {
        if(!isset(BibHandler::$instance)) {
            BibHandler::$instance = new BibHandler();
        }
        return BibHandler::$instance;
    }

    /**
     * Private construktor, only static method can construct it
     * @global array $conf Global DokuWiki configuration
     */
    protected function __construct() {
        global $conf;
        $this->bib_entries = array();

        //get the zotero configuration file
        $zotero_config = file_get_contents(DOKU_INC . 'lib/plugins/zotero/config.ini');
        //parse ID and its type
        preg_match('#([usergop]*)id =([ \d]*)#', $zotero_config, $match);
        $this->type = trim($match[1]) . "s";
        $this->id = trim($match[2]);
        //parse access key
        preg_match('#key =(.*)$#m', $zotero_config, $match);
        $this->key = trim($match[1]);
        //parse local repository location
        preg_match('#cachePage =(.*)$#m', $zotero_config, $match);
        $namespace = explode(':', trim($match[1]));
        $this->repository = $conf['datadir'];
        foreach ($namespace as $name) {
            $this->repository .= '/';
            $this->repository .= $name;
        }
        $this->repository .= '.txt';
    }

    /**
     * Load an entry from the external repository using REST api and the 
     * information from local repository and insert it to bibliography items.
     * @param string $entry Short title of an cited bibliography.
     */
    public function insert($entry) {
        //parse ID of the given $entry
        $rep = file_get_contents($this->repository);
        $regex = '#\|(.{8})\]\]\|' . $entry . '\|#';
        preg_match($regex, $rep, $match);
        $id = $match[1];

        //load the bibtex file using REST api
        $url = "https://api.zotero.org/".
                $this->type
                ."/" .
                $this->id
                . "/items/" .
                $id
                . "?key=" .
                $this->key
                . "&format=atom&content=bibtex";
        $item = simplexml_load_string(file_get_contents($url));
        $bib_item = (string) $item->content;
        //make the short title as the title of the entry
        preg_match('#^[@].*\{(.*),$#m', $bib_item, $match);
        $bib_item = str_replace($match[1], $entry, $bib_item);
        $this->bib_entries[$entry] = $bib_item;
    }

    /**
     * Get the bibtex file from all the entries.
     * @return string
     */
    public function getBibtex() {
        $bibtex = '';
        foreach ($this->bib_entries as $bib) {
            $bibtex .= $bib . "\n\n";
        }
        return $bibtex;
    }

    /**
     * Returns true, if there is no bibliography entries.
     * @return boolean
     */
    public function isEmpty() {
        if(empty($this->bib_entries)) {
            return true;
        } else {
            return false;
        }
    }
}
