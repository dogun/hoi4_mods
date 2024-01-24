<?php
$path = 'D:/SteamLibrary/steamapps/common/Hearts of Iron IV/gfx/leaders';

function ddd($path) {
$d = dir($path);
	while (($f = $d->read()) !== false) {
		if ($f == '.' || $f == '..') continue;
		$file = $path.'/'.$f;
		if (is_dir($file)) {
			ddd($file);
			continue;
		}
		if (!strstr($f, 'dds')) continue;
		echo $file."\n";
		$new_file = 'c:/tmp/'.str_replace('.dds', '.png', $f);
		try {
			$img = new Imagick();
			$img->readImage($file);
			$img->setFormat('PNG');
			$img->writeImage($new_file);
		} catch (Exception $e) {
			echo "   ".$e->getMessage()."\n";
		}
	}
}

ddd($path);