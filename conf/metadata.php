<?php

/**
 * Options for the latexit plugin
 *
 * @author Adam Kučera <adam.kucera@wrent.cz>
 */

$meta['document_class'] = array('multichoice', '_choices' => 
    array('article', 'report', 'book', 'memoir'));
$meta['font_size'] = array('numeric');
$meta['paper_size'] = array('multichoice', '_choices' => 
    array('a4paper', 'letterpaper', 'b5paper', 'executivepaper', 'legalpaper'));
$meta['output_format'] = array('multichoice', '_choices' => 
    array('oneside', 'twoside'));
$meta['landscape'] = array('onoff');
$meta['draft'] = array('onoff');
$meta['document_header'] = '';
$meta['document_header'] = '';
//http://ftp.snt.utwente.nl/pub/software/tex/macros/latex/required/babel/base/babel.pdf page 16
$meta['document_lang'] = array('multichoice', '_choices' =>
    array(
        'afrikaans',
        'bahasa',
        'basque',
        'breton',
        'bulgarian',
        'catalan',
        'croatian',
        'czech',
        'danish',
        'dutch',
        'english',
        'esperanto',
        'estonian',
        'finnish',
        'french',
        'galician',
        'german',
        'greek',
        'hebrew',
        'icelandic',
        'interlingua',
        'irish',
        'italian',
        'latin',
        'lowersorbian',
        'samin',
        'norsk',
        'polish',
        'portuges',
        'romanian',
        'russian',
        'scottish',
        'spanish',
        'slovak',
        'slovene',
        'swedish',
        'serbian',
        'turkish',
        'ukrainian',
        'uppersorbian',
        'welsh',
        ));
$meta['header_chapter'] = array('onoff');
$meta['header_part'] = array('onoff');
$meta['title'] = array('string');
$meta['author'] = array('string');
$meta['date'] = array('onoff');
$meta['table_of_content'] = array('onoff');
$meta['default_table_align'] = array('multichoice', '_choices' => array('l', 'c', 'r'));
