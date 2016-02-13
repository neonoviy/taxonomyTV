<?php
/*
Adds taxonomyTV input type
*/
$corePath = $modx->getOption('core_path',null,MODX_CORE_PATH).'components/taxonomytv/';

//$modx->lexicon->load('assetstv:default');

switch ($modx->event->name) {
    case 'OnTVInputRenderList':
        $modx->event->output($corePath.'elements/tv/input/');
        break;
    case 'OnTVInputPropertiesList':
        $modx->event->output($corePath.'elements/tv/input/options/');
        break;
}