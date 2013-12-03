<?php
/**
 * DokuWiki Plugin latexit (Renderer Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Ku&#269;era <adam.kucera@wrent.cz>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

require_once DOKU_INC.'inc/parser/xhtml.php';

class renderer_plugin_latexit extends Doku_Renderer_xhtml {

    /**
     * Make available as XHTML replacement renderer
     */
    public function canRender($format){
        if($format == 'xhtml') return true;
        return false;
    }

    // FIXME override any methods of Doku_Renderer_xhtml here
}

