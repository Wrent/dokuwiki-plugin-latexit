<?php

/**
 * Syntax tests for the latexit plugin
 *
 * @group plugin_latexit
 * @group plugins
 */
class syntax_plugin_latexit_base_test extends DokuWikiTest {

    /**
     * These plugins will be loaded for testing.
     * @var array
     */
    protected $pluginsEnabled = array('latexit', 'mathjax', 'imagereference', 'zotero');
    /**
     * Variable to store the instance of syntax plugin.
     * @var syntax_plugin_latexit
     */
    private $s;

    /**
     * Prepares the testing environment.
     */
    public function setUp() {
        parent::setUp();

        $this->s = new syntax_plugin_latexit_base();
    }

    /**
     * Testing getType method.
     */
    public function test_getType() {
        $this->assertEquals("substition", $this->s->getType());
    }

    /**
     * Testing getSort method.
     */
    public function test_getSort() {
        $this->assertEquals(245, $this->s->getSort());
        $this->s->_setSort(25);
        $this->assertEquals(25, $this->s->getSort());
    }

    /**
     * Testing isSingleton method.
     */
    public function test_isSingleton() {
        $this->assertTrue($this->s->isSingleton());
    }

    /**
     * Testing handle method.
     */
    public function test_handle() {
        //test zotero part of the method
        $r = $this->s->handle("\cite{bibliography}", "", 0, new Doku_Handler());
        $this->assertEquals("bibliography", $r);

        //test recursive insertion part of the method
        $r = $this->s->handle("~~~RECURSIVE~~~", "", 0, new Doku_Handler());
        $array = array("", array("~~~", "~~~"));
        $this->assertEquals($r, $array);
    }

    /**
     * Testing render method.
     */
    public function test_render() {
        //test recursive inserting part of method with xhtml renderer
        $r = new Doku_Renderer_xhtml();
        $data = array("", array("~~~", "~~~"));
        $result = $this->s->render("xhtml", $r, $data);
        $this->assertEquals("<h4>Next link is recursively inserted.</h4>", $r->doc);
        $this->assertTrue($result);
        
        //test recursive inserting part of method with latex renderer
        $r = new renderer_plugin_latexit();
        $data = array("", array("~~~", "~~~"));
        $result = $this->s->render("latex", $r, $data);
        $this->assertTrue($result);
        
        //test zotero of method
        $data = "bibliography";
        $result = $this->s->render("latex", $r, $data);
        $this->assertEquals("\\cite{bibliography}", $r->doc);
        $this->assertTrue($result);
                
        //test with not implemented rendering mode
        $result = $this->s->render("doc", $r, $data);
        $this->assertFalse($result);        
    }

}
