<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

include_once('FM_ExpFolder.php');
include_once('FM_Tools.php');
include_once('FM_FileSystem.php');

/**
 * This class creates a directory explorer.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_Explorer {

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * stores folder information
	 *
	 * @var FM_ExpFolder[]
	 */
	protected $_folders;

	/**
	 * expand all folders
	 *
	 * @var boolean
	 */
	protected $_expandAll;

	/**
	 * cache file path
	 *
	 * @var string
	 */
	protected $_cacheFile;

	/**
	 * timestamp of last update
	 *
	 * @var integer
	 */
	protected $_lastCacheUpdate;

	/**
	 * refresh explorer
	 *
	 * @var mixed	folder ID or -1
	 */
	protected $_doRefresh;

	/**
	 * holds FileManager object
	 *
	 * @var FileManager
	 */
	protected $_FileManager;

	/**
	 * holds listing object
	 *
	 * @var FM_Listing
	 */
	protected $_Listing;

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constructor
	 *
	 * @param FM_Listing $Listing
	 * @return FM_Explorer
	 */
	public function __construct(FM_Listing $Listing) {
		$this->_Listing = $Listing;
		$this->_FileManager = $this->_Listing->FileManager;
		$this->_expandAll = $this->_FileManager->explorerExpandAll;
		$this->_doRefresh = -1;
		$this->setCacheFile();
	}

	/**
	 * make directory explorer
	 *
	 * @return string
	 */
	public function make() {
		if($this->_doRefresh >= 0) $this->_reload();
		$explorer = $this->_makeHeader();
		$explorer .= $this->_makeContent();
		$explorer .= $this->_makeFooter();
		return $explorer;
	}

	/**
	 * get all folders
	 *
	 * @return array
	 */
	public function getFolders() {
		if(!$this->_folders) {
			$this->_folders = $this->_readCache();
			$this->_lastCacheUpdate = time();
			if(!$this->_folders) $this->_reload();
		}
		return $this->_folders;
	}

	/**
	 * get folder by ID
	 *
	 * @param integer $id	folder ID
	 * @return mixed		FM_ExpFolder or false
	 */
	public function getFolderById($id) {
		if(is_array($this->getFolders())) {
			if(isset($this->_folders[$id])) {
				return $this->_folders[$id];
			}
		}
		return false;
	}

	/**
	 * get folder by path
	 *
	 * @param string $dir	directory path
	 * @return mixed		FM_ExpFolder or false
	 */
	public function getFolderByPath($dir) {
		if(is_array($this->getFolders())) {
			foreach($this->_folders as $Folder) {
				if($Folder->path == $dir) return $Folder;
			}
		}
		return false;
	}

	/**
	 * check if cache or folder has been updated
	 *
	 * @param string $dir	directory path
	 * @return mixed		folder ID or -1
	 */
	public function checkUpdate($dir) {
		if($this->_doRefresh != -1) return -1;
		if($this->_checkCacheUpdate()) return 0;
		$Folder = $this->_checkFolderUpdate($dir);

		if($Folder instanceof FM_ExpFolder) {
			$id = array_search($Folder, $this->_folders);
			if($id !== false) {
				$response = $id;
				$this->_doRefresh = $id;
			}
		}
		return $this->_doRefresh;
	}

	/**
	 * set cache file path
	 */
	public function setCacheFile() {
		$FM = $this->_FileManager;
		$path = $FM->tmpDir . '/' . md5('explorer:utf8:' . $FM->ftpHost . ':' . $FM->rootDir);
		$this->_cacheFile = $path;
	}

	/**
	 * refresh directory tree
	 */
	public function refresh() {
		$this->_doRefresh = 0;
	}

