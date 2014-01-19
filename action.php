<?php

/**
 * DokuWiki Plugin latexit (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC'))
    die();

class action_plugin_latexit extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler &$controller) {

        $controller->register_hook('PARSER_CACHE_USE', 'BEFORE', $this, '_purgecache');
    }


    public function _purgecache(Doku_Event &$event, $param) {
        //FIXME purge only latex cache
        if($event->data->mode == 'latexit') {
            touch(DOKU_INC . 'conf/local.php');
        }      
    }

}
