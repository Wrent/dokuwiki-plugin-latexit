<?php

//this has to be set up to right path
require_once DOKU_INC . 'lib/plugins/latexit/classes/Rowspan.php';

/**
 * Rowspan tests for the latexit plugin
 *
 * @group plugin_latexit_classes
 * @group plugin_latexit
 * @group plugins
 */
class rowspan_plugin_latexit_test extends DokuWikiTest {

    /**
     * These plugins will be loaded for testing.
     * @var array
     */
    protected $pluginsEnabled = array('latexit', 'mathjax', 'imagereference', 'zotero');

    /**
     * Testing getRowspan and setRowspan methods.
     */
    public function test_getRowspan() {
        $r = new Rowspan(3, 2);
        $this->assertEquals(3, $r->getRowspan());
        $r->setRowspan(5);
        $this->assertEquals(5, $r->getRowspan());
    }

    /**
     * Testing getCellId and setCellId methods.
     */
    public function test_getCellId() {
        $r = new Rowspan(3, 2);
        $this->assertEquals(2, $r->getCellId());
        $r->setCellId(4);
        $this->assertEquals(4, $r->getCellId());
    }

}
