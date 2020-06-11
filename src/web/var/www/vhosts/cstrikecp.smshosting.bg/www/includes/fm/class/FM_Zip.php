<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

/**
 * This class is used to create and send ZIP files. Based on zip.lib.php of phpMyAdmin.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_Zip {

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * character set (filename encoding)
	 *
	 * @var string
	 */
	protected $_encoding;

	/**
	 * central directory
	 *
	 * @var array
	 */
	protected $_cdir = array();

	/**
	 * current position in ZIP archive
	 *
	 * @var integer
	 */
	protected $_offset = 0;

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constructor
	 *
	 * @param string $encoding		character set (filename encoding)
	 */
	public function __construct($encoding = '') {
		if($encoding == '') $encoding = 'ISO-8859-1';
		$this->_encoding = $encoding;
	}

	/**
	 * start transfer to browser
	 *
	 * @param string $filename		name of the ZIP archive
	 */
	public function startTransfer($filename) {
		header('Content-Type: application/zip');
		header("Content-Disposition: attachment; filename=$filename");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Expires: 0');
		ob_end_flush();
	}

	/**
	 * add file data to ZIP archive
	 *
	 * @param string $data		file data
	 * @param string $name		filename
	 * @param integer $time		optional: timestamp of last file modification
	 */
	public function addFile($data, $name, $time = 0) {
		$name = $this->_convertName($name);
		$ftm = $this->_convertTime($time);
		$crc = pack('V', crc32($data));
		$uln = pack('V', strlen($data));
		$nln = pack('v', strlen($name));
		$att = pack('V', 32);
		$off = pack('V', $this->_offset);
		$nul = pack('v', 0);

		$cdata = gzcompress($data);
		$cdata = substr(substr($cdata, 0, strlen($cdata) - 4), 2);
		$clen = pack('V', strlen($cdata));
		unset($data);

		$file = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00";
		$file .= $ftm.$crc.$clen.$uln.$nln.$nul.$name.$cdata;
		$this->_offset += strlen($file);
		print $file;
		
		$dir = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00";
		$dir .= $ftm.$crc.$clen.$uln.$nln.$nul.$nul.$nul.$nul.$att.$off.$name;
		$this->_cdir[] = $dir;
	}


	/**
	 * finish transfer to browser
	 */
	public function finishTransfer() {
		$cnt = count($this->_cdir);
		$dir = implode('', $this->_cdir);
		print $dir . "\x50\x4b\x05\x06\x00\x00\x00\x00" .
			pack('v', $cnt) .
			pack('v', $cnt) .
			pack('V', strlen($dir)) .
			pack('V', $this->_offset) .
			"\x00\x00";
		exit;
	}

/* PROTECTED METHODS *************************************************************************** */

	/**
	 * convert filename to CP437
	 *
	 * @param string $name
	 * @return string
	 */
	protected function _convertName($name) {
		$newName = '';
		if(function_exists('iconv')) {
			$newName = iconv($this->_encoding, 'CP437', $name);
		}
		else if(function_exists('mb_convert_encoding')) {
			$newName = mb_convert_encoding($name, 'CP437', $this->_encoding);
		}
		return ($newName != '') ? $newName : $name;
	}

	/**
	 * convert timestamp
	 *
	 * @param integer $time		optional: UNIX timestamp
	 * @return string
	 */
	protected function _convertTime($time = 0) {
		if($time == 0) $time = time();
		else if($time < 315532800) $time = 315532800;
		$arr = getdate($time);
		$time =	dechex(
			(($arr['year'] - 1980) << 25) |
			($arr['mon'] << 21) |
			($arr['mday'] << 16) |
			($arr['hours'] << 11) |
			($arr['minutes'] << 5) |
			($arr['seconds'] >> 1)
		);
		$t = substr('00000000' . $time, -8);
		$hex = '\x'.$t[6].$t[7].'\x'.$t[4].$t[5].'\x'.$t[2].$t[3].'\x'.$t[0].$t[1];
		eval('$hex = "' . $hex . '";');
		return $hex;
	}
}

?>