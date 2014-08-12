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

    protected $packages;

    public function __construct(){
        $this->packages = array();
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
     * Get all added document packages
     */
    public function getPackages() {
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

}
