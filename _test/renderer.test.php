<?php

/**
 * Renderer tests for the latexit plugin
 *
 * @group plugin_latexit
 * @group plugins
 */
class renderer_plugin_latexit_test extends DokuWikiTest {

    protected $pluginsEnabled = array('latexit', 'mathjax', 'imagereference', 'zotero');
    private $r;

    public function setUp() {
        parent::setUp();

        $this->r = new renderer_plugin_latexit();
        $this->r->document_start();
        $this->clearDoc();
    }

    private function clearDoc() {
        $this->r->doc = '';
    }

    public function test_canRender() {
        $this->assertTrue($this->r->canRender('latex'));
        $this->assertFalse($this->r->canRender('xhtml'));
        $this->assertFalse($this->r->canRender('affsd'));
    }

    public function test_getFormat() {
        $this->assertEquals('latex', $this->r->getFormat());
    }

    public function test_isSingleton() {
        $this->assertFalse($this->r->isSingleton());
    }

    public function test_document_start() {
        $this->r->document_start();
        $string = "\documentclass[a4paper, oneside, 10pt]{article}\n\usepackage[utf8x]{inputenc}"
                . "\n\usepackage[english]{babel}\n~~~PACKAGES~~~\begin{document}\n\n";

        $this->assertEquals($string, $this->r->doc);
        //FIXME test conf?
    }

    public function test_header() {
        $this->r->header("header", 1, 5);
        $string = "\n\n\section{\\texorpdfstring{header}{header}}\n\label{sec:header}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->header("Nadpis", 2, 245);
        $string = "\n\n\subsection{\\texorpdfstring{Nadpis}{Nadpis}}\n\label{sec:nadpis}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->header("Článek 5", 3, 575);
        $string = "\n\n\subsubsection{\\texorpdfstring{Článek 5}{Clanek 5}}\n\label{sec:clanek_5}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->header("sdfsdfsdk sdf ", 4, 7525);
        $string = "\n\n\paragraph{\\texorpdfstring{sdfsdfsdk sdf }{sdfsdfsdk sdf }}\n\label{sec:sdfsdfsdk_sdf_}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->header("Nadpis", 5, 4525);
        $string = "\n\n\subparagraph{\\texorpdfstring{Nadpis}{Nadpis}}\n\label{sec:nadpis2}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->header("Nadpis", 6, 4525);
        $string = "\n\n~\\newline\n\\textbf{Nadpis}\n\label{sec:nadpis3}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
    }

    public function test_cdata() {
        $this->r->cdata("text");
        $string = "text";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->cdata('\{}&%$#_~^<>');
        $string = '\textbackslash{}\{\}\&\%\$\#\_\textasciitilde{}\textasciicircum{}\textless \textgreater ';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
    }

    public function test_p_open() {
        $this->r->p_open();
        $this->assertEquals("\n\n", $this->r->doc);
    }

    public function test_linebreak() {
        $this->r->linebreak();
        $this->assertEquals("\\\\", $this->r->doc);
    }

    public function test_hr() {
        $this->r->hr();
        $string = "\n\n\\begin{center}\n\\line(1,0){250}\n\\end{center}\n\n";
        $this->assertEquals($string, $this->r->doc);
    }

    public function test_strong_open() {
        $this->r->strong_open();
        $this->assertEquals("\\textbf{", $this->r->doc);
    }

    public function test_emphasis_open() {
        $this->r->emphasis_open();
        $this->assertEquals("\\emph{", $this->r->doc);
    }

    public function test_underline_open() {
        $this->r->underline_open();
        $this->assertEquals("\\underline{", $this->r->doc);
    }

    public function test_monospace_open() {
        $this->r->monospace_open();
        $this->assertEquals("\\texttt{", $this->r->doc);
    }

    public function test_subscript_open() {
        $this->r->subscript_open();
        $this->assertEquals("\\textsubscript{", $this->r->doc);
    }

    public function test_superscript_open() {
        $this->r->superscript_open();
        $this->assertEquals("\\textsuperscript{", $this->r->doc);
    }

    public function test_deleted_open() {
        $this->r->deleted_open();
        $this->assertEquals("\\sout{", $this->r->doc);
    }

