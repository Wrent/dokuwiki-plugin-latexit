<?php

//this has to be set up to right path
require_once DOKU_INC . 'lib/plugins/latexit/classes/Package.php';

/**
 * Package tests for the latexit plugin
 *
 * @group plugin_latexit_classes
 * @group plugin_latexit
 * @group plugins
 */
class paclage_plugin_latexit_test extends DokuWikiTest {

    /**
     * These plugins will be loaded for testing.
     * @var array
     */
    protected $pluginsEnabled = array('latexit', 'mathjax', 'imagereference', 'zotero');

    /**
     * Testing getName method.
     */
    public function test_getName() {
        $p = new Package("balik");
        $this->assertEquals("balik", $p->getName());
    }

    /**
     * Testing printParameters and addParameter methods.
     */
    public function test_printParameters() {
        $p = new Package("balik");
        $this->assertEquals("", $p->printParameters());
        $p->addParameter("param");
        $this->assertEquals("[param]", $p->printParameters());
        $p->addParameter("p");
        $this->assertEquals("[param, p]", $p->printParameters());
    }

    /**
     * Testing printCommands and addCommand methods.
     */
    public function test_printCommands() {
        $p = new Package("balik");
        $this->assertEquals("", $p->printCommands());
        $p->addCommand("\use{aaa}");
        $this->assertEquals("\use{aaa}\n", $p->printCommands());
        $p->addCommand("\remove{bbb}");
        $this->assertEquals("\use{aaa}\n\remove{bbb}\n", $p->printCommands());
    }

}
