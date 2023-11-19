<?php
class config_parser {
	public $kv = array();
	private $control_c = array(' ', "\t", "\n", "\r", '#', '{', '}', '"', '=');
	private $config;
	private $str;
	private $index = 0;
	
	function _is_control($c) {
		foreach($this->control_c as $v) {
			if ($v == $c) return true;
		}
		return false;
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
			$c = $this->str[$this->index++];
			if ($c == ' ' || $c == "\r") {
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
			if ($c == ' ' || $c == "\r" || $c == "\n") {
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
			if (!$this->_is_control($c)) {
				$word = $this->_read_word_n();
			}
			if ($c == '"') {
				$word = $this->_read_word();
			}
			if ($c == '=') {
				$scope = 'value';
			}
			if ($c == ' ' || $c == "\n") {
				if ($word) {
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
			if ($c == "\n") {
				$scope = 'key';
				$key = '';
			}
			if ($c == '#') {
				$this->_skip_line();
			}
			if ($c == '{') {
				$kvs[count($kvs)-1][$key][] = $this->_parse();
				$scope = 'sub';
			}
			if ($c == '}') {
				if ($scope == 'sub') {
					$scope = 'key';
				}else {
					break;
				}
			}
			if ($this->index >= strlen($this->str)) break;
		}
		return $kvs;
	}
}


$p = new config_parser();
$p->set_config('test.txt');
$p->parse();
var_dump($p->kv);
