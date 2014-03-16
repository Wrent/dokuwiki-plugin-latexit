<?php

/**
 * DokuWiki Plugin latexit (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC'))
    die();

/**
 * Syntax component handels all substitutions and new DW commands in original text.
 */
class syntax_plugin_latexit extends DokuWiki_Syntax_Plugin {

    /**
     * Order in which this Syntax plugin will be called.
     * @var int 
     */
    private $sort;

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
        if (!isset($this->sort)) {
            return 245;
        } else {
            return $this->sort;
        }
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        //FIXME jenom 2-6 vlnek?
        $this->Lexer->addSpecialPattern('~~~*RECURSIVE~*~~', $mode, 'plugin_latexit');
        $this->Lexer->addSpecialPattern('\\\cite.*?\}', $mode, 'plugin_latexit');
    }

    /**
     * This syntax plugin should be used as a singleton.
     * (so it can change its sort, when latex will be rendered)
     * @return boolean
     */
    public function isSingleton() {
        return true;
    }

    /**
     * Handle matches of the latexit syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, &$handler) {
        //parse citations from the text (this will be done by this plugin only for latex export)
        if (preg_match("/\\\cite(\[([a-zA-Z0-9 \.,\-:]*)\])?\{([a-zA-Z0-9\-:]*?)\}/", $match, $matches)) {
            $pageRef = $matches[2];
            $citeKey = $matches[3];
            return $citeKey;
        } //parse RECURSIVE command
        //FIXME Wrong regex!!!
        elseif (preg_match('#~~RECURSIVE~~#', $match)) {
            $tildas = explode('RECURSIVE', $match);
            if ($tildas[0] == $tildas[1]) {
                return array($state, $tildas);
            }
        }
        return array();
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, &$renderer, $data) {
        //this will count the level of an following header according to number of ~ used
        $level = -1 * strlen($data[1][0]) + 7;
        if ($mode == 'xhtml') {
            if (is_array($data)) {
                $renderer->doc .= '<h' . $level . '>Next link recursively inserted</h' . $level . '>';
            }
            return true;
        } elseif ($mode == 'latex') {
            //set the next link to be added recursively
            if (is_array($data)) {
                //there might be more plugins rendering latex and calling this functions could cause an error
                if (method_exists($renderer, '_setRecursive')) {
                    $renderer->_setRecursive(true);
                    $renderer->_increaseLevel($level - 1);
                }
            }
            //insert citation
            else {
                $renderer->doc .= '\\cite{' . $data . '}';
            }
            return true;
        }

        return false;
    }

    /**
     * Set sort order of the syntax component
     * @param int $sort Sort order.
     */
    public function _setSort($sort) {
        $this->sort = $sort;
    }

}
