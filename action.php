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

/**
 * Latexit uses some function to load plugins.
 */
require_once DOKU_INC . 'inc/pluginutils.php';


/**
 * Action plugin component class handles calling of events before and after
 * some actions.
 */
class action_plugin_latexit extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for given events
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     */
    public function register(Doku_Event_Handler &$controller) {
        //call _purgeCache before using parser's cache
        $controller->register_hook('PARSER_CACHE_USE', 'BEFORE', $this, '_purgeCache');
        //call _setLatexitSort before initializing language (the very first event in DW)
        $controller->register_hook('INIT_LANG_LOAD', 'BEFORE', $this, '_setLatexitSort');
    }

    /**
     * Function purges latexit cache, so even a change in recursively inserted
     * page will generate new file.
     * @param Doku_Event $event Pointer to the give DW event.
     * @param array $param event parameters
     */
    public function _purgeCache(Doku_Event &$event, $param) {
        //FIXME purge only latex cache
        if ($event->data->mode == 'latexit') {
            //touching main config will make all cache invalid
            touch(DOKU_INC . 'conf/local.php');
        }
    }

    /**
     * When LaTeX export is called, this function will change priority
     * of its Syntax component to the highest one.
     * @param Doku_Event $event Pointer to the give DW event.
     * @param array $param event parameters
     */
    public function _setLatexitSort(Doku_Event &$event, $param) {
        if (isset($_GET['do']) && $_GET['do'] == 'export_latexit') {
            //load syntax component and set its sort order
            $syntax_plugin = plugin_load('syntax', 'latexit');
            $syntax_plugin->_setSort(1);
        }
    }

}
