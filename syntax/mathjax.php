<?php

/**
 * DokuWiki Plugin latexit (Syntax Component)
 * This file is based on mathjax syntax plugin https://www.dokuwiki.org/plugin:mathjax
 * 
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Mark Liffiton <liffiton@gmail.com>
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC'))
    die();

class syntax_plugin_latexit_mathjax extends DokuWiki_Syntax_Plugin {

    /**
     * Order in which this Syntax plugin will be called.
     * @var int 
     */
    private $sort;

    # We need to grab any math before dokuwiki tries to parse it.
    # Once it's 'claimed' by this plugin (type: protected), it won't be altered.
    # Set of environments that this plugin will protect from Dokuwiki parsing
    # * is escaped to work in regexp below
    # Note: "math", "displaymath", and "flalign" environments seem to not be 
    #        recognized by Mathjax...  They will still be protected from Dokuwiki,
    #        but they will not be rendered by MathJax.
    private static $ENVIRONMENTS = array(
        "math",
        "displaymath",
        "equation",
        "equation\*",
        "eqnarray",
        "eqnarray\*",
        "align",
        "align\*",
        "flalign",
        "flalign\*",
        "alignat",
        "alignat\*",
        "multline",
        "multline\*",
        "gather",
        "gather\*",
    );

    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'protected';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        if (!isset($this->sort)) {
            return 65;
        } else {
            return $this->sort;
        }
    }
    
    /**
     * Set sort order of the syntax component
     * @param int $sort Sort order.
     */
    public function _setSort($sort) {
        $this->sort = $sort;
    }

    /**
     * Connect lookup pattern to lexer.
     * regexp patterns adapted from jsMath plugin: http://www.dokuwiki.org/plugin:jsmath
     * 
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addEntryPattern('(?<!\\\\)\$(?=[^\$][^\r\n]*?\$)', $mode, 'plugin_latexit_mathjax');
        $this->Lexer->addEntryPattern('\$\$(?=.*?\$\$)', $mode, 'plugin_latexit_mathjax');
        $this->Lexer->addEntryPattern('\\\\\((?=.*?\\\\\))', $mode, 'plugin_latexit_mathjax');
        $this->Lexer->addEntryPattern('\\\\\[(?=.*?\\\\])', $mode, 'plugin_latexit_mathjax');
        foreach (self::$ENVIRONMENTS as $env) {
            $this->Lexer->addEntryPattern('\\\\begin{' . $env . '}(?=.*?\\\\end{' . $env . '})', $mode, 'plugin_latexit_mathjax');
        }
    }

    public function postConnect() {
        $this->Lexer->addExitPattern('\$(?!\$)', 'plugin_latexit_mathjax');
        $this->Lexer->addExitPattern('\\\\\)', 'plugin_latexit_mathjax');
        $this->Lexer->addExitPattern('\\\\\]', 'plugin_latexit_mathjax');
        foreach (self::$ENVIRONMENTS as $env) {
            $this->Lexer->addExitPattern('\\\\end{' . $env . '}', 'plugin_latexit_mathjax');
        }
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
        // Just pass it through...
        return $match;
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
        if ($mode == 'latex') {
            $renderer->_mathMode($data);
            return true;
        }
        return false;
    }

}

// vim:ts=4:sw=4:et:
