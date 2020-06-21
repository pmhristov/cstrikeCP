<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

include_once('FM_Entry.php');
include_once('FM_FileSystem.php');
include_once('FM_Tools.php');
include_once('FM_Image.php');
include_once('FM_Explorer.php');

/**
 * This class manages directory listings.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_Listing {

/* PUBLIC PROPERTIES *************************************************************************** */

	/**
	 * current directory path
	 *
	 * @var string
	 */
	public $curDir;

	/**
	 * holds current search string
	 *
	 * @var string
	 */
	public $searchString;

	/**
	 * holds OS type
	 *
	 * @var string
	 */
	public $sysType;

	/**
	 * holds FileSystem object
	 *
	 * @var FM_FileSystem
	 */
	public $FileSystem;

	/**
	 * holds FileManager object
	 *
	 * @var FileManager
	 */
	public $FileManager;

	/**
	 * holds Explorer object
	 *
	 * @var FM_Explorer
	 */
	public $Explorer;

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * holds current listing
	 *
	 * @var FM_Entry[]
	 */
	protected $_entries;

	/**
	 * current folder index
	 *
	 * @var string
	 */
	protected $_folderId;

	/**
	 * folder ID or -1
	 *
	 * @var mixed
	 */
	protected $_reloadExplorer;

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constructor
	 *
	 * @param FileManager $FileManager		file manager object
	 * @param string $dir					optional: directory path
	 * @return FM_Listing
	 */
	public function __construct(FileManager $FileManager, $dir = '') {
		$this->FileManager = $FileManager;
		$this->FileSystem = new FM_FileSystem($FileManager, ($FileManager->encoding == 'UTF-8'));
		$this->Explorer = new FM_Explorer($this);
		$this->curDir = ($dir != '') ? $dir : $this->FileManager->rootDir;
		$this->_reloadExplorer = -1;
	}

	/**
	 * view current listing
	 *
	 * @return boolean
	 */
	public function view() {
		if($this->searchString != '') {
			$this->_folderId = 'search';
			/* do not cache search results! */
			unset($this->_entries[$this->_folderId]);
		}
		else $this->_folderId = md5($this->curDir);

		if(!isset($this->_entries[$this->_folderId])) {
			$ok = $this->readDir($this->curDir);
		}
		else $ok = true;

		$this->_viewHeader();
		print $this->_makeCaptions() . ',';

		$rootDir = $this->FileManager->rootDir;
		$subdir = (
			strlen($this->curDir) > strlen($rootDir) &&
			strncmp($this->curDir, $rootDir, strlen($rootDir)) == 0
		);
		$items = array();

		if($subdir || ($this->searchString != '' && $this->FileManager->enableSearch)) {
			$items[] = $this->_viewDirUp();
		}

		if(is_array($this->_entries[$this->_folderId])) {
			foreach($this->_entries[$this->_folderId] as $Entry) {
				$items[] = $Entry->view();
			}
		}
		print 'items:[' . implode(',', $items) . ']';

		$this->_viewFooter();
		$this->_reloadExplorer = -1;
		return $ok;
	}

	/**
	 * refresh listing
	 *
	 * @param string $dir	optional: directory path
	 * @return boolean
	 */
	public function refresh($dir = '') {
		if($dir != '') $this->removeCacheFolder(md5($dir));
		else $this->removeCacheFolder();
		if($this->_reloadExplorer == -1) $this->checkUpdate();
		return $this->view();
	}

	/**
	 * refresh everything!
	 *
	 * @return boolean
	 */
	public function refreshAll() {
		$this->removeCacheFolder();
		$this->Explorer->refresh();
		$this->_reloadExplorer = 0;
		return $this->view();
	}

	/**
	 * check if explorer cache or current folder has been updated
	 *
	 * @return mixed	folder ID or 'all' or -1
	 */
	public function checkUpdate() {
		$this->_reloadExplorer = $this->Explorer->checkUpdate($this->curDir);
		return $this->_reloadExplorer;
	}

	/**
	 * remove cache folder(s)
	 *
	 * @param string $id	optional: folder ID
	 */
	public function removeCacheFolder($id = '') {
		if($id != '') unset($this->_entries[$id]);
		else $this->_entries = array();
	}

	/**
	 * get entry by ID
	 *
	 * @param integer $id		entry ID
	 * @return mixed			entry object or false on failure
	 */
	public function getEntry($id) {
		if(is_array($this->_entries[$this->_folderId])) {
			foreach($this->_entries[$this->_folderId] as $Entry) {
				if($Entry->id == $id) return $Entry;
			}
		}
		return false;
	}

	/**
	 * get entry by file/directory name
	 *
	 * @param string $name		file/directory name
	 * @return mixed			entry object or false on failure
	 */
	public function getEntryByName($name) {
		if(is_array($this->_entries[$this->_folderId])) {
			foreach($this->_entries[$this->_folderId] as $Entry) {
				if($Entry->name == $name) return $Entry;
			}
		}
		return false;
	}

	/**
	 * get all entries
	 *
	 * @return FM_Entry[]	entry objects
	 */
	public function getEntries() {
		return $this->_entries[$this->_folderId];
	}

	/**
	 * move uploaded file/directory to current directory
	 *
	 * @param string $src		source file path
	 * @param string $newName	new file/directory name
	 * @param array $info		optional: container for upload info
	 * @return mixed			file/directory name on success, else boolean false
	 */
	public function upload($src, $newName, array &$info = null) {
		if(!$info) $info = array('files' => array(), 'errors' => array());

		if($this->FileManager->hideSystemFiles && $newName[0] == '.') {
			$this->FileManager->Log->add(FM_Tools::getMsg('errAccess') . ": $newName", 'error');
			return false;
		}
		$ext = strtolower(FM_Tools::getSuffix($newName, '.'));

		/* check if file extension is allowed */
		if($ext != '') {
			$hidden = $this->FileManager->hideFileTypes;
			$allowed = $this->FileManager->allowFileTypes;

			if(in_array($ext, $hidden) || ($allowed && !in_array($ext, $allowed))) {
				$this->FileManager->Log->add(FM_Tools::getMsg('errAccess') . ": $newName", 'error');
				return false;
			}
		}

		/* check if image has to be resized */
		if($this->FileManager->maxImageWidth > 0 || $this->FileManager->maxImageHeight > 0) {
			if(in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
				if($Entry = new FM_Entry($this, true)) {
					$Entry->path = $src;
					$Entry->name = FM_Tools::basename($src);
					$Entry->size = @filesize($src);
					$width = $this->FileManager->maxImageWidth;
					$height = $this->FileManager->maxImageHeight;
					$Image = new FM_Image($Entry, $width, $height);
					if($error = $Image->save()) $this->FileManager->Log->add($error, 'error');
				}
			}
		}

		/* check if file name has to be modified */
		$replSpaces = isset($_REQUEST['fmReplSpaces']) ? $_REQUEST['fmReplSpaces'] : false;
		$lowerCase = isset($_REQUEST['fmLowerCase']) ? $_REQUEST['fmLowerCase'] : false;
		$fmReplSpaces = ($this->FileManager->replSpacesUpload || $replSpaces);
		$fmLowerCase = ($this->FileManager->lowerCaseUpload || $lowerCase);
		if($fmReplSpaces) $newName = str_replace(' ', '_', $newName);
		if($fmLowerCase) $newName = strtolower($newName);

		if($this->FileManager->createBackups) {
			$this->_createBackup($newName);
		}
		$dst = $this->curDir . '/' . $newName;

		if(is_dir($src)) {
			if(!$this->FileSystem->makeDir($dst)) {
				return false;
			}
			$oldCurDir = $this->curDir;
			$this->curDir = $dst;

			if($dp = @opendir($src)) {
				while(($file = @readdir($dp)) !== false) {
					if($file != '.' && $file != '..') {
						if(!$this->upload("$src/$file", $file, $info)) {
							return false;
						}
					}
				}
				@closedir($dp);
			}
			$this->curDir = $oldCurDir;
			return $newName;
		}

		if($this->FileSystem->putFile($src, $dst)) {
			if($this->FileManager->mailOnUpload || $this->FileManager->uploadHook) {
				$info['files'][] = array(
					'path' => $dst,
					'size' => @filesize($src) # yes, that's correct - must get size from source file!
				);
			}

			if($this->FileManager->defaultFilePermissions) {
				if(!$this->FileSystem->changePerms($dst, $this->FileManager->defaultFilePermissions)) {
					$info['errors'][] = FM_Tools::getMsg('errPermChange', $newName);
				}
			}

			if($this->FileManager->uploadHook && $info['files']) {
				$file = end($info['files']);
				$this->FileManager->callUploadHook($file['path'], $file['size']);
			}
			return $newName;
		}
		return false;
	}

	/**
	 * create directory
	 *
	 * @param string $dir		directory path
	 * @return boolean
	 */
	public function mkDir($dir) {
		return $this->FileSystem->makeDir($dir);
	}

	/**
	 * perform search
	 *
	 * @param string $text		search string
	 */
	public function performSearch($text) {
		if($this->FileSystem->useUtf8) {
			$text = FM_Tools::utf8Encode($text, $this->FileManager->encoding);
		}
		else $text = FM_Tools::utf8Decode($text, $this->FileManager->encoding);

		$this->searchString = $text;
		$this->view();
	}

	/**
	 * check if directory access is allowed
	 *
	 * @param string $dir		directory path
	 * @return boolean
	 */
	public function isAllowedDir($dir) {
		$dir = preg_replace('%^/%', '', ($this->FileSystem->checkPath($dir)));
		$names = explode('/', $dir);
		$allowedDirs = $this->FileManager->startSubDirs;
		$hideDirs = $this->FileManager->hideDirNames;

		if($allowedDirs && is_array($allowedDirs)) {
			if($names[0] != '' && !in_array($names[0], $allowedDirs)) {
				return false;
			}
		}

		if($hideDirs && is_array($hideDirs)) {
			if(is_array($names)) foreach($names as $name) {
				if(in_array($name, $hideDirs)) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * read directory entries
	 *
	 * @param string $dir		directory path
	 * @return boolean
	 */
	public function readDir($dir) {
		if(!$this->sysType) {
			$this->sysType = $this->FileSystem->getSystemType();
			$this->sysType = str_replace('/', ' ', $this->sysType);
		}
		$rootDir = $this->FileManager->rootDir;

		if(strncmp($dir, $rootDir, strlen($rootDir)) != 0) {
			$dir = $this->curDir = $rootDir;
		}
		if(!$this->isAllowedDir($dir)) return false;

		$list = $this->FileSystem->readDir($dir);

		if(!is_array($list)) {
			if($this->curDir != $rootDir) {
				$this->curDir = $rootDir;
				$this->readDir($rootDir);
			}
			return false;
		}

		if($this->_folderId != 'search') {
			$this->_folderId = md5($this->curDir);
			$this->_entries[$this->_folderId] = array();
		}

		foreach($list as $row) {
			if($this->_folderId == 'search' && $row['isDeleted']) continue;
			$Entry = $this->_addEntry($row, $dir);

			if(is_object($Entry)) {
				if($this->searchString != '' && $Entry->isDir()) {
					$this->readDir($Entry->path);
				}
			}
			else if(is_string($Entry)) {
				$this->readDir("$dir/$Entry");
			}
		}
		return true;
	}

/* PROTECTED METHODS *************************************************************************** */

	/**
	 * view header
	 */
	protected function _viewHeader() {
		$path = $this->FileSystem->checkPath($this->curDir);
		$path = ($path == '') ? '/' : FM_Tools::utf8Encode($path, $this->FileManager->encoding);
		$searchString = FM_Tools::utf8Encode($this->searchString, $this->FileManager->encoding);

		$json = "cont:'{$this->FileManager->container}',";
		$json .= 'reloadExplorer:' . (int) $this->_reloadExplorer . ',';
		$json .= "search:'" . addslashes($searchString) . "',";
		$json .= "path:'" . addslashes($path) . "'";

		if($this->FileManager->fmCaption != '') {
			$json .= ",title:'" . addslashes($this->FileManager->fmCaption) . "'";
		}
		else if(!$this->FileManager->hideSystemType) {
			if(FM_Tools::strlen($this->sysType) > 15) {
				$sysType = FM_Tools::substr($this->sysType, 0, 15) . '...';
			}
			else $sysType = $this->sysType;
			$sysType = FM_Tools::utf8Encode($sysType, $this->FileManager->encoding);
			$json .= ",sysType:'" . addslashes($sysType) . "'";
		}
		print $json . ',entries:{';
	}

	/**
	 * view footer
	 */
	protected function _viewFooter() {
		print '}';
	}

	/**
	 * view directory up icon
	 *
	 * @return string
	 */
	protected function _viewDirUp() {
		$Entry = new FM_Entry($this);
		$Entry->icon = 'bullet_arrow_up';
		$Entry->type = 'cdup';
		$Entry->name = ($this->searchString == '') ? '..' : '';
		return $Entry->view();
	}

	/**
	 * add listing entry
	 *
	 * @param array $file			file information
	 * @param string $dir			optional: directory path
	 * @return mixed				entry object, directory name or false
	 */
	public function _addEntry(array $file, $dir = '') {
		if($dir == '') $dir = $this->curDir;

		/* if search is performed, $Entry will just contain the directory name */
		$Entry = $this->_createEntry($file, $dir);

		if(is_object($Entry)) {
			if($Entry->isDeleted()) {
				if(!$this->FileManager->enableRestore || !$this->FileManager->viewDeletedFiles) {
					return false;
				}
			}
			$ext = strtolower(FM_Tools::getSuffix($Entry->name, '.'));

			if($Entry->isDir()) {
				/* check if directory access is allowed */
				if(!$this->isAllowedDir($Entry->path)) return false;
			}
			else {
				/* check if file extension is allowed */
				if($ext != '') {
					$hidden = $this->FileManager->hideFileTypes;
					$allowed = $this->FileManager->allowFileTypes;

					if(in_array($ext, $hidden) || ($allowed && !in_array($ext, $allowed))) {
						return false;
					}

					if($this->FileManager->enableImagePreview) {
						if(in_array($ext, array('jpeg', 'jpg', 'gif', 'png'))) {
							$Image = new FM_Image($Entry);

							if(in_array($Image->getType(), array(1, 2, 3))) {
								$Entry->width = $Image->getWidth();
								$Entry->height = $Image->getHeight();
							}
						}
					}
				}
			}
			$Entry->id = count($this->_entries[$this->_folderId]);
			$this->_entries[$this->_folderId][] = $Entry;
		}
		else if(is_string($Entry)) {
			/* check if directory access is allowed */
			if(!$this->isAllowedDir($Entry)) return false;
		}
		return $Entry;
	}

	/**
	 * create entry, but don't add it to the current listing
	 *
	 * @param array $file			file information
	 * @param string $dir			directory path
	 * @return mixed				entry object, directory name or false
	 */
	public function _createEntry(array $file, $dir) {
		$Entry = new FM_Entry($this);
		$Entry->setProperties($file, $dir);

		if($this->searchString != '') {
			if(stristr($Entry->name, $this->searchString)) {
				return $Entry;
			}
			else if($Entry->isDir()) return $Entry->name;
		}
		else if(!$this->FileManager->hideSystemFiles || $Entry->name[0] != '.') {
			return $Entry;
		}
		return false;
	}

	/**
	 * create backup by renaming original file
	 *
	 * @param string $fileName		file name
	 */
	protected function _createBackup($fileName) {
		$parts = explode('.', $fileName);
		if(count($parts) > 1) {
			$ext = '.' . end($parts);
			$name = FM_Tools::substr($fileName, 0, FM_Tools::strlen($fileName) - FM_Tools::strlen($ext));
		}
		else {
			$ext = '';
			$name = $fileName;
		}
		$backupName = $fileName;
		$cnt = 0;

		while($this->getEntryByName($backupName)) {
			$cnt++;
			$backupName = $name . "($cnt)$ext";
		}

		if($cnt > 0) {
			$this->FileSystem->rename($this->curDir . '/' . $fileName, $this->curDir . '/' . $backupName);
		}
	}

	/**
	 * make column captions
	 *
	 * @return string
	 */
	protected function _makeCaptions() {
		$items = array('isDir', 'name');
		if(!in_array('size', $this->FileManager->hideColumns)) $items[] = 'size';
		if(!in_array('changed', $this->FileManager->hideColumns)) $items[] = 'changed';
		if(!in_array('permissions', $this->FileManager->hideColumns)) $items[] = 'permissions';
		if(!in_array('owner', $this->FileManager->hideColumns)) $items[] = 'owner';
		if(!in_array('group', $this->FileManager->hideColumns)) $items[] = 'group';
		$cont = $this->FileManager->container;
		$icon = 'menu.gif';
		$tooltip = addslashes(FM_Tools::getMsg('cmdSelAction'));
		$style = 'cursor:pointer';
		$action = "exec:['fmLib.viewMenu','bulkAction','$cont']";
		return "captions:['" . implode("','", $items) . "'],lastCol:{icon:'$icon',style:'$style',$action,tooltip:'$tooltip'}";
	}
}

?>