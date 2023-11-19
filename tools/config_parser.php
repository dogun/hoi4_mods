<?php
class config_parser {
	public $kv = array();
	private $control_c = array(' ', "\t", "\n", "\r", '#', '{', '}', '"', '=');
	private $n_str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_.';
	private $config;
	private $str;
	private $index = 0;
	
	function _is_control($c) {
		foreach($this->control_c as $v) {
			if ($v == $c) return true;
		}
		return false;
	}
	
	function _is_n_str($str) {
		for ($i = 0; $i < strlen($str); ++$i) {
			$c = $str[$i];
			if (!strstr($this->n_str, $c)) return false;
		}
		return true;
	}
	
	function set_config($config) {
		if (!$config) die('config null');
		$this->config = $config;
		$this->str = file_get_contents($this->config) or die('open config error');
		$this->index = 0;
	}
	
	function parse() {
		if (!$this->str) die('set_config first');
		$this->kv['_main'] = $this->_parse();
	}
	
	function _skip_line() {
		while(true) {
			$c = $this->str[$this->index++];
			if ($c == "\n" || $this->index >= strlen($this->str)) break;
		}
	}
	
	function _skip_blank() {
		while(true) {
			if ($this->index >= strlen($this->str)) break;
			$c = $this->str[$this->index++];
			if ($c == ' ' || $c == "\r" || $c == "\t") {
			} else {
				$this->index--; //回退
				break;
			}
		}
	}
	
	function _read_word() {
		$word = '';
		while(true) {
			$c = $this->str[$this->index++];
			if ($c == '"') break;
			else $word .= $c;
		}
		return $word;
	}
	
	function _read_word_n() {
		$word = '';
		$this->index--; //回退一下
		while(true) {
			$c = $this->str[$this->index++];
			if ($this->_is_control($c)) {
				$this->index--; //回退一下
				break;
			}
			else $word .= $c;
		}
		return $word;
	}
	
	function _parse() {
		$kvs = array();
		
		$scope = 'key';
		$word = '';
		$key = '';
		$value = '';
		while (true) {
			$c = $this->str[$this->index++];
			if ($c == "\t") $c = ' ';
			if (!$this->_is_control($c)) {
				$word = $this->_read_word_n();
				echo "--- $word ---\n";
			}
			if ($c == '"') {
				$word = $this->_read_word();
				echo "---\" $word \"---\n";
			}
			if ($this->_is_control($c)) {
				if ($word != '') {
					echo "*** $word \n";
					if ($scope == 'key') {
						$kv = array();
						$kv[$word] = array();
						$kvs[] = $kv;
						$key = $word;
					} elseif ($scope == 'value') {
						$kvs[count($kvs)-1][$key][] = $word;
					}
					$word = '';
				}
				$this->_skip_blank();
			}
			if ($c == '=') {
				$scope = 'value';
			}
			if ($c == "\n") {
				$scope = 'key';
				$key = '';
			}
			if ($c == '#') {
				$this->_skip_line();
			}
			if ($c == '{') {
				$kvs[count($kvs)-1][$key][] = $this->_parse();
				$scope = 'key';
			}
			if ($c == '}') {
				break;
			}
			if ($this->index >= strlen($this->str)) break;
		}
		return $kvs;
	}
	
	function to_string() {
		$kvs = $this->kv['_main'];
		return $this->_to_string($kvs);
	}
	
	function _space($deep) {
		$s = '';
		for ($i = 0; $i < $deep; ++$i) {
			$s .= "\t";
		}
		return $s;
	}
	
	function _to_string($kvs, $deep = 0) {
		$str = '';
		$_i = 0;
		foreach ($kvs as $val) {
			foreach ($val as $k => $v) {
				if (count($v) > 0) {
					$str .= $this->_space($deep).$k;
					$str .= ' =';
				} else {
					echo "## $_i \n";
					if ($_i == 0) $str .= $this->_space($deep);
					else $str .= ' ';
					$str .= $k;
				}
				foreach ($v as $_v) {
					if (is_array($_v)) {
						$str .= " {\n";
						$str .= $this->_to_string($_v, $deep + 1);
						$str .= $this->_space($deep).'}';
					}else {
						if ($this->_is_n_str($_v)) {
							$str .= ' '.$_v;
						}else {
							$str .= ' "'.$_v.'"';
						}
					}
				}
				if (count($v) > 0 || $_i == count($kvs) - 1) $str .= "\n";
			}
			$_i++;
		}
		return $str;
	}
}


$p = new config_parser();
$p->set_config('test.txt');
$p->parse();
var_dump($p->kv);

echo $p->to_string();