    public function test_footnote_open() {
        $this->r->footnote_open();
        $this->assertEquals("\\footnote{", $this->r->doc);
    }

    public function test_strong_close() {
        $this->r->strong_close();
        $this->assertEquals("}", $this->r->doc);
    }

    public function test_emphasis_close() {
        $this->r->emphasis_close();
        $this->assertEquals("}", $this->r->doc);
    }

    public function test_underline_close() {
        $this->r->underline_close();
        $this->assertEquals("}", $this->r->doc);
    }

    public function test_monospace_close() {
        $this->r->monospace_close();
        $this->assertEquals("}", $this->r->doc);
    }

    public function test_subscript_close() {
        $this->r->subscript_close();
        $this->assertEquals("}", $this->r->doc);
    }

    public function test_superscript_close() {
        $this->r->superscript_close();
        $this->assertEquals("}", $this->r->doc);
    }

    public function test_deleted_close() {
        $this->r->deleted_close();
        $this->assertEquals("}", $this->r->doc);
    }

    public function test_footnote_close() {
        $this->r->footnote_close();
        $this->assertEquals("}", $this->r->doc);
    }

    public function test_listu_open() {
        $this->r->listu_open();
        $this->assertEquals("\n\\begin{itemize}\n", $this->r->doc);
    }

    public function test_listo_open() {
        $this->r->listo_open();
        $this->assertEquals("\n\\begin{enumerate}\n", $this->r->doc);
    }

    public function test_listu_close() {
        $this->r->listu_close();
        $this->assertEquals("\\end{itemize}\n", $this->r->doc);
    }

    public function test_listo_close() {
        $this->r->listo_close();
        $this->assertEquals("\\end{enumerate}\n", $this->r->doc);
    }

    public function test_listitem_open() {
        $this->r->listitem_open(1);
        $this->assertEquals("  \\item", $this->r->doc);
        $this->clearDoc();

        $this->r->listitem_open(2);
        $this->assertEquals("    \\item", $this->r->doc);
    }

    public function test_listcontent_close() {
        $this->r->listcontent_close();
        $this->assertEquals("\n", $this->r->doc);
    }

    public function test_unformatted() {
        $this->r->unformatted("text");
        $string = "text";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->unformatted('\{}&%$#_~^<>');
        $string = '\textbackslash{}\{\}\&\%\$\#\_\textasciitilde{}\textasciicircum{}\textless \textgreater ';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
    }

    public function test_php() {
        $this->r->php("echo \"test\";");
        $string = "\\lstset{frame=single, language=PHP}\n\\begin{lstlisting}\necho \"test\";\\end{lstlisting}\n\n";
        $this->assertEquals($string, $this->r->doc);
    }

    public function test_phpblock() {
        $this->r->phpblock("echo \"test\";");
        $string = "\\lstset{frame=single, language=PHP}\n\\begin{lstlisting}\necho \"test\";\\end{lstlisting}\n\n";
        $this->assertEquals($string, $this->r->doc);
    }

    public function test_html() {
        $this->r->html("<html><b>Hello World!</b></html>");
        $string = "\\lstset{frame=single, language=HTML}\n\\begin{lstlisting}\n<html><b>Hello World!</b></html>\\end{lstlisting}\n\n";
        $this->assertEquals($string, $this->r->doc);
    }

    public function test_htmlblock() {
        $this->r->htmlblock("<html><b>Hello World!</b></html>");
        $string = "\\lstset{frame=single, language=HTML}\n\\begin{lstlisting}\n<html><b>Hello World!</b></html>\\end{lstlisting}\n\n";
        $this->assertEquals($string, $this->r->doc);
    }

    public function test_preformatted() {
        $this->r->preformatted("    no format a a    ");
        $string = "\n\\begin{verbatim}\n    no format a a    \n\\end{verbatim}\n";
        $this->assertEquals($string, $this->r->doc);
    }

    public function test_quote_open() {
        $this->r->quote_open();
        $this->assertEquals("\n\\begin{quote}\n", $this->r->doc);
    }

    public function test_quote_close() {
        $this->r->quote_close();
        $this->assertEquals("\n\\end{quote}\n", $this->r->doc);
    }

