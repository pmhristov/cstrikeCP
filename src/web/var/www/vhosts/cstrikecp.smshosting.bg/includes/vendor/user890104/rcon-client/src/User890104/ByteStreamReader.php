<?php
namespace User890104;

use Exception;

class ByteStreamReader {
	private $data;
	private $length;
	private $offset = 0;
	
	public function __construct($data) {
		$this->data = $data;
		$this->length = strlen($data);
	}
	
	public function getLength() {
		return $this->length;
	}
	
	public function getOffset() {
		return $this->offset;
	}
	
	private function setOffset($pos) {
		$this->offset = $pos;
	}
	
	private function increaseOffset($count) {
		$this->setOffset($this->getOffset() + $count);
	}
	
	public function reset() {
		$this->setOffset(0);
	}
	
	public function readAllData() {
		return $this->data;
	}
	
	public function readRemainingData() {
		$data = substr($this->data, $this->offset);
		$this->setOffset($this->length);
		return $data;
	}
	
	private function read($format, $length) {
		if ($length > $this->length - $this->offset) {
			throw new Exception('Not enough data');
		}
		
		$data = unpack($format, substr($this->data, $this->offset, $length));
		
		if ($data === false) {
			throw new Exception('Failed to decode data');
		}
		
		$this->increaseOffset($length);
		return reset($data);
	}
	
	
	// 8 bit
	public function readChar() {
		return $this->read('c', 1);
	}
	
	public function readUnsignedChar() {
		return $this->read('C', 1);
	}
	
	// 16 bit
	public function readShort() {
		return $this->read('s', 2);
	}
	
	public function readUnsignedShort() {
		return $this->read('S', 2);
	}
	
	public function readUnsignedShortBE() {
		return $this->read('n', 2);
	}
	
	public function readUnsignedShortLE() {
		return $this->read('v', 2);
	}
	
	// 32 bit
	public function readInt() {
		return $this->read('l', 4);
	}
	
	public function readUnsignedInt() {
		return $this->read('L', 4);
	}
	
	public function readUnsignedIntBE() {
		return $this->read('N', 4);
	}
	
	public function readUnsignedIntLE() {
		return $this->read('V', 4);
	}
	
	// 64 bit
	public function readLong() {
		return $this->read('q', 8);
	}
	
	public function readUnsignedLong() {
		return $this->read('Q', 8);
	}
	
	public function readUnsignedLongBE() {
		return $this->read('J', 8);
	}
	
	public function readUnsignedLongLE() {
		return $this->read('P', 8);
	}
	
	// Float
	public function readFloat() {
		return $this->read('f', 4);
	}
	
	public function readDouble() {
		return $this->read('d', 8);
	}
	
	// Text
	public function readAsciiLetter() {
		$data = $this->data[$this->getOffset()];
		$this->increaseOffset(1);
		
		return $data;
	}
	
	public function readString() {
		$pos = strpos($this->data, chr(0), $this->offset);
		
		if ($pos === false) {
			throw new Exception('No end marker found');
		}
		
		$data = substr($this->data, $this->offset, $pos - $this->offset);
		$this->setOffset($pos + 1);
		
		return $data;
	}
}
