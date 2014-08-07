<?php

//this has to be set up to right path
require_once DOKU_INC . 'lib/plugins/latexit/classes/RecursionHandler.php';

/**
 * RecursionHandler tests for the latexit plugin
 *
 * @group plugin_latexit_classes
 * @group plugin_latexit
 * @group plugins
 */
class recursionhandler_plugin_latexit_test extends DokuWikiTest {

    /**
     * These plugins will be loaded for testing.
     * @var array
     */
    protected $pluginsEnabled = array('latexit', 'mathjax', 'imagereference', 'zotero');

    /**
     * Variable to store the instance of the Recursion Handler.
     * @var RecursionHandler
     */
    protected $r;

    /**
     * Prepares the testing environment.
     */
    public function setUp() {
        parent::setUp();
        $this->r = RecursionHandler::getInstance();
    }

    /**
     * Testing disallow, insert and remove methods.
     */
    public function test_disallow() {
        $this->r->insert("page");
        $this->assertTrue($this->r->disallow("page"));
        $this->assertFalse($this->r->disallow("anotherpage"));
        $this->r->remove("page");
        $this->assertFalse($this->r->disallow("page"));
    }

}
