<?php
$root = 'E:/SteamLibrary/steamapps/common/Hearts of Iron IV/map/supplyareas/';

function ddd($path) {
	global $root;
	$d = dir($root.$path);
	while (($f = $d->read()) !== false) {
		if ($f == '.' || $f == '..') continue;
		$file = $path.'/'.$f;
		if (is_dir($root.$file)) {
			ddd($file);
			continue;
		}
		echo $file."\n";
		$new_file = 'c:/tmp/'.$file;
		$_new_dir = dirname($new_file);
		@mkdir($_new_dir);
		touch($new_file);
	}
}

ddd('/');