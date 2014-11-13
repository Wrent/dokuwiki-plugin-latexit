<?php

//this has to be set up to right path
require_once DOKU_INC . 'lib/plugins/latexit/classes/BibHandler.php';
//this has to be set up to the original pages dir
define("PAGES", DOKU_INC . 'u44e7ehr22wd/pages');

/**
 * BibHandler tests for the latexit plugin
 * To let the testing work, you have to have zotero plugin installed and set up.
 * You have to have the entries, which are inserted in your zotero repository.
 *
 * @group plugin_latexit_classes
 * @group plugin_latexit
 * @group plugins
 */
class bibhandler_plugin_latexit_test extends DokuWikiTest {

    /**
     * These plugins will be loaded for testing.
     * @var array
     */
    protected $pluginsEnabled = array('latexit', 'mathjax', 'imagereference', 'zotero');

    /**
     * Variable to store the instance of the Bibliography Handler.
     * @var BibHandler
     */
    protected $b;

    /**
     * Prepares the testing environment
     * @global array $conf Stores configuration information
     */
    public function setUp() {
        global $conf;

        parent::setUp();

        if(!is_dir(DOKU_PLUGIN.'zotero')) {
            $this->markTestSkipped('Zotero Plugin is not installed');
            return;
        }

        //create page folder for zotero sources.
        $dir = $conf["datadir"] . '/zotero/';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        
        //copy zotero sources
        copy(dirname(PAGES) . '/pages/zotero/sources.txt', $dir . '/sources.txt');
        $this->b = BibHandler::getInstance();
    }

    /**
     * Testing isEmpty method.
     */
    public function test_isEmpty() {
        $this->assertTrue($this->b->isEmpty());
        //you have to have this entry in your repository!
        $this->b->insert("bib:agile-manifesto");
        $this->assertFalse($this->b->isEmpty());
    }

    /**
     * Testing getBibtex method.
     */
    public function test_getBibtex() {
        //you have to have this entry in your repository!
        $this->b->insert("bib:arlow-uml2-up");
        //this string has to be the bibtex format of your entries
        $string = "
@misc{bib:agile-manifesto,
	title = {Manifesto for Agile Software Development},
	lccn = {0987},
	shorttitle = {bib:agile-manifesto},
	url = {http://agilemanifesto.org},
	author = {{Kent Beck} and {Mike Beedle} and {Arie van Bennekum} and {Alistair Cockburn} and {Ward Cunningham} and {Martin Fowler} and {James Grenning} and {Jim Highsmith} and {Andrew Hunt} and {Ron Jeffries} and {Jon Kern} and {Brian Marick} and {Robert C. Martin} and {Steve Mellor} and {Ken Schwaber} and {Jeff Sutherland} and {Dave Thomas}},
	year = {2001},
	note = {01246},
	keywords = {agile methodologies, software engineering}
}


@book{bib:arlow-uml2-up,
	title = {{UML} 2.0 and the Unified Process: Practical Object-Oriented Analysis and Design (2nd Edition)},
	isbn = {0321321278},
	lccn = {0000},
	shorttitle = {bib:arlow-uml2-up},
	publisher = {Addison-Wesley Professional},
	author = {Arlow, Jim and Neustadt, Ila},
	year = {2005},
	note = {00000},
	keywords = {modelling, uml}
}

";
        $this->assertEquals($string, $this->b->getBibtex());
    }

}
