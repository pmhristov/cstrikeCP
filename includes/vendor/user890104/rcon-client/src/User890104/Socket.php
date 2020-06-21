<?php
namespace User890104;

use Exception;

class Socket {
	private $fd = null;
	private $readTimeout = [1, null];
	private $writeTimeout = [1, null];
	
	public function __construct($host, $port, $connectTimeout = 1) {
		$fd = fsockopen($host, $port, $errno, $errstr, $connectTimeout);
		
		if ($fd === false || $errno !== 0) {
			throw new Exception('Socket error: ' . $errstr . ' (' . $errno . ')');
		}
		
		stream_set_blocking($fd, false);
		
		$this->fd = $fd;
	}

	public function __destruct() {
		if (is_null($this->fd)) {
			return;
		}
		
		fclose($this->fd);
		$this->fd = null;
	}
	
	public function setReadTimeout($sec, $usec = null) {
		$this->readTimeout = [$sec, $usec];
	}
	
	public function setWriteTimeout($sec, $usec = null) {
		$this->writeTimeout = [$sec, $usec];
	}
	
	private function select($isWrite, $tv_sec, $tv_usec) {
		$read = $isWrite ? null : array($this->fd);
		$write = $isWrite ? array($this->fd) : null;
		$except = null;
		$select = stream_select($read, $write, $except, $tv_sec, $tv_usec);

		if ($select === false) {
			throw new Exception(($isWrite ? 'Write' : 'Read') . ' select failed');
		}
		
		return $select;
	}
	
	public function send($cmd) {
		$select = $this->select(true, $this->writeTimeout[0], $this->writeTimeout[1]);
		
		if ($select === 0) {
			throw new Exception('Write timeout');
		}

		if (fwrite($this->fd, $cmd) === false) {
			throw new Exception('Send failed');
		}
	}
	
	public function receive($packetLen = 1500) {
		$select = $this->select(false, $this->readTimeout[0], $this->readTimeout[1]);
		
		if ($select === 0) {
			return false;
		}

		$data = fread($this->fd, $packetLen);
		
		if ($data === false) {
			throw new Exception('Receive failed');
		}
		
		return $data;
	}
}
