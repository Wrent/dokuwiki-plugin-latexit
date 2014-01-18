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
require_once DOKU_INC . 'lib/plugins/latexit/classes/RowspanHandler.php';
require_once DOKU_INC . 'inc/parserutils.php';
require_once DOKU_INC . 'inc/pageutils.php';

class renderer_plugin_latexit extends Doku_Renderer {

    private $packages;
    private $last_level;
    private $list_opened;
    private $recursion_level;
    private $headers_level;
    private $recursive;
    private $last_level_increase;
    private $cells_count;
    private $table_cols;
    private $last_colspan;
    private $last_rowspan;
    private $last_align;
    private $in_table;
    private $default_table_align;
    private $rowspan_handler;

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

    public function isSingleton() {
        return false;
    }

    /**
     * function is called, when a document is started to being rendered.
     * It adds headers to the LaTeX document and sets the browser headers of the file.
     */
    function document_start() {
        //initialize variables
        global $latexit_level;
        global $latexit_headers;


        $this->packages = array();
        $this->list_opened = FALSE;
        $this->recursive = FALSE;
        $this->in_table = FALSE;
        $this->last_level_increase = 0;
        $this->rowspan_handler = new RowspanHandler();
        if (!isset($latexit_level) || is_null($latexit_level)) {
            $this->recursion_level = 0;
        } else {
            $this->recursion_level = $latexit_level;
        }
        if (!isset($latexit_headers) || is_null($latexit_headers)) {
            $this->headers_level = 0;
        } else {
            $this->headers_level = $latexit_headers;
        }

        if (!$this->_immersed()) {
            //this is default LaTeX header right now, can be changed in configuration
            $header_default = "\\documentclass[a4paper, 11pt]{article}\n"
                    . "\\usepackage[utf8x]{inputenc}\n"
                    . "\\usepackage[czech]{babel}\n";
            $packages = '~~~PACKAGES~~~';
            $document_start = "\\begin{document}";
            //FIXME if conf
            $header = $header_default;
            $this->doc .= $header . $packages . $document_start;
            $this->doc .= "\n\n";

            //set the headers, so the browsers knows, this is not the HTML file
            header('Content-Type: application/x-latex');
            header('Content-Disposition: attachment; filename="output.latex";');
        } else {
            $this->doc .= '~~~PACKAGES-START~~~';
            $this->doc .= '~~~PACKAGES~~~';
            $this->doc .= '~~~PACKAGES-END~~~';
        }
    }

    /**
     * function is called, when a document ends its rendering to finish the document
     * It finalizes the document.
     */
    function document_end() {
        if (!$this->_immersed()) {
            $this->doc .= "\n\n";
            $footer_default = "\\end{document}\n";

            $this->doc .= $footer_default;
            $this->_highlightFixme();
        }
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

        if ($this->_immersed()) {
            $level += $this->headers_level;
        }
        $this->doc .= "\n\n";
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
                $this->_open('textbf');
                $this->doc .= $this->_latexSpecialChars($text);
                $this->_close();
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
        if ($this->in_table) {
            $this->doc .= "\\newline ";
        } else {
            $this->doc .= "\\\\";
        }
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
        $this->doc .= "\n";
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
        $this->doc .= "\n";
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
        $this->doc .= $this->_latexSpecialChars($text);
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
        //FIXME file, konfigurace?
        $pckg = new Package('listings');
        $this->_addPackage($pckg);
        if (!is_null($lang)) {
            $this->_open('lstset');
            $this->doc .= 'language=';
            $this->doc .= $this->_latexSpecialChars($lang);
            $this->_close();
            $this->doc .= "\n";
        }
        $this->_open('begin');
        $this->doc .= 'lstlisting';
        $this->_close();
        $this->doc .= "\n";
        $text = str_replace('”', '"', $text);
        $text = str_replace('–', '-', $text);
        $this->doc .= $text;
        $this->_open('end');
        $this->doc .= 'lstlisting';
        $this->_close();
        $this->doc .= "\n\n";
    }

    function acronym($acronym) {
        $this->doc .= $this->_latexSpecialChars($acronym);
    }

    function smiley($smiley) {
        if ($smiley == 'FIXME') {
            $pckg = new Package('soul');
            $this->_addPackage($pckg);
            $this->doc .= 'FIXME';
        }
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
        $this->internallink($link, $link);
    }

