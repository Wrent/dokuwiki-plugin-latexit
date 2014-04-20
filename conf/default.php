<?php
/**
 * Default settings for the latexit plugin
 *
 * @author Adam Kučera <adam.kucera@wrent.cz>
 */

$conf['document_class'] = 'article';
$conf['font_size'] = '10';
$conf['paper_size'] = 'a4paper';
$conf['output_format'] = 'oneside';
$conf['landscape'] = 0;
$conf['draft'] = 0;
$conf['document_header'] = '\\usepackage[utf8x]{inputenc}
';
$conf['document_footer'] = '
';
$conf['document_lang'] = "english";
$conf['header_chapter'] = 0;
$conf['header_part'] = 0;
$conf['title'] = "";
$conf['author'] = "";
$conf['date'] = 0;
$conf['table_of_content'] = 0;
$conf['media_folder'] = 'media';
$conf['image_params'] = 'keepaspectratio=true,width=0.8\textwidth';
$conf['bibliography_style'] = 'plain';
$conf['bibliography_name'] = 'bibliography';
$conf['link_insertion_message'] = "Next link is recursively inserted.";
$conf['default_table_align'] = 'l';
$conf['showexportbutton'] = 1;