<?php

/**
 * Package is an entity representing a LaTeX package with its parameters.
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */

/**
 * Class representing a LaTeX package.
 */
class Package {

    /**
     * Name of the LaTeX package.
     * @var string 
     */
    protected $name;
    /**
     * Array of the package parameters.
     * @var array of strings 
     */
    protected $parameters;
    /**
     * Array of commands called after inserting the package.
     * @var array of strings
     */
    protected $commands;

    /**
     * Creates an Package object.
     * @param string $name Name of the package
     */

    public function __construct($name) {
        $this->name = $name;
        $this->parameters = array();
        $this->commands = array();
    }

    /**
     * Adds new parameter to the package and prevents duplicates.
     * @param string $name Name of the parameter
     */

    public function addParameter($name) {
        if (!in_array($name, $this->parameters)) {
            $this->parameters[] = $name;
        }
    }
    
    /**
     * Adds new command to the package and prevents duplicates.
     * @param string $command Command.
     */
    public function addCommand($command) {
        if (!in_array($command, $this->commands)) {
            $this->commands[] = $command;
        }
    }

    /**
     * Print this package ready to use
     *
     * @return string
     */
    public function printUsePackage() {
        $data  = '\\usepackage';
        $data .= $this->printParameters();
        $data .= '{';
        $data .= helper_plugin_latexit::escape($this->getName());
        $data .= "}\n";
        $data .= $this->printCommands();

        return $data;
    }

    /**
     * Prints all parameters, so they can be used in LaTeX \usepackage command
     * @return String List of parameters in right format.
     */
    public function printParameters() {
        if(!$this->countParameters()) return '';

        $parameters = $this->parameters;
        $parameters = array_map(array('helper_plugin_latexit', 'escape'), $parameters);
        $parameters = join(', ', $parameters);
        return '['.$parameters.']';
    }
    
    /**
     * Prints all commands, each on new line.
     * @return String Text of commands.
     */
    public function printCommands() {
        if(!count($this->commands)) return '';

        $commands = '';
        foreach ($this->commands as $c) {
            $commands .= $c."\n";
        }
        return $commands;
    }

    /**
     * Returns the name of the package.
     * @return string Name of the package.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Check the number of parameters a command has
     *
     * @return bool
     */
    protected function countParameters() {
        return count($this->parameters);
    }

    /**
     * Custom comparator to sort Packages
     *
     * @param Package $a
     * @param Package $b
     * @return int
     */
    static function cmpPackages($a, $b) {
        if($a->countParameters() == $b->countParameters()) {
            return 0;
        }
        return ($a->countParameters() > $b->countParameters()) ? -1 : +1;
    }

}
