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
require_once 'conf/metadata.php';

class renderer_plugin_latexit extends Doku_Renderer_xhtml {

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

    function document_start() {}

    function document_end() {}

    function render_TOC() { return ''; }

    function toc_additem($id, $text, $level) {}

    function header($text, $level, $pos) {}

    function section_open($level) {}

    function section_close() {}

    function cdata($text) {}

    function p_open() {}

    function p_close() {}

    function linebreak() {}

    function hr() {}

    function strong_open() {}

    function strong_close() {}

    function emphasis_open() {}

    function emphasis_close() {}

    function underline_open() {}

    function underline_close() {}

    function monospace_open() {}

    function monospace_close() {}

    function subscript_open() {}

    function subscript_close() {}

    function superscript_open() {}

    function superscript_close() {}

    function deleted_open() {}

    function deleted_close() {}

    function footnote_open() {}

    function footnote_close() {}

    function listu_open() {}

    function listu_close() {}

    function listo_open() {}

    function listo_close() {}

    function listitem_open($level) {}

    function listitem_close() {}

    function listcontent_open() {}

    function listcontent_close() {}

    function unformatted($text) {}

    function php($text) {}

    function phpblock($text) {}

    function html($text) {}

    function htmlblock($text) {}

    function preformatted($text) {}

    function quote_open() {}

    function quote_close() {}

    function file($text, $lang = null, $file = null ) {}

    function code($text, $lang = null, $file = null ) {}

    function acronym($acronym) {}

    function smiley($smiley) {}

    function wordblock($word) {}

    function entity($entity) {}

    // 640x480 ($x=640, $y=480)
    function multiplyentity($x, $y) {}

    function singlequoteopening() {}

    function singlequoteclosing() {}

    function apostrophe() {}

    function doublequoteopening() {}

    function doublequoteclosing() {}

    // $link like 'SomePage'
    function camelcaselink($link) {}

    function locallink($hash, $name = NULL) {}

    // $link like 'wiki:syntax', $title could be an array (media)
    function internallink($link, $title = NULL) {}

    // $link is full URL with scheme, $title could be an array (media)
    function externallink($link, $title = NULL) {}

    function rss ($url,$params) {}

    // $link is the original link - probably not much use
    // $wikiName is an indentifier for the wiki
    // $wikiUri is the URL fragment to append to some known URL
    function interwikilink($link, $title = NULL, $wikiName, $wikiUri) {}

    // Link to file on users OS, $title could be an array (media)
    function filelink($link, $title = NULL) {}

    // Link to a Windows share, , $title could be an array (media)
    function windowssharelink($link, $title = NULL) {}

//  function email($address, $title = NULL) {}
    function emaillink($address, $name = NULL) {}

    function internalmedia ($src, $title=NULL, $align=NULL, $width=NULL,
                            $height=NULL, $cache=NULL, $linking=NULL) {}

    function externalmedia ($src, $title=NULL, $align=NULL, $width=NULL,
                            $height=NULL, $cache=NULL, $linking=NULL) {}

    function internalmedialink (
        $src,$title=NULL,$align=NULL,$width=NULL,$height=NULL,$cache=NULL
        ) {}

    function externalmedialink(
        $src,$title=NULL,$align=NULL,$width=NULL,$height=NULL,$cache=NULL
        ) {}

    function table_open($maxcols = null, $numrows = null, $pos = null){}

    function table_close($pos = null){}

    function tablerow_open(){}

    function tablerow_close(){}

    function tableheader_open($colspan = 1, $align = NULL, $rowspan = 1){}

    function tableheader_close(){}

    function tablecell_open($colspan = 1, $align = NULL, $rowspan = 1){}

    function tablecell_close(){}
}