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
     * Prints all parameters, so they can be used in LaTeX \usepackage command
     * @return String List of parameters in right format.
     */

    public function printParameters() {
        $params = '';
        if (count($this->parameters) > 0) {
            $params .= '[';
            $first = true;
            foreach ($this->parameters as $p) {
                if(!$first) {
                    $params .= ", ";
                }
                $params .=  $p;
                $first = false;
            }
            $params .= ']';
            return $params;
        } else {
            return "";
        }
    }
    
    /**
     * Prints all commands, each on new line.
     * @return String Text of commands.
     */
    public function printCommands() {
        $commands = '';
        if (count($this->commands > 0)) {
            foreach ($this->commands as $c) {
                $commands .= $c."\n";
            }
            return $commands;
        } else {
            return "";
        }
    }

    /**
     * Returns the name of the package.
     * @return string Name of the package.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Check if the command has any parameters
     *
     * @return bool
     */
    protected function hasParameters() {
        return (bool) $this->parameters;
    }

    /**
     * Custom comparator to sort Packages
     *
     * @param Package $a
     * @param Package $b
     * @return int
     */
    static function cmpPackages($a, $b) {
        if($a->hasParameters() == $b->hasParameters()) {
            return 0;
        }
        return ($a->hasParameters() > $b->hasParameters()) ? -1 : +1;
    }

}
