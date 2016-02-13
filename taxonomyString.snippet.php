<?php
//Вызывается при вызове getResources. В качестве параметра принимает id корневого ресурса структуры каталога.
//Пример:
//
//[[!getResources? &parents=`id_базы_данных` &tpl=`object-list`&includeTVs=`1` &processTVs=`1`
//&string=`[[!taxonomyString? &parent=`id_корня_структуры`]]` 
//&tvFilters=`taxonomy==[[*id]]||taxonomy==[[*id]]|%||taxonomy==%|[[*id]]||taxonomy==%|[[*id]]|%`]]
//
//Подробнее на dyranov.ru/2015/02/taksonomiya-v-modx-revo-custom-tv-input/

//Формируем строку со всеми возможными параметрами TV вида: id_родителя:заголовок==id_документа||

$string = $modx->runSnippet('getResources',array('parents'=>$parent,'tpl'=>'@INLINE [[+parent]]:[[+pagetitle]]==[[+id]]||','showHidden'=>'1','limit'=>'100','hideContainers'=>'1'));
return $string;