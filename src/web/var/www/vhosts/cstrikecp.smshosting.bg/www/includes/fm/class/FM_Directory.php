<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

 include_once('FM_FileBase.php');
 include_once('FM_Tools.php');
 
/**
 * This class handles directory operations on a single directory.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_Directory extends FM_FileBase {

	/**
	 * UNIX directory listing row
	 */
	const UNIX_ROW = '/^([drwxst\-]{10}) +\d+ +([^ ]+) +([^ ]+) +(\d+) +(\w{3} +\d+ +(\d{2,4} )?[\d\:]{4,5}) +(.+)$/i';

	/**
	 * Windows directory listing row
	 */
	const WINDOWS_ROW = '/^([\d\.\-]+) +([\d\:]{5}[PA]?M?) +(<DIR>|[\d\.]+) +(.+)$/i';

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * delete directory
	 *
	 * @param boolean $restore		optional: restore enabled?
	 * @return boolean
	 */
	public function delete($restore = false) {
		if($restore) {
			if(preg_match('/\.' . FM_EXT_DELETED . '$/', $this->_path)) return true;
			return $this->_ftp
				? @ftp_rename($this->_ftp, $this->_path, $this->_path . '.' . FM_EXT_DELETED)
				: @rename($this->_path, $this->_path . '.' . FM_EXT_DELETED);
		}
		else return $this->_ftp
			? @ftp_rmdir($this->_ftp, $this->_path)
			: @rmdir($this->_path);
	}

	/**
	 * read directory
	 *
	 * @return mixed	directory entries or false on failure
	 */
	public function read() {
		if($this->_ftp) {
			if($this->_path == '/' || $this->_path == '.') {
				$list = @ftp_rawlist($this->_ftp, $this->_path);
			}
			else if(($wd = @ftp_pwd($this->_ftp)) !== false) {
				if(@ftp_chdir($this->_ftp, $this->_path)) {
					$list = @ftp_rawlist($this->_ftp, '.');
					@ftp_chdir($this->_ftp, $wd);
				}
				else return false;
			}
			else return false;
		}
		else if($dp = @opendir($this->_path)) {
			$list = array();
			while(($file = @readdir($dp)) !== false) {
				$list[] = $this->_path . '/' . $file;
			}
			@closedir($dp);
		}
		else return false;

		if(is_array($list)) {
			$newList = array();

			foreach($list as $file) {
				$item = array();

				if($this->_ftp) {
					if(preg_match(FM_FileSystem::UNIX_ROW, $file, $m)) {
						if($m[7] == '..' || $m[7] == '.') continue;
						$changed = $m[6] ? $m[5] : (strstr($m[5], ':') ? preg_replace('/([\d\:]{4,5})$/', @date('Y') . ' $1', $m[5]) : $m[5]);
						$tstamp = @strtotime($changed);
						$item['isDir'] = ($m[1][0] == 'd');
						$item['permissions'] = $m[1];
						$item['owner'] = $m[2];
						$item['group'] = $m[3];
						$item['size'] = $m[4];
						$item['changed'] = ($tstamp > 0) ? @date('Y-m-d H:i:s', $tstamp) : $m[5];
						$item['name'] = $m[7];
						$item['target'] = '';
					}
					else if(preg_match(FM_FileSystem::WINDOWS_ROW, $file, $m)) {
						if($m[4] == '..' || $m[4] == '.') continue;
						$t = explode(':', $m[2]);
						if(preg_match('/[AP]M$/', strtoupper($t[1]), $m2)) {
							$t[1] = (int) $t[1];
							if($m2[0] == 'PM') $t[0] += 12;
						}
						if(strstr($m[1], '-')) {
							$d = explode('-', $m[1]);
							$tstamp = mktime($t[0], $t[1], 0, $d[0], $d[1], $d[2]);
						}
						else if(strstr($m[1], '.')) {
							$d = explode('.', $m[1]);
							$tstamp = mktime($t[0], $t[1], 0, $d[1], $d[0], $d[2]);
						}
						else {
							$tstamp = @strtotime($m[1] . ' ' . $m[2]);
						}
						$item['isDir'] = (strtoupper($m[3]) == '<DIR>');
						$item['changed'] = ($tstamp > 0) ? @date('Y-m-d H:i:s', $tstamp) : $m[1] . ' ' . $m[2];
						$item['permissions'] = '';
						$item['owner'] = '';
						$item['group'] = '';
						$item['size'] = $item['isDir'] ? 0 : str_replace('.', '', $m[3]);
						$item['name'] = $m[4];
						$item['target'] = '';
					}
				}
				else {
					$filename = FM_Tools::basename($file);
					if($filename == '..' || $filename == '.') continue;
					$item['isDir'] = is_dir($file);
					$item['owner'] = @fileowner($file);
					$item['group'] = @filegroup($file);
					$item['size'] = @filesize($file);
					$item['changed'] = @date('Y-m-d H:i:s', @filemtime($file));
					$item['name'] = $filename;
					$item['permissions'] = FM_Tools::getPermissions($this->_path . '/' . $item['name']);
					$item['target'] = is_link($file) ? @realpath($file) : '';
				}
				$item['path'] = $this->_path . '/' . $item['name'];
				$item['isDeleted'] = preg_match('/\.' . FM_EXT_DELETED . '$/', $item['name']);
				$newList[] = $item;
			}
			return $newList;
		}
		return false;
	}
}

?>