<?php

//this has to be set up to right path
require_once DOKU_INC . 'lib/plugins/latexit/classes/RowspanHandler.php';

/**
 * RowspanHandler tests for the latexit plugin
 *
 * @group plugin_latexit_classes
 * @group plugin_latexit
 * @group plugins
 */
class rowspanhandler_plugin_latexit_test extends DokuWikiTest {

    /**
     * These plugins will be loaded for testing.
     * @var array
     */
    protected $pluginsEnabled = array('latexit', 'mathjax', 'imagereference', 'zotero');

    /**
     * Variable to store the instance of the Rowspan Handler.
     * @var RowspanHandler
     */
    private $r;

    /**
     * Prepares the testing environment.
     */
    public function setUp() {
        parent::setUp();
        $this->r = new RowspanHandler();
    }

    /**
     * Testing getRowspan method.
     */
    public function test_getRowspan() {
        $this->r->insertRowspan(6, 3);
        $this->assertEquals(6, $this->r->getRowspan(3));
        //this rowspan does not exist => should return 0
        $this->assertEquals(0, $this->r->getRowspan(1));
    }

    /**
     * Testing decreaseRowspan method.
     */
    public function test_decreaseRowspan() {
        $this->r->insertRowspan(6, 3);
        $this->r->decreaseRowspan(3);
        $this->assertEquals(5, $this->r->getRowspan(3));

        $this->r->insertRowspan(1, 2);
        $this->assertEquals(1, $this->r->getRowspan(2));
        $this->r->decreaseRowspan(2);
        //Rowspan does not exist anymore now.
        $this->assertEquals(0, $this->r->getRowspan(2));
    }

}
