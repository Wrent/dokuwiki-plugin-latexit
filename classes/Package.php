<?php

/**
 * Package is an entity representing a LaTeX package with its parameters.
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adam Kučera <adam.kucera@wrent.cz>
 */
class Package {

    private $name;
    private $parameters;
    private $commands;

    /**
     * Creates an Package object.
     * @param $name Name of the package
     */

    public function __construct($name) {
        $this->name = $name;
        $this->parameters = array();
        $this->commands = array();
    }

    /**
     * Adds new parameter to the package and prevents duplicates.
     * @param $name Name of the parameter
     */

    public function addParameter($name) {
        if (!in_array($name, $this->parameters)) {
            $this->parameters[] = $name;
        }
    }
    
    public function addCommand($command) {
        if (!in_array($command, $this->commands)) {
            $this->commands[] = $command;
        }
    }

    /**
     * Prints all parameters, so they can be used in LaTeX \usepackage command
     * @return String List of parameters in right format.
     */

    public function printParameters() {
        if (count($this->parameters > 0)) {
            foreach ($this->parameters as $p) {
                $params .= '[' . $p . ']';
            }
            return $params;
        } else {
            return "";
        }
    }
    
    public function printCommands() {
        if (count($this->commands > 0)) {
            foreach ($this->commands as $c) {
                $commands .= $c."\n";
            }
            return $commands;
        } else {
            return "";
        }
    }

    public function getName() {
        return $this->name;
    }

}
