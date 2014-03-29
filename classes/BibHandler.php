<?php

/**
 * Bibliography handler is responsible for getting the bibliography info from Zotero portal.
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */
require_once DOKU_INC . 'lib/plugins/zotero/TextZoteroRepository.php';

class BibHandler {

    private $group;
    private $key;
    private $repository;
    private $bib_entries;

    public function __construct() {
        global $conf;
        $this->bib_entries = array();

        $zotero_config = file_get_contents(DOKU_INC . 'lib/plugins/zotero/config.ini');
        preg_match('#groupid =([ \d]*)#', $zotero_config, $match);
        $this->group = trim($match[1]);
        preg_match('#key =(.*)$#m', $zotero_config, $match);
        $this->key = trim($match[1]);
        preg_match('#cachePage =(.*)$#m', $zotero_config, $match);
        $namespace = explode(':', trim($match[1]));
        $this->repository = $conf['datadir'];
        foreach ($namespace as $name) {
            $this->repository .= '/';
            $this->repository .= $name;
        }
        $this->repository .= '.txt';
    }

    public function insert($entry) {
        $rep = file_get_contents($this->repository);
        $regex = '#\|(.{8})\]\]\|' . $entry . '\|#';
        preg_match($regex, $rep, $match);
        $id = $match[1];

        $url = "https://api.zotero.org/groups/" .
                $this->group
                . "/items/" .
                $id
                . "?key=" .
                $this->key
                . "&format=atom&content=bibtex";
        $item = simplexml_load_string(file_get_contents($url));
        $bib_item = (string) $item->content;
        preg_match('#^[@].*\{(.*),$#m', $bib_item, $match);
        $bib_item = str_replace($match[1], $entry, $bib_item);
        $this->bib_entries[$entry] = $bib_item;
    }

    public function getBibtex() {
        foreach ($this->bib_entries as $bib) {
            $bibtex .= $bib . "\n\n";
        }
        return $bibtex;
    }

}
