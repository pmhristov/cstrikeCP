<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

include_once('FM_Tools.php');

/**
 * This class handles log messages.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_Log {

/* PUBLIC PROPERTIES *************************************************************************** */

	/**
	 * remote IP address
	 *
	 * @var string
	 */
	public $remoteIp;

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * character set
	 *
	 * @var string
	 */
	protected $_encoding;

	/**
	 * log messages
	 *
	 * @var array
	 */
	protected $_messages;

	/**
	 * log file prefix
	 *
	 * @var string
	 */
	protected $_prefix;

	/**
	 * path to log file directory
	 *
	 * @var string
	 */
	protected $_logDir;

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constructor
	 *
	 * @param string $encoding	character set
	 * @param string $logDir	optional: path to log file directory
	 * @param string $prefix	optional: log file prefix
	 * @return FM_Log
	 */
	public function __construct($encoding, $logDir = '', $prefix = '') {
		$this->_encoding = $encoding;
		$this->_logDir = $logDir;
		$this->_prefix = ($prefix != '') ? $prefix . '_' : '';
		$this->_messages = array();
		$this->remoteIp = $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : 'n/a';
		if($logDir != '' && !is_dir($this->_logDir)) FM_Tools::makeDir($this->_logDir);
	}

	/**
	 * get log messages
	 *
	 * @return string
	 */
	public function get() {
		$log = implode(',', $this->_messages);
		$this->_messages = array();
		return $log;
	}

	/**
	 * add log message
	 *
	 * @param string $text		message text
	 * @param string $type		optional: message type
	 */
	public function add($text, $type = '') {
		switch(strtolower($type)) {
			case 'info': break;
			case 'error': break;
			default: $type = 'default';
		}
		$time = @date('Y-m-d H:i:s');
		$ip = $this->remoteIp;
		$text = FM_Tools::utf8Encode($text, $this->_encoding);
		$text = addslashes($text);
		$this->_messages[] = "{type:'$type',time:'$time',text:'$text'}";

		if($this->_logDir != '' && $type != 'info') {
			$file = $this->_logDir . '/' . $this->_prefix . @date('Y-m-d') . '.log';
			$line = sprintf("%s  %s  %s\n", $time, $ip, $text);
			$ok = FM_Tools::saveLocalFile($file, $line, true);
			if(!$ok) $this->_messages[] = "{type:'error',time:'$time',ip:'$ip',text:'Could not write to logfile'}";
		}
	}
}

?>