    function locallink($hash, $name = NULL) {
        
    }

    /**
     * function is called, when renderer finds an internal link
     * It resolves the internal link (namespaces, URL)
     * Depending on the configuration:
     *     It handles link as an external and calls proper function in LaTeX depending on the title
     * @param type $link Internal link (can be without proper namespace)
     * @param type $title Title, can be null or array (if it is media)
     */
    function internallink($link, $title = NULL) {
        global $ID; //in this global var DokuWiki stores the current page id with namespaces
        global $latexit_level;
        global $latexit_headers;

        $title = $this->_latexSpecialChars($title);

        $link_original = $link;

        $current_namespace = getNS($ID); //get current namespace from current page
        resolve_pageid($current_namespace, $link, $exists); //get the page ID with right namespaces
        //$exists stores information, if the page exists. We don't care about that right now. FIXME?
        $params = '';
        $absoluteURL = true;
        $url = wl($link, $params, $absoluteURL); //get the whole URL
        //FIXME keep hash in the end? have to test!
        //FIXME configurable
        if ($this->recursive) {
            //FIXME bacha na nekonecnou rekurzi
            $latexit_level = $this->recursion_level + 1;
            $latexit_headers = $this->headers_level;

            $data = p_cached_output(wikifn($link), 'latexit');
            $data = $this->_loadPackages($data);
            $this->doc .= "\n\n";
            $this->doc .= "%RECURSIVELY INSERTED FILE START";
            $this->doc .= "\n\n";
            $this->doc .= $data;
            $this->doc .= "\n\n";
            $this->doc .= "%RECURSIVELY INSERTED FILE END";
            $this->doc .= "\n\n";
            $this->headers_level -= $this->last_level_increase;
        } else {
            //handle internal links as they were external
            $package = new Package('hyperref');
            //to fix encoding warning
            $package->addParameter('unicode');
            $this->_addPackage($package);
            //FIXME title pictures
            if (is_null($title) || trim($title) == '') {
                $this->doc .= '\\href{' . $url . '}{' . $link_original . '}';
            } else {
                $this->doc .= '\\href{' . $url . '}{' . $title . '}';
            }
        }
        $this->recursive = FALSE;
    }