/* PROTECTED METHODS *************************************************************************** */

	/**
	 * make header
	 *
	 * @return string
	 */
	protected function _makeHeader() {
		return '{expandAll:' . (int) $this->_expandAll . ',items:[';
	}

	/**
	 * make content
	 *
	 * @return string
	 */
	protected function _makeContent() {
		if(is_array($this->getFolders())) {
			$items = array();

			foreach($this->_folders as $id => $Folder) {
				if(!$this->_Listing->isAllowedDir($Folder->path)) continue;
				$name = FM_Tools::basename($Folder->path);
				$name = ($name == '') ? '/' : FM_Tools::utf8Encode($name, $this->_FileManager->encoding);
				$path = $this->_Listing->FileSystem->checkPath($Folder->path);
				$path = ($path == '') ? '/' : FM_Tools::utf8Encode($path, $this->_FileManager->encoding);
				$item = "{id:$id,";
				$item .= 'level:' . (int) $Folder->level . ',';
				$item .= "name:'" . addslashes($name) . "',";
				$item .= "path:'" . addslashes($path) . "',";
				$item .= "hash:'" . md5($Folder->path) . "',";
				$item .= 'files:' . (int) $Folder->files . '}';
				$items[] = $item;
			}
			return implode(',', $items);
		}
	}

	/**
	 * make footer
	 *
	 * @return string
	 */
	protected function _makeFooter() {
		return ']}';
	}

	/**
	 * reload folders and update cache
	 */
	protected function _reload() {
		$this->_folders = $this->_readFolders($this->_FileManager->rootDir);
		$this->_sortFolders();
		$this->_writeCache();
		$this->_doRefresh = -1;
	}

	/**
	 * check if folder has been updated
	 *
	 * @param string $dir	directory path
	 * @return mixed		FM_ExpFolder or false
	 */
	public function _checkFolderUpdate($dir) {
		$Folder = $this->getFolderByPath($dir);
		if($Folder instanceof FM_ExpFolder) {
			$lastUpdate = $this->_Listing->FileSystem->getLastUpdate($this->_Listing->curDir);
			if($lastUpdate > $Folder->time) {
				return $Folder;
			}
		}
		return false;
	}

	/**
	 * check if cache has been updated
	 *
	 * @return boolean
	 */
	public function _checkCacheUpdate() {
		if(@filemtime($this->_cacheFile) > $this->_lastCacheUpdate) {
			$folders = $this->_readCache();
			if(is_array($folders)) {
				if(count($folders) != count($this->_folders)) {
					$this->_folders = $folders;
					$this->_lastCacheUpdate = time();
					return true;
				}
				foreach($folders as $Folder) {
					$CurFolder = $this->getFolderByPath($Folder->path);
					if(!$CurFolder || $CurFolder->level != $Folder->level || $CurFolder->files != $Folder->files) {
						$this->_folders = $folders;
						$this->_lastCacheUpdate = time();
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * read folder and all sub-folders
	 *
	 * @param string $dir		directory path
	 * @param integer $level	optional: directory level
	 * @return FM_ExpFolders[]
	 */
	protected function _readFolders($dir, $level = 1) {
		list($items, $cntFiles) = $this->_readFolder($dir);
		$dirs = array(new FM_ExpFolder($level, $dir, $cntFiles));

		if(is_array($items)) foreach($items as $item) {
			if($item['isDir'] && !$item['isDeleted'] && $item['path'] != '') {
				$dirs = array_merge($dirs, $this->_readFolders($item['path'], $level + 1));
			}
		}
		return $dirs;
	}

	/**
	 * read single folder
	 *
	 * @param string $dir		directory path
	 * @return array			directory entries, number of files
	 */
	protected function _readFolder($dir) {
		$items = $this->_Listing->FileSystem->readDir($dir, true);
		$cntFiles = 0;

		if(is_array($items)) foreach($items as $item) {
			if(!$item['isDir'] && !$item['isDeleted']) $cntFiles++;
		}
		return array($items, $cntFiles);
	}

	/**
	 * sort folders alphabetically
	 */
	protected function _sortFolders() {
		$paths = array();
		foreach($this->_folders as $id => $Folder) {
			$paths[$id] = FM_Tools::strtolower($Folder->path);
		}
		array_multisort($paths, SORT_ASC, SORT_REGULAR, $this->_folders);
	}

	/**
	 * read cache
	 *
	 * @return mixed	FM_ExpFolder[] or false
	 */
	protected function _readCache() {
		$str = FM_Tools::readLocalFile($this->_cacheFile);
		if($str) {
			$folders = FM_Tools::utf8Decode(unserialize($str), $this->_FileManager->encoding);
			return $folders;
		}
		return false;
	}

	/**
	 * write cache
	 */
	protected function _writeCache() {
		if($this->_cacheFile != '') {
			FM_Tools::saveLocalFile(
				$this->_cacheFile,
				FM_Tools::utf8Encode(serialize($this->_folders), $this->_FileManager->encoding)
			);
			$this->_lastCacheUpdate = time();
		}
	}
}

?>