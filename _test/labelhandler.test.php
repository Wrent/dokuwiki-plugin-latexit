<?php

//this has to be set up to right path
require_once DOKU_INC . 'lib/plugins/latexit/classes/LabelHandler.php';

/**
 * LabelHandler tests for the latexit plugin
 *
 * @group plugin_latexit_classes
 * @group plugin_latexit
 * @group plugins
 */
class labelhandler_plugin_latexit_test extends DokuWikiTest {

    /**
     * These plugins will be loaded for testing.
     * @var array
     */
    protected $pluginsEnabled = array('latexit', 'mathjax', 'imagereference', 'zotero');

    /**
     * Variable to store the instance of the Label Handler.
     * @var LabelHandler
     */
    protected $l;

    /**
     * Prepares the testing environment.
     */
    public function setUp() {
        parent::setUp();
        $this->l = LabelHandler::getInstance();
    }

    /**
     * Testing newLabel method.
     */
    public function test_newLabel() {
        $this->assertEquals("label", $this->l->newLabel("label"));
        $this->assertEquals("stitek", $this->l->newLabel("stitek"));
        $this->assertEquals("label2", $this->l->newLabel("label"));
        $this->assertEquals("label3", $this->l->newLabel("label"));
    }

}
