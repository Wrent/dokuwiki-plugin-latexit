<?php

/**
 * DokuWiki Plugin latexit (Renderer Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC'))
    die();

require_once DOKU_INC . 'inc/parser/xhtml.php';
require_once DOKU_INC . 'lib/plugins/latexit/classes/Package.php';

class renderer_plugin_latexit extends Doku_Renderer {

    private $packages;
    private $last_level;
    private $list_opened;

    /**
     * Make available as LaTeX renderer
     */
    public function canRender($format) {
        if ($format == 'latex') {
            return true;
        }
        return false;
    }

    /**
     * Return the rendering format of the renderer
     */
    public function getFormat() {
        return 'latex';
    }

    /**
     * function is called, when a document is started to being rendered.
     * It adds headers to the LaTeX document and sets the browser headers of the file.
     */
    function document_start() {
        //initialize variables
        $this->packages = array();
        $this->list_opened = FALSE;
        //this is default LaTeX header right now, can be changed in configuration
        $header_default = "\\documentclass[a4paper, 11pt]{article}\n"
                . "\\usepackage[utf8]{inputenc}\n"
                . "\\usepackage[czech]{babel}\n";
        $packages = '~~~PACKAGES~~~';
        $document_start = "\\begin{document}\n"
                . "\n";
        //FIXME if conf
        $header = $header_default;
        $this->doc .= $header . $packages . $document_start;

        //set the headers, so the browsers knows, this is not the HTML file
        header('Content-Type: application/x-latex');
        header('Content-Disposition: attachment; filename="output.latex";');
    }

    /**
     * function is called, when a document ends its rendering to finish the document
     * It finalizes the document.
     */
    function document_end() {
        $footer_default = "\n\n"
                . "\\end{document}\n";

        $this->doc .= $footer_default;

        //insert all packages collected during rendering
        $this->_insertPackages();
    }

    function render_TOC() {
        return '';
    }

    function toc_additem($id, $text, $level) {
        
    }

    /**
     * Function is called, when renderer finds a new header.
     * It calls the LaTeX command for an appropriate level.
     * @param type $text Text of the header
     * @param type $level Level of the header.
     * @param type $pos ???
     */
    function header($text, $level, $pos) {
        switch ($level) {
            case 1:
                $this->_header('section', $text);
                break;
            case 2:
                $this->_header('subsection', $text);
                break;
            case 3:
                $this->_header('subsubsection', $text);
                break;
            case 4:
                $this->_header('paragraph', $text);
                break;
            case 5:
                $this->_header('subparagraph', $text);
                break;
            default:
                $this->doc .= $this->_latexSpecialChars($text);
                break;
        }
    }

    function section_open($level) {
        
    }

    function section_close() {
        
    }

    /**
     * Basic funcion called, when a text not from DokuWiki syntax is read
     * It adds the data to the document, potentionally dangerous characters for
     * LaTeX are escaped or removed.
     */
    function cdata($text) {
        $this->doc .= $this->_latexSpecialChars($text);
    }

    /**
     * Function is called, when renderer finds a new paragraph.
     * It makes new paragraph in LaTeX Document.
     */
    function p_open() {
        $this->doc .= "\n\n";
    }

    /**
     * Function is called, when renderer finds the end of a paragraph.
     */
    function p_close() {
        //there is nothing done with that in LaTeX
    }

    /**
     * Function is called, when renderer finds a linebreak.
     * It adds new line in LaTeX Document.
     */
    function linebreak() {
        $this->doc .= "\\\\";
    }

    function hr() {
        
    }

    /**
     * function is called, when renderer finds a strong text
     * It calls command for strong text in LaTeX Document.
     */
    function strong_open() {
        $this->_open('textbf');
    }

    /**
     * function is called, when renderer finds the end of a strong text 
     */
    function strong_close() {
        $this->_close();
    }

    /**
     * function is called, when renderer finds an emphasised text
     * It calls command for emphasised text in LaTeX Document.
     */
    function emphasis_open() {
        $this->_open('emph');
    }

    /**
     * function is called, when renderer finds the end of an emphasised text
     */
    function emphasis_close() {
        $this->_close();
    }

    /**
     * function is called, when renderer finds an underlined text
     * It calls command for underlined text in LaTeX Document.
     */
    function underline_open() {
        $this->_open('underline');
    }

    /**
     * function is called, when renderer finds the end of an underlined text
     */
    function underline_close() {
        $this->_close();
    }

    /**
     * function is called, when renderer finds a monospace text 
     * (all letters have same width)
     * It calls command for monospace text in LaTeX Document.
     */
    function monospace_open() {
        $this->_open('texttt');
    }

    /**
     * function is called, when renderer finds the end of a monospace text 
     * (all letters have same width)
     */
    function monospace_close() {
        $this->_close();
    }

    /**
     * function is called, when renderer finds a subscript 
     * It adds needed package and calls command for subscript in LaTeX Document.
     */
    function subscript_open() {
        $package = new Package('fixltx2e');
        $this->_addPackage($package);
        $this->_open('textsubscript');
    }

    /**
     * function is called, when renderer finds the end of a subscript 
     */
    function subscript_close() {
        $this->_close();
    }

    /**
     * function is called, when renderer finds a superscript 
     * It adds needed package and calls command for superscript in LaTeX Document.
     */
    function superscript_open() {
        $package = new Package('fixltx2e');
        $this->_addPackage($package);
        $this->_open('textsuperscript');
    }

    /**
     * function is called, when renderer finds the end of a superscript 
     */
    function superscript_close() {
        $this->_close();
    }

    /**
     * function is called, when renderer finds a deleted text
     * It adds needed package and calls command for deleted text in LaTeX Document.
     */
    function deleted_open() {
        $package = new Package('ulem');
        $package->addParameter('normalem');
        $this->_addPackage($package);
        $this->_open('sout');
    }

    /**
     * function is called, when renderer finds the end of a deleted text
     */
    function deleted_close() {
        $this->_close();
    }

    /**
     * function is called, when renderer finds a footnote
     * It calls footnote command in LaTeX Document.
     */
    function footnote_open() {
        $this->_open('footnote');
    }

    /**
     * function is called, when renderer finds the end of a footnote
     */
    function footnote_close() {
        $this->_close();
    }

    /**
     * function is called, when renderer finds start of an unordered list
     * It calls command for an unordered list in latex, even with right indention
     */
    function listu_open() {
        //FIXME possible refactor
        if ($this->list_opened) {
            for ($i = 1; $i < $this->last_level + 1; $i++) {
                $this->doc .= "  ";
            }
        } else {
            $this->list_opened = TRUE;
        }
        $this->_indent_list();
        $this->doc .= "\\begin{itemize}\n";
    }

    /**
     * function is called, when renderer finds the end of an unordered list
     * It calls command for the end of an unordered list in latex, even with right indention
     */
    function listu_close() {
        //FIXME possible refactor
        if ($this->last_level == 1) {
            $this->list_opened = FALSE;
        }
        $this->_indent_list();
        $this->doc .= "\\end{itemize}\n";
    }

    /**
     * function is called, when renderer finds start of an ordered list
     * It calls command for an ordered list in latex, even with right indention
     */
    function listo_open() {
        //FIXME possible refactor
        if ($this->list_opened) {
            for ($i = 1; $i < $this->last_level + 1; $i++) {
                $this->doc .= "  ";
            }
        } else {
            $this->list_opened = TRUE;
        }
        $this->_indent_list();
        $this->doc .= "\\begin{enumerate}\n";
    }

    /**
     * function is called, when renderer finds the end of an ordered list
     * It calls command for the end of an ordered list in latex, even with right indention
     */
    function listo_close() {
        //FIXME possible refactor
        if ($this->last_level == 1) {
            $this->list_opened = FALSE;
        }
        $this->_indent_list();
        $this->doc .= "\\end{enumerate}\n";
    }

    /**
     * function is called, when renderer finds start of a list item
     * It calls command for a list item in latex, even with right indention
     */
    function listitem_open($level) {
        $this->last_level = $level;
        $this->_indent_list();
        $this->doc .= "  \\item";
    }

    /**
     * function is called, when renderer finds the end of a list item
     */
    function listitem_close() {
        //does nothing in latex
    }

    /**
     * function is called, when renderer finds start of a list item content
     */
    function listcontent_open() {
        //does nothing in latex
    }

    /**
     * function is called, when renderer finds the end of a list item content
     * It adds newline to the latex file.
     */
    function listcontent_close() {
        $this->doc .= "\n";
    }

    function unformatted($text) {
        
    }

    function php($text) {
        
    }

    function phpblock($text) {
        
    }

    function html($text) {
        
    }

    function htmlblock($text) {
        
    }

    function preformatted($text) {
        
    }

    function quote_open() {
        
    }

    function quote_close() {
        
    }

    function file($text, $lang = null, $file = null) {
        
    }

    function code($text, $lang = null, $file = null) {
        
    }

    function acronym($acronym) {
        
    }

    function smiley($smiley) {
        
    }

    function wordblock($word) {
        
    }

    function entity($entity) {
        
    }

    // 640x480 ($x=640, $y=480)
    function multiplyentity($x, $y) {
        
    }

    function singlequoteopening() {
        
    }

    function singlequoteclosing() {
        
    }

    function apostrophe() {
        
    }

    function doublequoteopening() {
        
    }

    function doublequoteclosing() {
        
    }

    // $link like 'SomePage'
    function camelcaselink($link) {
        
    }

    function locallink($hash, $name = NULL) {
        
    }

    // $link like 'wiki:syntax', $title could be an array (media)
    function internallink($link, $title = NULL) {
        
    }

    /**
     * function is called, when renderer finds an external link
     * It calls proper function in LaTeX depending on the title
     * @param type $link External link
     * @param type $title Title, can be null or array (if it is media)
     */
    function externallink($link, $title = NULL) {
        $package = new Package('hyperref');
        $this->_addPackage($package);
        //FIXME pictures
        if(is_null($title)) {
            $this->doc .= '\\url{'.$link.'}';
        } else {
            $this->doc .= '\\href{'.$link.'}{'.$title.'}';
        }
    }

    function rss($url, $params) {
        
    }

    // $link is the original link - probably not much use
    // $wikiName is an indentifier for the wiki
    // $wikiUri is the URL fragment to append to some known URL
    function interwikilink($link, $title = NULL, $wikiName, $wikiUri) {
        
    }

    // Link to file on users OS, $title could be an array (media)
    function filelink($link, $title = NULL) {
        
    }

    // Link to a Windows share, , $title could be an array (media)
    function windowssharelink($link, $title = NULL) {
        
    }

    /**
     * function is called, when renderer finds an email link
     * It calls proper function in LaTeX depending on the name and sets mailto
     * @param type $address Email address
     * @param type $name Name, can be null or array (if it is media)
     */
    function emaillink($address, $name = NULL) {
        $package = new Package('hyperref');
        $this->_addPackage($package);
        //FIXME pictures
        if(is_null($name)) {
            $this->doc .= '\\href{mailto:'.$address.'}{'.$address.'}';
        } else {
            $this->doc .= '\\href{mailto:'.$address.'}{'.$name.'}';
        }
    }

    function internalmedia($src, $title = NULL, $align = NULL, $width = NULL, $height = NULL, $cache = NULL, $linking = NULL) {
        
    }

    function externalmedia($src, $title = NULL, $align = NULL, $width = NULL, $height = NULL, $cache = NULL, $linking = NULL) {
        
    }

    function internalmedialink(
    $src, $title = NULL, $align = NULL, $width = NULL, $height = NULL, $cache = NULL
    ) {
        
    }

    function externalmedialink(
    $src, $title = NULL, $align = NULL, $width = NULL, $height = NULL, $cache = NULL
    ) {
        
    }

    function table_open($maxcols = null, $numrows = null, $pos = null) {
        
    }

    function table_close($pos = null) {
        
    }

    function tablerow_open() {
        
    }

    function tablerow_close() {
        
    }

    function tableheader_open($colspan = 1, $align = NULL, $rowspan = 1) {
        
    }

    function tableheader_close() {
        
    }

    function tablecell_open($colspan = 1, $align = NULL, $rowspan = 1) {
        
    }

    function tablecell_close() {
        
    }

    /**
     * Syntax of almost every LaTeX command is alway the same.
     * @param $command The name of a LaTeX command.
     */
    private function _open($command) {
        $this->doc .= '\\' . $command . '{';
    }

    /**
     * Closing tag of all LaTeX commands is always same and will be called
     * in almost every _close function.
     */
    private function _close() {
        $this->doc .= '}';
    }

    /**
     * Adds name of new package to packages array, but prevents duplicates
     * @param $package LaTeX package to be used in rendering.
     */
    private function _addPackage($package) {
        foreach ($this->packages as $p) {
            if ($p->getName() == $package->getName()) {
                return;
            }
        }
        $this->packages[] = $package;
    }

    /**
     * Inserts all packages collected during the rendering to the head of the document.
     */
    private function _insertPackages() {
        foreach ($this->packages as $package) {
            $param = $this->_latexSpecialChars($package->printParameters());
            $packages .= "\\usepackage$param{" . $this->_latexSpecialChars($package->getName()) . "}\n";
        }
        $this->doc = str_replace('~~~PACKAGES~~~', $packages, $this->doc);
    }

    /**
     * Indents the list given the last seen level.
     */
    private function _indent_list() {
        for ($i = 1; $i < $this->last_level; $i++) {
            $this->doc .= '  ';
        }
    }

    /**
     * Insert header to the LaTeX document with right level command.
     * @param type $level LaTeX command for header on right level.
     * @param type $text Text of the Header.
     */
    private function _header($level, $text) {
        $this->_open($level);
        $this->doc .= $this->_latexSpecialChars($text);
        $this->_close();
        $this->doc .= "\n";
    }

    private function _latexSpecialChars($text) {
        //FIXME
        return $text;
    }

}
