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


require_once DOKU_INC . 'inc/pluginutils.php';

class action_plugin_latexit extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler &$controller) {

        $controller->register_hook('PARSER_CACHE_USE', 'BEFORE', $this, '_purgeCache');
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, '_setLatexitSort');
        $controller->register_hook('INIT_LANG_LOAD', 'BEFORE', $this, '_setLatexitSort2');
    }

    public function _purgeCache(Doku_Event &$event, $param) {
        //FIXME purge only latex cache
        if ($event->data->mode == 'latexit') {
            touch(DOKU_INC . 'conf/local.php');
        }
    }

    public function _setLatexitSort(Doku_Event &$event, $param) {
        if ($event->data == 'export_latexit') {
            $syntax_plugin = plugin_load('syntax', 'latexit');
            $syntax_plugin->_setSort(1);
        }
    }
    
    public function _setLatexitSort2(Doku_Event &$event, $param) {
        if (isset($_GET['do']) && $_GET['do'] == 'export_latexit') {
            $syntax_plugin = plugin_load('syntax', 'latexit');
            $syntax_plugin->_setSort(1);
        }
    }

}
