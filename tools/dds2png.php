<?php
$path = 'D:/SteamLibrary/steamapps/common/Hearts of Iron IV/gfx/interface/goals';
$d = dir($path);
while (($f = $d->read()) !== false) {
	if (!strstr($f, 'dds')) continue;
	$file = $path.'/'.$f;
	$new_file = 'c:/tmp/'.str_replace('.dds', '.png', $f);
	$img = new Imagick();
	$img->readImage($file);
	$img->setFormat('PNG');
	$img->writeImage($new_file);
}