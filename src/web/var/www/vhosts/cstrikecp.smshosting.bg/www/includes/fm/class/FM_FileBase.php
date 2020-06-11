<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

/**
 * Base class for file / directory operations.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
abstract class FM_FileBase {

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * holds FileManager object
	 *
	 * @var FileManager
	 */
	protected $_FileManager;

	/**
	 * full path
	 *
	 * @var string
	 */
	protected $_path;

	/**
	 * holds FTP stream
	 *
	 * @var resource
	 */
	protected $_ftp;

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constructor
	 *
	 * @param FileManager $FileManager	reference to FileManager object
	 * @param string $path				file / directory path
	 * @param resource $ftp				optional: FTP stream
	 * @return FM_File
	 */
	public function __construct(FileManager $FileManager, $path, $ftp = null) {
		$this->_FileManager = $FileManager;
		$this->_path = $path;
		$this->_ftp = $ftp;
	}

	/**
	 * change permissions
	 *
	 * @param integer $mode		permissions
	 * @return boolean
	 */
	public function changePerms($mode) {
		return $this->_ftp
			? @ftp_chmod($this->_ftp, $mode, $this->_path)
			: @chmod($this->_path, $mode);
	}

	/**
	 * restore file / directory
	 *
	 * @return boolean
	 */
	public function restore() {
		if(preg_match('/\.' . FM_EXT_DELETED . '$/', $this->_path)) {
			$newName = preg_replace('/\.' . FM_EXT_DELETED . '$/', '', $this->_path);
			return $this->rename($newName);
		}
		return false;
	}

	/**
	 * rename file / directory
	 *
	 * @param string $dstPath	destination path
	 * @return boolean
	 */
	public function rename($dstPath) {
		return $this->_ftp
			? @ftp_rename($this->_ftp, $this->_path, $dstPath)
			: @rename($this->_path, $dstPath);
	}

	/**
	 * get last update timestamp
	 * NOTE: ftp_mdtm doesn't work with directories
	 *
	 * @return integer
	 */
	public function getLastUpdate() {
		if($this->_ftp) {
			$time = ftp_mdtm($this->_ftp, $this->_path);
			if($time <= 0) {
				/* looks like this is a directory... */
				$path = preg_replace('%/[^/]+$%', '', $this->_path);
				$Directory = new FM_Directory($this->_FileManager, $path, $this->_ftp);
				$list = $Directory->read();

				if(is_array($list)) foreach($list as $item) {
					if($item['path'] == $this->_path) {
						$time = @strtotime($item['changed']);
						return ($time > 0) ? $time : 0;
					}
				}
				else $time = 0;
			}
			return $time;
		}
		return @filemtime($this->_path);
	}
}

?>