    public function test_file() {
        $this->r->file("std::cout \"Hello world!\";", "C++");
        $string = "\\lstset{frame=single, language=C++}\n\\begin{lstlisting}\nstd::cout \"Hello world!\";\\end{lstlisting}\n\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->file("std::cout \"Hello world!\";", "C++", "script.cpp");
        $string = "\\lstset{frame=single, language=C++, title=script.cpp}\n\\begin{lstlisting}\nstd::cout \"Hello world!\";\\end{lstlisting}\n\n";
        $this->assertEquals($string, $this->r->doc);
    }

    public function test_code() {
        $this->r->code("std::cout \"Hello world!\";", "C++");
        $string = "\\lstset{frame=single, language=C++}\n\\begin{lstlisting}\nstd::cout \"Hello world!\";\\end{lstlisting}\n\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->code("std::cout \"Hello world!\";", "C++", "script.cpp");
        $string = "\\lstset{frame=single, language=C++, title=script.cpp}\n\\begin{lstlisting}\nstd::cout \"Hello world!\";\\end{lstlisting}\n\n";
        $this->assertEquals($string, $this->r->doc);
    }

    public function test_acronym() {
        $this->r->acronym("text");
        $string = "text";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->acronym('\{}&%$#_~^<>');
        $string = '\textbackslash{}\{\}\&\%\$\#\_\textasciitilde{}\textasciicircum{}\textless \textgreater ';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
    }

    public function test_smiley() {
        $this->r->smiley("text");
        $string = "text";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->smiley('\{}&%$#_~^<>');
        $string = '\textbackslash{}\{\}\&\%\$\#\_\textasciitilde{}\textasciicircum{}\textless \textgreater ';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->smiley(':-)');
        $string = ':-)';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->smiley('FIXME');
        $string = 'FIXME';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
    }

    public function test_entity() {
        $this->r->entity("->");
        $string = "///ENTITYSTART///";
        $string .= '$\rightarrow$';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->entity("<-");
        $string = "///ENTITYSTART///";
        $string .= '$\leftarrow$';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->entity("<->");
        $string = "///ENTITYSTART///";
        $string .= '$\leftrightarrow$';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->entity("=>");
        $string = "///ENTITYSTART///";
        $string .= '$\Rightarrow$';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->entity("<=");
        $string = "///ENTITYSTART///";
        $string .= '$\Leftarrow$';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->entity("<=>");
        $string = "///ENTITYSTART///";
        $string .= '$\Leftrightarrow$';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->entity("(c)");
        $string = "///ENTITYSTART///";
        $string .= '\copyright ';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->entity("(tm)");
        $string = "///ENTITYSTART///";
        $string .= '\texttrademark ';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->entity("(r)");
        $string = "///ENTITYSTART///";
        $string .= '\textregistered ';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();

        $this->r->entity('\{}&%$#_~^<>');
        $string = "///ENTITYSTART///";
        $string .= '\textbackslash{}\{\}\&\%\$\#\_\textasciitilde{}\textasciicircum{}\textless \textgreater ';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
    }

    public function test_multiplyentity() {
        $this->r->multiplyentity('\{}&%$', '#_~^<>');
        $string = "///ENTITYSTART///";
        $string .= '$\textbackslash{}\{\}\&\%\$ \times \#\_\textasciitilde{}\textasciicircum{}\textless \textgreater $';
        $string .= '///ENTITYEND///';
        $this->assertEquals($string, $this->r->doc);
    }

    public function test_singlequoteopening() {
        $this->r->singlequoteopening();
        $this->assertEquals('`', $this->r->doc);
    }

    public function test_singlequoteclosing() {
        $this->r->singlequoteclosing();
        $this->assertEquals('\'', $this->r->doc);
    }

    public function test_apostrophe() {
        $this->r->apostrophe();
        $this->assertEquals('\'', $this->r->doc);
    }

    public function test_doublequoteopening() {
        $this->r->doublequoteopening();
        $this->assertEquals(',,', $this->r->doc);
    }

    public function test_doublequoteclosing() {
        $this->r->doublequoteclosing();
        $this->assertEquals('"', $this->r->doc);
    }

    public function test_internallink() {
        $this->r->internallink("NotExistingCamelCase", "NotExistingCamelCase");
        $this->assertEquals('NotExistingCamelCase', $this->r->doc);     
    }

    public function test_locallink() {
        $this->r->locallink("section", "Odkaz");
        $this->assertEquals("Odkaz (\\autoref{section})", $this->r->doc);
        $this->clearDoc();
        
        $this->r->locallink("section");
        $this->assertEquals("section (\\autoref{section})", $this->r->doc);
    }

    public function test_externallink() {
        $this->r->externallink("http://url.com", "Odkaz");
        $this->assertEquals("\\href{http://url.com}{Odkaz}", $this->r->doc);
        $this->clearDoc();
        
        $this->r->externallink("http://url.com");
        $this->assertEquals("\\url{http://url.com}", $this->r->doc);
    }

    public function test_interwikilink() {
        $this->r->interwikilink("doku>Interwiki", NULL, "doku", "Interwiki");
        $this->assertEquals("\\href{http://www.google.com/search?q=Interwiki\&amp;btnI=lucky}{Interwiki}", $this->r->doc);
    }
    
    public function test_filelink() {
        $this->r->filelink("file:///P:/Manuals/UserManual.pdf", "text");
        $this->assertEquals("\\href{file:///P:/Manuals/UserManual.pdf}{text}", $this->r->doc);
        $this->clearDoc();
        
        $this->r->filelink("file:///P:/Manuals/UserManual.pdf");
        $this->assertEquals("\\url{file:///P:/Manuals/UserManual.pdf}", $this->r->doc);
    }
    
    public function test_windowssharelink() {
        $this->r->windowssharelink("\\server\share", "text");
        $this->assertEquals("\\href{\\\\server\\\\share}{text}", $this->r->doc);
        $this->clearDoc();
        
        $this->r->windowssharelink("\\server\share");
        $this->assertEquals("\\url{\\\\server\\\\share}", $this->r->doc);
    }
    
    public function test_emaillink() {
        $this->r->emaillink("email@domain.com", "Email");
        $this->assertEquals("\\href{mailto:email@domain.com}{Email}", $this->r->doc);
        $this->clearDoc();
        
        $this->r->emaillink("email@domain.com");
        $this->assertEquals("\\href{mailto:email@domain.com}{email@domain.com}", $this->r->doc);
    }
    
    public function test_internalmedia() {
        $this->r->internalmedia("pic:picture.png", "aaa", "left");
        $string = "\\raggedleft\\includegraphics[keepaspectratio=true,width=0.8\\textwidth]{picture}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->internalmedia("pic:picture.png", "aaa", "center");
        $string = "\\centering\\includegraphics[keepaspectratio=true,width=0.8\\textwidth]{picture}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->internalmedia("pic:picture.png", "aaa", "right");
        $string = "\\raggedright\\includegraphics[keepaspectratio=true,width=0.8\\textwidth]{picture}\n";
        $this->assertEquals($string, $this->r->doc);
    }
    
        public function test_externalmedia() {
        $this->r->externalmedia("http://url.com/picture.png", "aaa", "left");
        $string = "\\raggedleft\\includegraphics[keepaspectratio=true,width=0.8\\textwidth]{picture}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->externalmedia("http://url.com/picture.png", "aaa", "center");
        $string = "\\centering\\includegraphics[keepaspectratio=true,width=0.8\\textwidth]{picture}\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->externalmedia("http://url.com/picture.png", "aaa", "right");
        $string = "\\raggedright\\includegraphics[keepaspectratio=true,width=0.8\\textwidth]{picture}\n";
        $this->assertEquals($string, $this->r->doc);
    }
    
    public function test_table_open() {
        $this->r->table_open(5);
        $string = "\\begin{longtable}{|l|l|l|l|l|}\n\\hline\n";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->table_open(3);
        $string = "\\begin{longtable}{|l|l|l|}\n\\hline\n";
        $this->assertEquals($string, $this->r->doc);
    }
    
    public function test_table_close() {
        $this->r->table_close();
        $string = "\\end{longtable}\n\n";
        $this->assertEquals($string, $this->r->doc);
    }
    
    public function test_tablerow_close() {
        $this->r->tablerow_close();
        $string = " \\\\ \n\\hline\n \n";
        $this->assertEquals($string, $this->r->doc);
    }
    
    public function test_tableheader_open() {
        $this->r->tableheader_open(1,"r",1);
        $string = "\\multicolumn{1}{|r|}{\\textbf{";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->tableheader_open(1,NULL,1);
        $string = "\\textbf{";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->tableheader_open(2,NULL,1);
        $string = "\\multicolumn{2}{|l|}{\\textbf{";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->tableheader_open(1,NULL,2);
        $string = "\\multirow{2}{*}{\\textbf{";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        $this->setUp();
        
        $this->r->tableheader_open(2,NULL,2);
        $string = "\\multicolumn{2}{|l|}{\\multirow{2}{*}{\\textbf{";
        $this->assertEquals($string, $this->r->doc);
    }
    
    public function test_tableheader_close() {
        $this->r->tableheader_open();
        $this->clearDoc();
        $this->r->tableheader_close();
        $this->assertEquals("} & ", $this->r->doc);
        $this->clearDoc();
        
        $this->r->tableheader_open(2);
        $this->clearDoc();
        $this->r->tableheader_close();
        $this->assertEquals("}} & ", $this->r->doc);
        $this->clearDoc();
        $this->setUp();
        
        $this->r->tableheader_open(1, "r");
        $this->clearDoc();
        $this->r->tableheader_close();
        $this->assertEquals("}} & ", $this->r->doc);
        $this->clearDoc();
        $this->setUp();
        
        $this->r->tableheader_open(1, "l", 2);
        $this->clearDoc();
        $this->r->tableheader_close();
        $this->assertEquals("}} & ", $this->r->doc);
        $this->clearDoc();
        $this->setUp();
        
        $this->r->tableheader_open(2, "l", 2);
        $this->clearDoc();
        $this->r->tableheader_close();
        $this->assertEquals("}}} & ", $this->r->doc);
        $this->clearDoc();
        $this->setUp();
    }
            
    public function test_tablecell_open() {
        $this->r->tablecell_open(1,"r",1);
        $string = "\\multicolumn{1}{|r|}{";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->tablecell_open(1,NULL,1);
        $string = "";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->tablecell_open(2,NULL,1);
        $string = "\\multicolumn{2}{|l|}{";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        
        $this->r->tablecell_open(1,NULL,2);
        $string = "\\multirow{2}{*}{";
        $this->assertEquals($string, $this->r->doc);
        $this->clearDoc();
        $this->setUp();
        
        $this->r->tablecell_open(2,NULL,2);
        $string = "\\multicolumn{2}{|l|}{\\multirow{2}{*}{";
        $this->assertEquals($string, $this->r->doc);
    }
    
    public function test_tablecell_close() {
        $this->r->tablecell_open();
        $this->clearDoc();
        $this->r->tablecell_close();
        $this->assertEquals(" & ", $this->r->doc);
        $this->clearDoc();
        
        $this->r->tablecell_open(2);
        $this->clearDoc();
        $this->r->tablecell_close();
        $this->assertEquals("} & ", $this->r->doc);
        $this->clearDoc();
        $this->setUp();
        
        $this->r->tablecell_open(1, "r");
        $this->clearDoc();
        $this->r->tablecell_close();
        $this->assertEquals("} & ", $this->r->doc);
        $this->clearDoc();
        $this->setUp();
        
        $this->r->tablecell_open(1, "l", 2);
        $this->clearDoc();
        $this->r->tablecell_close();
        $this->assertEquals("} & ", $this->r->doc);
        $this->clearDoc();
        $this->setUp();
        
        $this->r->tablecell_open(2, "l", 2);
        $this->clearDoc();
        $this->r->tablecell_close();
        $this->assertEquals("}} & ", $this->r->doc);
        $this->clearDoc();
        $this->setUp();
    }
    
    public function test__mathMode() {
        $this->r->_mathMode("$\lnot$");
        $this->assertEquals("$\\lnot$", $this->r->doc);
        $this->clearDoc();
        
        $this->r->_mathMode("$-> <- <-> => <= <=> ... −$");
        $string = '$\rightarrow \leftarrow \leftrightarrow \Rightarrow \Leftarrow \Leftrightarrow \ldots -$';
        $this->assertEquals($string, $this->r->doc);
        
    }
}
