<?php

/**
 * DokuWiki Plugin latexit (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author     Andreas Gohr <gohr@cosmocode.de>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC'))
    die();

/**
 * Latexit uses some function to load plugins.
 */
require_once DOKU_INC . 'inc/pluginutils.php';

/**
 * Helper component that keeps certain configurations for multiple instances of the renderer
 */
class helper_plugin_latexit extends DokuWiki_Plugin {

    /** @var array list of Packages to load */
    protected $packages;

    /** @var  array the list of preamble commands */
    protected $preamble;

    /**
     * Constructor
     */
    public function __construct(){
        $this->packages = array();
        $this->preamble = array();
    }

    /**
     * Add a new entry to the preamble
     *
     * @param array|string $data Either the parameters for renderer_plugin_latexit::_c() or a string to be used as is
     */
    public function addPreamble($data){
        // make sure data contains the right info
        if(is_array($data)){
            if(!isset($data[0])) trigger_error('No command given', E_USER_ERROR); // command
            if(!isset($data[1])) $data[1] = null; // text
            if(!isset($data[2])) $data[2] = 1; // newlines
            if(!isset($data[3])) $data[3] = null; // params
        } else {
            if(substr($data,-1) != "\n") $data .= "\n";
        }

        $this->preamble[] = $data;
    }

    /**
     * Return the setup preamble as array
     *
     * @return array returns a reference to the preamble (allows modifying)
     */
    public function &getPreamble() {
        return $this->preamble;
    }

    /**
     * Add a package to the list of document packages
     *
     * @param Package $package
     */
    public function addPackage(Package $package){
        $name = $package->getName();
        if(isset($this->packages[$name])) return;
        $this->packages[$name] = $package;
    }

    /**
     * Get all added document packages (sorted)
     */
    public function getPackages() {
        // sort the packages
        usort($this->packages, array('Package', 'cmpPackages'));
        return $this->packages;
    }

    /**
     * Remove the given package from the package list
     *
     * @param string $name
     */
    public function removePackage($name) {
        if(isset($this->packages[$name])) unset($this->packages[$name]);
    }


    /**
     * Escapes LaTeX special chars.
     * Entities are in the middle of special tags so eg. MathJax texts are not escaped, but entities are.
     * @param string $text Text to be escaped.
     * @return string Escaped text.
     */
    static public function escape($text) {
        //find only entities in TEXT, not in eg MathJax
        preg_match('#///ENTITYSTART///(.*?)///ENTITYEND///#si', $text, $entity);
        //replace classic LaTeX escape chars
        $text = str_replace(
            array('\\', '{', '}', '&', '%', '$', '#', '_', '~', '^', '<', '>'),
            array('\textbackslash', '\{', '\}', '\&', '\%', '\$', '\#', '\_', '\textasciitilde{}', '\textasciicircum{}', '\textless ', '\textgreater '),
            $text);
        //finalize escaping
        $text = str_replace('\\textbackslash', '\textbackslash{}', $text);
        //replace entities in TEXT
        $text = preg_replace('#///ENTITYSTART///(.*?)///ENTITYEND///#si', $entity[1], $text);
        return $text;
    }
}
