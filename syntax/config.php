<?php

/**
 * DokuWiki Plugin latexit (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * Syntax component
 *
 * Allows per document reconfiguration
 */
class syntax_plugin_latexit_config extends DokuWiki_Syntax_Plugin {

    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 300;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{latexit[:>].+?}}', $mode, 'plugin_latexit_config');
    }

    /**
     * Handle matches of the latexit syntax
     *
     * @param string       $match   The match of the syntax
     * @param int          $state   The state of the handler
     * @param int          $pos     The position in the document
     * @param Doku_Handler $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, &$handler) {
        list($key, $val) = explode(' ', trim(substr($match, 10, -2)), 2);
        $key = trim($key);
        $val = trim($val);

        return array($key, $val);
    }

    /**
     * Store config in metadata
     *
     * @param string        $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer $renderer  The renderer
     * @param array         $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, &$renderer, $data) {
        if($mode != 'metadata') return false;

        list($key, $val) = $data;
        $renderer->meta['plugin_latexit'][$key] = $val;
    }
}