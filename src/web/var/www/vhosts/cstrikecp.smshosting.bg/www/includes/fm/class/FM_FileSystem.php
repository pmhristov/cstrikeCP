<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

include_once('FM_File.php');
include_once('FM_FileMp3.php');
include_once('FM_Directory.php');
include_once('FM_Tools.php');

/**
 * This class contains file system methods.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_FileSystem {

	/**
	 * UNIX directory listing row
	 */
	const UNIX_ROW = '/^([drwxst\-]{10}) +\d+ +([^ ]+) +([^ ]+) +(\d+) +(\w{3} +\d+ +(\d{2,4} )?[\d\:]{4,5}) +(.+)$/i';

	/**
	 * Windows directory listing row
	 */
	const WINDOWS_ROW = '/^([\d\.\-]+) +([\d\:]{5}[PA]?M?) +(<DIR>|[\d\.]+) +(.+)$/i';

/* PUBLIC PROPERTIES *************************************************************************** */

	/**
	 * use UTF-8 for file/directory names
	 *
	 * @var boolean
	 */
	public $useUtf8;

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * holds FTP stream
	 *
	 * @var resource
	 */
	protected $_ftp;

	/**
	 * FTP server
	 *
	 * @var string
	 */
	protected $_host;

	/**
	 * FTP port number
	 *
	 * @var integer
	 */
	protected $_port;

	/**
	 * OS type
	 *
	 * @var string
	 */
	protected $_sysType;

	/**
	 * holds FileManager object
	 *
	 * @var FileManager
	 */
	protected $_FileManager;

	/**
	 * holds Log object
	 *
	 * @var FM_Log
	 */
	protected $_Log;

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constructor
	 *
	 * @param FileManager $FileManager
	 * @param boolean $useUtf8				optional
	 * @return FM_FileSystem
	 */
	public function __construct(FileManager $FileManager, $useUtf8 = false) {
		$this->_FileManager = $FileManager;
		$this->useUtf8 = $useUtf8;
		$this->_Log = $this->_FileManager->Log;
	}

	/**
	 * connect to FTP server
	 *
	 * @param string $host			server
	 * @param integer $port			optional: port number
	 * @param integer $timeout		optional: timeout in seconds
	 * @return boolean
	 */
	public function ftpConnect($host, $port = 21, $timeout = 30) {
		$this->_host = $host;
		$this->_port = $port;

		if($this->_FileManager->ftpSSL) {
			if(function_exists('ftp_ssl_connect')) {
				$this->_ftp = @ftp_ssl_connect($host, $port, $timeout);
			}
			else $this->_Log->add('No FTPS support on this server', 'error');
		}
		else $this->_ftp = @ftp_connect($host, $port, $timeout);

		if($this->_ftp) {
			@ftp_exec($this->_ftp, 'epsv4 off');
			$this->_Log->add("Connected to $host:$port");
			return true;
		}
		$this->_Log->add("Could not connect to $host:$port", 'error');
		return false;
	}

	/**
	 * close FTP connection
	 *
	 * @return boolean
	 */
	public function ftpClose() {
		if(!$this->_ftp) return false;
		if(@ftp_quit($this->_ftp)) {
			$this->_Log->add("Closed connection to $this->_host:$this->_port");
			$this->_ftp = null;
			return true;
		}
		$this->_Log->add("Could not close connection to $this->_host:$this->_port", 'error');
		return false;
	}

	/**
	 * FTP login
	 *
	 * @param string $user			user name
	 * @param string $password		password
	 * @return boolean
	 */
	public function ftpLogin($user, $password) {
		if(!$this->_ftp) return false;
		if(@ftp_login($this->_ftp, $user, $password)) {
			$this->_Log->add("User $user logged in");
			return true;
		}
		$this->_Log->add("User $user could not log in", 'error');
		return false;
	}

	/**
	 * switch to passive mode
	 *
	 * @param boolean $mode			true = passive, false = active
	 * @return boolean
	 */
	public function ftpPassiveMode($mode) {
		if(!$this->_ftp) return false;
		if(@ftp_pasv($this->_ftp, $mode)) {
			if($mode) $this->_Log->add('Switched to passive mode');
			else $this->_Log->add('Switched to active mode');
			return true;
		}
		$this->_Log->add('Could not switch passive mode', 'error');
		return false;
	}

	/**
	 * get OS type
	 *
	 * @return string
	 */
	public function getSystemType() {
		if($this->_sysType) return $this->_sysType;

		if($this->_checkFtp()) {
			$this->_sysType = @ftp_systype($this->_ftp);
		}
		else if(!$this->_FileManager->ftpHost) {
			$this->_sysType = function_exists('php_uname') ? php_uname() : PHP_OS;
		}

		if($this->_sysType) {
			if(!$this->_FileManager->hideSystemType) {
				$this->_Log->add("System type is $this->_sysType", 'info');
			}
			return $this->_sysType;
		}
		$this->_Log->add('Could not get system type', 'error');
		return false;
	}

	/**
	 * change directory
	 *
	 * @param string $path		directory path
	 * @return boolean
	 */
	public function changeDir($path) {
		if($this->_checkFtp()) $ok = @ftp_chdir($this->_ftp, $path);
		else $ok = @chdir($path);
		$path = $this->checkPath($path);

		if($ok) {
			$this->_Log->add("Changed directory to $path");
			return true;
		}
		$this->_Log->add("Could not change directory to $path", 'error');
		return false;
	}

	/**
	 * create directory
	 *
	 * @param string $dir		directory path
	 * @return boolean
	 */
	public function makeDir($dir) {
		$dir = $this->_checkEncoding($dir);

		if($this->_checkFtp()) {
			$ok = @ftp_mkdir($this->_ftp, $dir);
			/* workaround for PHP bug */
			if(!$ok) $ok = @ftp_nlist($this->_ftp, $dir);
		}
		else $ok = @mkdir($dir, 0755);
		$dir = $this->checkPath($dir);

		if($ok) {
			$this->_Log->add("Created directory $dir");
			return true;
		}
		$this->_Log->add("Could not create directory $dir", 'error');
		return false;
	}

	/**
	 * remove directory recursively
	 *
	 * @param string $path		directory path
	 * @param boolean $noLog	optional: don't generate log message
	 * @return boolean
	 */
	public function removeDir($path, $noLog = false) {
		$restore = ($this->_FileManager->enableRestore && !preg_match('/\.' . FM_EXT_DELETED . '$/', $path));
		$Directory = new FM_Directory($this->_FileManager, $path, $this->_checkFtp());
		$list = $Directory->read();

		if($list === false) {
			/* is directory in recycle bin? */
			$Directory = new FM_Directory($this->_FileManager, $path . '.' . FM_EXT_DELETED, $this->_checkFtp());
			$list = $Directory->read();
		}

		if(is_array($list)) foreach($list as $item) {
			if($item['isDir']) $this->removeDir($item['path'], true);
			else $this->deleteFile($item['path'], true);
		}
		$ok = $Directory->delete($restore);
		$path = $this->checkPath($path);

		if($restore) {
			if($ok) {
				if(!$noLog) $this->_Log->add("Moved directory $path to recycle bin");
				return true;
			}
			$this->_Log->add("Could not move directory $path to recycle bin", 'error');
			return false;
		}
		else {
			if($ok) {
				if(!$noLog) $this->_Log->add("Removed directory $path");
				return true;
			}
			$this->_Log->add("Could not remove directory $path", 'error');
			return false;
		}
	}

	/**
	 * read directory
	 *
	 * @param string $path		directory path
	 * @param boolean $noLog	optional: don't generate log message
	 * @return mixed			entries or false
	 */
	public function readDir($path, $noLog = false) {
		$Directory = new FM_Directory($this->_FileManager, $path, $this->_checkFtp());
		$list = $Directory->read();
		$path = $this->checkPath($path);
		if(!$path) $path = '/';

		if(is_array($list)) {
			if(!$noLog) $this->_Log->add("Read directory $path");
			return $list;
		}
		$this->_Log->add("Could not read directory $path", 'error');
		return false;
	}

	/**
	 * change permissions
	 *
	 * @param string $path		file / directory name
	 * @param integer $mode		permissions
	 * @return boolean
	 */
	public function changePerms($path, $mode) {
		$File = new FM_File($this->_FileManager, $path, $this->_checkFtp());
		$ok = $File->changePerms($mode);
		$path = $this->checkPath($path);

		if($ok) {
			$this->_Log->add(sprintf('Changed permissions of %s to %o', $path, $mode));
			return true;
		}
		$this->_Log->add(sprintf('Could not change permissions of %s to %o', $path, $mode), 'error');
		return false;
	}

	/**
	 * get file from FTP server if necessary
	 *
	 * @param string $src			remote or local file path
	 * @param boolean $useFtpNb		optional: use FTP non-blocking mode
	 * @return mixed				local file path or array
	 */
	public function getFile($src, $useFtpNb = false) {
		if($this->_checkFtp()) {
			$dstDir = $this->_FileManager->getContCacheDir();
			$ext = strtolower(FM_Tools::getSuffix($src, '.'));
			$dst = $dstDir . '/' . md5($this->_FileManager->ftpUser . $src) . '.' . $ext;

			if($this->_FileManager->useFileCache && is_file($dst)) {
				return $dst;
			}
			else if($useFtpNb && function_exists('ftp_nb_get')) {
				$status = @ftp_nb_get($this->_ftp, $dst, $src, FTP_BINARY);
				if($status != FTP_FAILED) {
					$this->_Log->add('Got file ' . $this->checkPath($src));
					return array($dst, $status, $this->_ftp);
				}
			}
			else if(@ftp_get($this->_ftp, $dst, $src, FTP_BINARY)) {
				$this->_Log->add('Got file ' . $this->checkPath($src));
				return $dst;
			}
		}
		else if(is_file($src)) {
			return $src;
		}
		$src = $this->checkPath($src);
		$this->_Log->add("Could not get file $src", 'error');
		return false;
	}

	/**
	 * upload file
	 *
	 * @param string $src		source path (local / temp)
	 * @param string $dst		destination path (remote / target dir)
	 * @return boolean
	 */
	public function putFile($src, $dst) {
		$dst = $this->_checkEncoding($dst);
		$File = new FM_File($this->_FileManager, $src, $this->_checkFtp());
		$ok = $File->put($dst);
		$dst = $this->checkPath($dst);

		if($ok) {
			$this->_Log->add("Saved file $dst");
			return true;
		}
		$this->_Log->add("Could not save file $dst", 'error');
		return false;
	}

	/**
	 * delete file
	 *
	 * @param string $path		file path
	 * @param boolean $noLog	optional: don't generate log message
	 * @return boolean
	 */
	public function deleteFile($path, $noLog = false) {
		$restore = ($this->_FileManager->enableRestore && !preg_match('/\.' . FM_EXT_DELETED . '$/', $path));
		$File = new FM_File($this->_FileManager, $path, $this->_checkFtp());
		$ok = $File->delete($restore);
		$path = $this->checkPath($path);

		if($restore) {
			if($ok) {
				if(!$noLog) $this->_Log->add("Moved file $path to recycle bin");
				return true;
			}
			$this->_Log->add("Could not move file $path to recycle bin", 'error');
			return false;
		}
		else {
			if($ok) {
				if(!$noLog) $this->_Log->add("Deleted file $path");
				return true;
			}
			$this->_Log->add("Could not delete file $path", 'error');
			return false;
		}
	}

	/**
	 * restore file
	 *
	 * @param string $path		file path
	 * @param boolean $noLog	optional: don't generate log message
	 * @return boolean
	 */
	public function restoreFile($path, $noLog = false) {
		$File = new FM_File($this->_FileManager, $path, $this->_checkFtp());
		$ok = $File->restore();
		$path = $this->checkPath($path);

		if($ok) {
			if(!$noLog) $this->_Log->add("Restored file $path");
			return true;
		}
		$this->_Log->add("Could not restore file $path", 'error');
		return false;
	}

	/**
	 * restore directory
	 *
	 * @param string $path		directory path
	 * @param boolean $noLog	optional: don't generate log message
	 * @return boolean
	 */
	public function restoreDir($path, $noLog = false) {
		$Directory = new FM_Directory($this->_FileManager, $path, $this->_checkFtp());
		$list = $Directory->read();

		if(is_array($list)) foreach($list as $item) {
			if($item['isDir']) $this->restoreDir($item['path'], true);
			else $this->restoreFile($item['path'], true);
		}
		$ok = $Directory->restore();
		$path = $this->checkPath($path);

		if($ok) {
			if(!$noLog) $this->_Log->add("Restored directory $path");
			return true;
		}
		$this->_Log->add("Could not restore directory $path", 'error');
		return false;
	}

	/**
	 * write file data
	 *
	 * @param string $path		file path
	 * @param string $data		file data
	 * @return boolean
	 */
	public function writeFile($path, &$data) {
		if($data == '') return false;
		if(get_magic_quotes_gpc()) $data = stripslashes($data);
		$ok = false;

		if($this->_checkFtp()) {
			$srcPath = $this->_FileManager->getUserTmpDir() . '/' . FM_Tools::basename($path);
			$ok = FM_Tools::saveLocalFile($srcPath, $data);

			if($ok && is_file($srcPath)) {
				return $this->putFile($srcPath, $path);
			}
		}
		else $ok = FM_Tools::saveLocalFile($path, $data);
		$path = $this->checkPath($path);

		if($ok) {
			$this->_Log->add("Saved file $path");
			return true;
		}
		$this->_Log->add("Could not save file $path", 'error');
		return false;
	}

	/**
	 * copy file
	 *
	 * @param string $src		source path
	 * @param string $dst		destination path
	 * @return boolean
	 */
	public function copyFile($src, $dst) {
		$File = new FM_File($this->_FileManager, $src, $this->_checkFtp());
		$ok = $File->copy($dst);
		$src = $this->checkPath($src);
		$dst = $this->checkPath($dst);

		if($ok) {
			$this->_Log->add("Copied $src => $dst");
			return true;
		}
		$this->_Log->add("Could not copy $src => $dst", 'error');
		return false;
	}

	/**
	 * rename file / directory
	 *
	 * @param string $src		source path
	 * @param string $dst		destination path
	 * @return boolean
	 */
	public function rename($src, $dst) {
		$dst = $this->_checkEncoding($dst);
		$File = new FM_File($this->_FileManager, $src, $this->_checkFtp());
		$ok = $File->rename($dst);
		$src = $this->checkPath($src);
		$dst = $this->checkPath($dst);

		if($ok) {
			$this->_Log->add("Renamed $src => $dst");
			return true;
		}
		$this->_Log->add("Could not rename $src => $dst", 'error');
		return false;
	}

	/**
	 * get last update timestamp
	 *
	 * @param string $path		file / directory path
	 * @return integer			UNIX timestamp
	 */
	public function getLastUpdate($path) {
		$File = new FM_File($this->_FileManager, $path, $this->_checkFtp());
		return $File->getLastUpdate();
	}

	/**
	 * read data from URL and save it to local file
	 *
	 * @param string $url		source URL
	 * @param string $dstDir	destination directory
	 * @return boolean
	 */
	public function saveFromUrl($url, $dstDir) {
		$dstDir = $this->_checkEncoding($dstDir);
		$errno = $errstr = '';
		if(!strstr($url, '://')) $url = 'http://' . $url;
		$urlParts = @parse_url($url);
		$path = $urlParts['path'] . ($urlParts['query'] ? '?' . $urlParts['query'] : '');
		$path = str_replace(' ', '%20', $path);
		$port = $urlParts['port'] ? $urlParts['port'] : 80;
		$filename = FM_Tools::getSuffix($urlParts['path'], '/');
		$dstPath = $dstDir . '/' . $filename;
		$ok = false;

		if($sp = @fsockopen($urlParts['host'], $port, $errno, $errstr, 15)) {
			fputs($sp, "GET $path HTTP/1.1\r\n");
			fputs($sp, "Host: {$urlParts['host']}\r\n");
			fputs($sp, "Connection: close\r\n\r\n");

			if($fp = @fopen($dstPath, 'wb')) {
				$buffer = '';
				$headerFound = false;
				$ok = true;

				while(!@feof($sp)) {
					$buffer .= @fread($sp, 4096);
					if(preg_match('/^HTTP\/[\d\.]+ (\d+) ([\w ]+)/i', $buffer, $m)) {
						if($m[1] != 200) {
							$errstr = "Host returned \"$m[1] $m[2]\"";
							$ok = false;
							break;
						}
					}

					if($headerFound) {
						@fwrite($fp, $buffer);
						$buffer = '';
					}
					else {
						$headerEnd = strpos($buffer, "\r\n\r\n");
						if($headerEnd !== false) {
							$headerFound = true;
							@fwrite($fp, substr($buffer, $headerEnd + 4));
							$buffer = '';
						}
					}
				}
				@fclose($fp);
				if($ok) $this->_Log->add("Got file $filename");
			}
			else $this->_Log->add("Could not open file $filename", 'error');
			@fclose($sp);
		}
		if($errstr) $this->_Log->add('Could not read data: ' . trim($errstr), 'error');
		return $ok;
	}

	/**
	 * read ID3 tags from MP3 file
	 *
	 * @param string $path		file path
	 * @return array
	 */
	public function readId3Tags($path) {
		if($this->_FileManager->enableId3Tags) {
			if(preg_match('/\.mp3$/i', $path)) {
				if($path = $this->getFile($path)) {
					$File = new FM_FileMp3($this->_FileManager, $path, $this->_checkFtp());
					return $File->readId3Tags();
				}
			}
		}
		return false;
	}

	/**
	 * remove start directory from file path
	 * remove additional extension from deleted files
	 *
	 * @param string $path		file path
	 * @return string
	 */
	public function checkPath($path) {
		$path = FM_Tools::substr($path, FM_Tools::strlen($this->_FileManager->rootDir), FM_Tools::strlen($path));
		if($this->_FileManager->enableRestore) $path = preg_replace('/\.' . FM_EXT_DELETED . '$/', '', $path);
		return $path;
	}

/* PROTECTED METHODS *************************************************************************** */

	/**
	 * check if FTP connection exists; create new one if necessary
	 *
	 * @return mixed
	 */
	protected function _checkFtp() {
		if($this->_FileManager->ftpHost && !$this->_ftp) {
			if($this->ftpConnect($this->_FileManager->ftpHost, $this->_FileManager->ftpPort)) {
				if($this->ftpLogin($this->_FileManager->ftpUser, $this->_FileManager->ftpPassword)) {
					$this->ftpPassiveMode($this->_FileManager->ftpPassiveMode);
				}
			}
		}
		return $this->_ftp;
	}

	/**
	 * encode/decode file path, if necessary
	 *
	 * @param string $path		file path
	 * @return string			encoded/decoded path
	 */
	protected function _checkEncoding($path) {
		if($this->useUtf8) {
			return FM_Tools::utf8Encode($path, $this->_FileManager->encoding);
		}
		return FM_Tools::utf8Decode($path, $this->_FileManager->encoding);
	}
}

?>