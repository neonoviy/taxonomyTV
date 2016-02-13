<?php
//Строит ссылки из данных в TV taxonomy и строки, полученной от taxonomyString.
//Вызывается в шаблоне getResources
//Принимает 2 параметра:  tvs — TV taxonomy и string — это строка от первого сниппета.
//Пример вызова:
//[[!taxonomyLinks? &string=`[[+string]]` &tvs=`[[+taxonomy]]`]].
//
//Подробнее на dyranov.ru/2015/02/taksonomiya-v-modx-revo-custom-tv-input/

$o = '';
$currentId = $modx->resource->get('id');
//Преобразуем значения TV в массив и проходим по нему. Значения TV имеют вид: id||id2||id5...
$tvarr = explode('||', $tvs);
foreach ($tvarr as $tv) {

//Преобразуем строку от makestring в массив и проходим по нему. Строка имеет вид: id_родителя:заголовок==id||id_родителя:заголовок2==id2...
$strarr = explode('||', $string);
foreach($strarr as $str) {

//Если id в строке соответствует обрабатываему tv...
	$line1 = explode('==', $str);
    $id = $line1[1];
//...разбиваем строку по составляющим и формируем ссылку
	if($id == $tv){
	$parentName = $line1[0];
    $line2 = explode(':', $parentName);
	$parent = $line2[0];
//Убираем лишние пробелы у id родителя
	$parent = trim ($parent, " \t\n\r\0\x0B"  );
	$name = $line2[1];
if ($currentId == $tv){
$o .='<span class="taxonomy.taxgroup-'.$parent.'">'.$name.'</span> ';
}else{
$o .='<a href="[[~'.$tv.'~]]" class="taxonomy.taxgroup-'.$parent.'">'.$name.'</a> ';
}
    }
}
}
return $o;