<?php
include('config_parser.php');

function get_prov($path) {
	$p = new config_parser();
	$p->set_config($path);
	$p->parse();

	#echo $p->to_string();

	if (!@$p->kv['_main'][0]['state'][0]) {
		echo $path."\n";
		return array();
	}
	$l = $p->kv['_main'][0]['state'][0];
	$prov = array();
	foreach ($l as $k => $v) {
		if (@$v['provinces'] != NULL) {
			$prov = $v['provinces'][0];
		}
	}
	$arr = array();
	foreach ($prov as $k => $v) {
		foreach ($v as $kk => $vv) {
			$arr[] = $kk;
		}
	}
	sort($arr);
	return $arr;
}

$path = '../BW/history/states/';
$path1 = 'F:/SteamLibrary/steamapps/common/Hearts of Iron IV/history/states/';
$d = dir($path);
while (($f = $d->read()) !== false) {
	if ($f == '.' || $f == '..' || is_dir($path.$f)) continue;
	$p1 = get_prov($path.$f);
	$p2 = get_prov($path1.$f);
	$diff = array_diff($p1, $p2);
	if (count($diff) == 0) {
		$diff = array_diff($p2, $p1);
	}
	if (count($diff) > 0) {
		echo $f."\n";
	}
}
?>