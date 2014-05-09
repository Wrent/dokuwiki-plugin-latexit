<?php

/**
 * Action tests for the latexit plugin
 *
 * @group plugin_latexit
 * @group plugins
 */
class action_plugin_latexit_test extends DokuWikiTest {

    /**
     * These plugins will be loaded for testing.
     * @var array
     */
    protected $pluginsEnabled = array('latexit', 'mathjax', 'imagereference', 'zotero');

    /**
     * Variable to store the instance of action plugin.
     * @var action_plugin_latexit
     */
    private $a;

    /**
     * Prepares the testing environment.
     */
    public function setUp() {
        parent::setUp();

        $this->a = new action_plugin_latexit();
    }

    /**
     * Testing _purgeCache method.
     * Checks, if the access time to conf file is newer or equal the current time.
     */
    public function test__purgeCache() {
        //create object from array, so it can be accessed via -> operator
        $data = array("mode" => "latexit");
        $data = (object) $data;
        //create new event
        $e = new Doku_Event("event", $data);
        //save current time
        $time = time();
        $this->a->_purgeCache($e, "");
        //get last access time
        $access_time = fileatime(DOKU_INC . 'conf/local.php');
        //test
        $this->assertGreaterThanOrEqual($time, $access_time);
    }

    /**
     * Testing _setLatexitSort method.
     * @global array $_GET Global var to simulate normal DW behaviour.
     */
    public function test__setLatexitSort() {
        //prepare the environment
        global $_GET;
        $_GET["do"] = 'export_latexit';
        //prepare the event object
        $data = array("mode" => "latexit");
        $data = (object) $data;
        $e = new Doku_Event("event", $data);
        $this->a->_setLatexitSort($e, "");
        //this has to be tested through the involved syntax plugin
        $syntax_plugin = plugin_load('syntax', 'latexit');
        //test
        $this->assertEquals(1, $syntax_plugin->getSort());
    }

}
