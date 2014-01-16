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

class syntax_plugin_latexit extends DokuWiki_Syntax_Plugin {

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
        return 295;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        //FIXME jenom 2-6 vlnek?
        $this->Lexer->addSpecialPattern('~~~*RECURSIVE~*~~', $mode, 'plugin_latexit');
    }
    
    public function isSingleton() {
        return false;
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
         $tildas = explode('RECURSIVE',$match);
         if($tildas[0] == $tildas[1]){
             return array($state, $tildas);
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
        $level = -1*strlen($data[1][0]) + 7;
        if ($mode == 'xhtml') {
            //6 = 1, 5=2,4=3,3=4,2=5
            $renderer->doc .= '<h'.$level.'>Next link recursively inserted</h'.$level.'>';
            return true;
        } elseif ($mode == 'latex') {
            //FIXME co kdyz bude latex generovat i neco jineho? nemam se radeji prejmenovat format na latexit?
            $renderer->_setRecursive(true);
            $renderer->_increaseLevel($level - 1);
            return true;
        }

        return false;
    }

}