    /**
     * function is called, when renderer finds an external link
     * It calls proper function in LaTeX depending on the title
     * @param type $link External link
     * @param type $title Title, can be null or array (if it is media)
     */
    function externallink($link, $title = NULL) {
        $title = $this->_latexSpecialChars($title);
        $package = new Package('hyperref');
        //to fix encoding warning
        $package->addParameter('unicode');
        $this->_addPackage($package);
        //FIXME pictures
        if (is_null($title) || trim($title) == '') {
            $this->doc .= '\\url{' . $link . '}';
        } else {
            $this->doc .= '\\href{' . $link . '}{' . $title . '}';
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
        $name = $this->_latexSpecialChars($name);
        $package = new Package('hyperref');
        //to fix encoding warning
        $package->addParameter('unicode');
        $this->_addPackage($package);
        //FIXME pictures
        if (is_null($name) || trim($name) == '') {
            $this->doc .= '\\href{mailto:' . $address . '}{' . $address . '}';
        } else {
            $this->doc .= '\\href{mailto:' . $address . '}{' . $name . '}';
        }
    }

    function internalmedia($src, $title = NULL, $align = NULL, $width = NULL, $height = NULL, $cache = NULL, $linking = NULL) {
        $pckg = new Package('graphicx');
        $pckg->addCommand('\\graphicspath{{images/}}');
        $this->_addPackage($pckg);
        $namespaces = explode(':', $src);
        for ($i = 1; $i < count($namespaces); $i++) {
            if ($i != 1) {
                $path .= "/";
            }
            $path .= $namespaces[$i];
        }
        //http://stackoverflow.com/questions/2395882/how-to-remove-extension-from-string-only-real-extension
        $path = preg_replace("/\\.[^.\\s]{3,4}$/", "", $path);
        $this->doc .= "\includegraphics{" . $path . "}";
    }

    function externalmedia($src, $title = NULL, $align = NULL, $width = NULL, $height = NULL, $cache = NULL, $linking = NULL) {
        
    }

    function internalmedialink(
    $src, $title = NULL, $align = NULL, $width = NULL, $height = NULL, $cache = NULL
    ) {
        var_dump($src);
    }

    function externalmedialink(
    $src, $title = NULL, $align = NULL, $width = NULL, $height = NULL, $cache = NULL
    ) {
        
    }

    function table_open($maxcols = null, $numrows = null, $pos = null) {
        $this->default_table_align = 'l';
        $this->table_cols = $maxcols;
        $this->in_table = true;
        $pckg = new Package('longtable');
        $this->_addPackage($pckg);
        $this->doc .= "\\begin{longtable}{|";
        for ($i = 0; $i < $maxcols; $i++) {
            $this->doc .= $this->default_table_align . "|";
            //FIXME v konfiguraci nastavit defaultni zarovnani tabulek (zvysi pak prehlednost generovaneho kodu)
        }
        $this->doc .= "}\n\hline\n";
    }

    function table_close($pos = null) {
        $this->in_table = false;
        $this->doc .= "\\end{longtable}\n\n";
    }

    function tablerow_open() {
        $this->cells_count = 0;
    }

    function tablerow_close() {
        $this->doc .= " \\\\ \n";
        $this->doc .= "\\hline \n";
    }

    function tableheader_open($colspan = 1, $align = NULL, $rowspan = 1) {
        $this->tablecell_open($colspan, $align, $rowspan);
        $this->_open('textbf');

        /* FIXME
         * \endfirsthead: Line(s) to appear as head of the table on the first page
          \endhead: Line(s) to appear at top of every page (except first)
          \endfoot: Last line(s) to appear at the bottom of every page (except last)
          \endlastfoot: Last line(s) to appear at the end of the table
         */
    }

    function tableheader_close() {
        $this->_close();
        $this->tablecell_close();
    }

    function tablecell_open($colspan = 1, $align = NULL, $rowspan = 1) {
        if ($align == NULL) {
            $align = $this->default_table_align;
        } else {
            $align = substr($align, 0, 1);
        }
        $this->last_colspan = $colspan;
        $this->last_rowspan = $rowspan;
        $this->last_align = $align;

        if ($this->rowspan_handler->getRowspan($this->cells_count) != 0) {
            $this->doc .= ' & ';
            $this->rowspan_handler->decreaseRowspan($this->cells_count);
            $this->cells_count++;
        }

        if ($colspan != 1 || $align != $this->default_table_align) {
            $this->doc .= "\\multicolumn{" . $colspan . "}{|$align|}{";
        }
        if ($rowspan != 1) {
            $pckg = new Package('multirow');
            $this->_addPackage($pckg);
            $this->rowspan_handler->insertRowspan($rowspan - 1, $this->cells_count);
            $this->doc .= "\\multirow{" . $rowspan . "}{*}{";
        }
    }

    function tablecell_close() {
        if ($this->last_colspan != 1 || $this->last_align != $this->default_table_align) {
            $this->doc .= "}";
        }
        if ($this->last_rowspan != 1) {
            $this->doc .= "}";
        }

        $this->cells_count += $this->last_colspan;
        if ($this->table_cols != $this->cells_count) {
            $this->doc .= " & ";
        }
    }

    /**
     * Syntax of almost every LaTeX command is alway the same.
     * @param $command The name of a LaTeX command.
     */
    private function _open($command) {
        $this->doc .= "\\" . $command . "{";
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
        if ($this->_immersed()) {
            $packages = serialize($this->packages);
        } else {
            foreach ($this->packages as $package) {
                $param = $this->_latexSpecialChars($package->printParameters());
                $packages .= "\\usepackage$param{" . $this->_latexSpecialChars($package->getName()) . "}\n";
                $packages .= $package->printCommands();
            }
        }
        $this->doc = str_replace('~~~PACKAGES~~~', $packages, $this->doc);
    }

    private function _loadPackages($data) {
        preg_match('#~~~PACKAGES-START~~~(.*?)~~~PACKAGES-END~~~#si', $data, $pckg);
        $data = preg_replace('#~~~PACKAGES-START~~~.*~~~PACKAGES-END~~~#si', '', $data);

        $packages = unserialize($pckg[1]);
        if (!is_null($packages) && is_array($packages)) {
            foreach ($packages as $package) {
                $this->_addPackage($package);
            }
        }
        return $data;
    }

    private function _highlightFixme() {
        $this->doc = str_replace('FIXME', '\hl{FIXME}', $this->doc);
        $this->doc = preg_replace_callback('#{FIXME}\[(.*?)\]\((.*?)\)#si', array(&$this, '_highlightFixmeHandler'), $this->doc);
    }
    
    private function _highlightFixmeHandler($matches) {
        $matches[1] = $this->_stripDiacritics($matches[1]);
        $matches[2] = $this->_stripDiacritics($matches[2]);
        return '{FIXME['.$matches[1].']('.$matches[2].')}';
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

    /**
     * This function finds out, if the current renderer is immersed in recursion.
     * @return boolean Is immersed in recursion?
     */
    private function _immersed() {
        if ($this->recursion_level > 0) {
            return true;
        }
        return false;
    }

    private function _latexSpecialChars($text) {
        $text = str_replace(array('\\', '{', '}', '&', '%', '$', '#', '_', '~', '^', '<', '>'), array('\textbackslash', '\{', '\}', '\&', '\%', '\$', '\#', '\_', '\textasciitilde{}', '\textasciicircum{}', '\textless ', '\textgreater '), $text);
        $text = str_replace('\\textbackslash', '\textbackslash{}', $text);
        /* $text = str_replace('$', '\$', $text);
          $text = str_replace('\\$\\backslash\\\\$', '$\backslash$', $text); */
        return $text;
    }

    private function _checkLinkRecursion($text) {
        return preg_match('#~~~LINK-RECURSION~~~#si', $text);
    }

    public function _setRecursive($recursive) {
        $this->recursive = $recursive;
    }

    public function _increaseLevel($level) {
        $this->last_level_increase = $level;
        $this->headers_level += $level;
    }

    private function _stripDiacritics($data) {
        $table = Array(
            'ä' => 'a',
            'Ä' => 'A',
            'á' => 'a',
            'Á' => 'A',
            'à' => 'a',
            'À' => 'A',
            'ã' => 'a',
            'Ã' => 'A',
            'â' => 'a',
            'Â' => 'A',
            'č' => 'c',
            'Č' => 'C',
            'ć' => 'c',
            'Ć' => 'C',
            'ď' => 'd',
            'Ď' => 'D',
            'ě' => 'e',
            'Ě' => 'E',
            'é' => 'e',
            'É' => 'E',
            'ë' => 'e',
            'Ë' => 'E',
            'è' => 'e',
            'È' => 'E',
            'ê' => 'e',
            'Ê' => 'E',
            'í' => 'i',
            'Í' => 'I',
            'ï' => 'i',
            'Ï' => 'I',
            'ì' => 'i',
            'Ì' => 'I',
            'î' => 'i',
            'Î' => 'I',
            'ľ' => 'l',
            'Ľ' => 'L',
            'ĺ' => 'l',
            'Ĺ' => 'L',
            'ń' => 'n',
            'Ń' => 'N',
            'ň' => 'n',
            'Ň' => 'N',
            'ñ' => 'n',
            'Ñ' => 'N',
            'ó' => 'o',
            'Ó' => 'O',
            'ö' => 'o',
            'Ö' => 'O',
            'ô' => 'o',
            'Ô' => 'O',
            'ò' => 'o',
            'Ò' => 'O',
            'õ' => 'o',
            'Õ' => 'O',
            'ő' => 'o',
            'Ő' => 'O',
            'ř' => 'r',
            'Ř' => 'R',
            'ŕ' => 'r',
            'Ŕ' => 'R',
            'š' => 's',
            'Š' => 'S',
            'ś' => 's',
            'Ś' => 'S',
            'ť' => 't',
            'Ť' => 'T',
            'ú' => 'u',
            'Ú' => 'U',
            'ů' => 'u',
            'Ů' => 'U',
            'ü' => 'u',
            'Ü' => 'U',
            'ù' => 'u',
            'Ù' => 'U',
            'ũ' => 'u',
            'Ũ' => 'U',
            'û' => 'u',
            'Û' => 'U',
            'ý' => 'y',
            'Ý' => 'Y',
            'ž' => 'z',
            'Ž' => 'Z',
            'ź' => 'z',
            'Ź' => 'Z'
        );

        return strtr($data, $table);
    }

}
