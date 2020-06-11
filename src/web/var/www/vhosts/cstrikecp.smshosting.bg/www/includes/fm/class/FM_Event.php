<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

include_once('FM_Entry.php');
include_once('FM_Editor.php');
include_once('FM_Explorer.php');
include_once('FM_Tools.php');
include_once('FM_Zip.php');

/**
 * This class handles all user and system events.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_Event {

/* PROTECTED PROPERTIES ************************************************************************ */

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
	 * @param FileManager $FileManager
	 * @return FM_Event
	 */
	public function __construct(FileManager $FileManager) {
		$this->_FileManager = $FileManager;
		$this->_Listing = $FileManager->getListing();
	}

	/**
	 * handle event
	 *
	 * @param string $type		event type
	 * @param string $id		optional: object ID(s)
	 * @param string $param		optional: additional parameter(s)
	 * @return string
	 */
	public function handle($type, $id = '', $param = '') {
		switch($type) {

			case 'open':
				return $this->_openDir($id);

			case 'expOpen':
				return $this->_openExpDir($id);

			case 'parent':
				return $this->_parentDir();

			case 'rename':
				return $this->_rename($id, $param);

			case 'delete':
				return $this->_delete($id);

			case 'restore':
				return $this->_restore($id);

			case 'newDir':
				return $this->_newDir($param);

			case 'newFile':
				return $this->_newFile();

			case 'refresh':
				return $this->_refresh($id);

			case 'refreshAll':
				return $this->_refreshAll();

			case 'permissions':
				if(isset($_REQUEST['fmPerms'])) {
					return $this->_changePermissions($id, $_REQUEST['fmPerms']);
				}
				return '';

			case 'edit':
				return $this->_editFile($id);

			case 'search':
				return $this->_search($param);

			case 'saveFromUrl':
				return $this->_saveFromUrl($param);

			case 'upload':
				return $this->_upload();

			case 'toggleDeleted':
				return $this->_toggleDeleted();

			case 'getUserPerms':
				return $this->_getUserPerms();

			case 'getMessages':
				return $this->_getMessages();

			case 'getContSettings':
				return $this->_getContSettings();

			case 'getThumbnail':
				return $this->_getThumbnail($id);

			case 'getCachedImage':
				return $this->_getCachedImage($id);

			case 'getFile':
				return $this->_getFile($id, 'attachment', $this->_FileManager->enableDownload);

			case 'getFiles':
				$files = $this->_getFiles(explode(',', $id));
				return $this->_sendZip($files);

			case 'loadFile':
				return $this->_getFile($id, 'inline');

			case 'readTextFile':
				return $this->_readTextFile($id);

			case 'getExplorer':
				return $this->_getExplorer();

			case 'move':
				if($this->_FileManager->enableMove) {
					return $this->_move($id, $param);
				}
				$this->_Listing->view();
				return '';

			case 'copy':
				if($this->_FileManager->enableCopy) {
					return $this->_move($id, $param, true);
				}
				$this->_Listing->view();
				return '';

			case 'rotateLeft':
				return $this->_rotateImage($id, 90);

			case 'rotateRight':
				return $this->_rotateImage($id, 270);

			case 'jupload':
				return $this->_createJavaUploader();

			case 'checkUpdate':
				return $this->_checkUpdate();

			case '':
				$this->_Listing->view();
				$postMaxSize = ini_get('post_max_size');
				$error = "PHP post_max_size = $postMaxSize";
				$this->_FileManager->Log->add($error, 'error');
				return $error;
		}
		$this->_Listing->view();
		return '';
	}

/* PROTECTED METHODS *************************************************************************** */

	/**
	 * open directory
	 *
	 * @param integer $id
	 * @return string
	 */
	protected function _openDir($id) {
		if($Entry = $this->_Listing->getEntry($id)) {
			if($Entry->isDir()) {
				$this->_Listing->curDir = $Entry->path;
				$this->_Listing->searchString = '';

				if(!$this->_Listing->view()) {
					return FM_Tools::getMsg('errOpen', $Entry->name);
				}
			}
		}
	}

	/**
	 * open explorer directory
	 *
	 * @param integer $id
	 * @return string
	 */
	protected function _openExpDir($id) {
		$Folder = $this->_Listing->Explorer->getFolderById($id);
		if($Folder) {
			$this->_Listing->curDir = $Folder->path;
			$this->_Listing->searchString = '';

			if(!$this->_Listing->view()) {
				return FM_Tools::getMsg('errOpen', FM_Tools::basename($Folder->path));
			}
		}
		return '';
	}

	/**
	 * return to parent directory
	 */
	protected function _parentDir() {
		$this->_Listing->curDir = preg_replace('%/[^/]+$%', '', $this->_Listing->curDir);
		$this->_Listing->searchString = '';
		$this->_Listing->view();
	}

	/**
	 * rename file / directory
	 *
	 * @param integer $id
	 * @param string $name
	 * @return string
	 */
	protected function _rename($id, $name) {
		$error = '';
		if($this->_FileManager->enableRename && $name != '' && $id != '') {
			if($Entry = $this->_Listing->getEntry($id)) {
				$path = FM_Tools::dirname($Entry->path);
				if(get_magic_quotes_gpc()) $name = stripslashes($name);
				$name = FM_Tools::basename($name);

				if($this->_FileManager->replSpacesUpload) {
					$name = str_replace(' ', '_', $name);
				}

				if($this->_FileManager->lowerCaseUpload) {
					$name = strtolower($name);
				}

				if(!$Entry->rename("$path/$name")) {
					$error = FM_Tools::getMsg('errRename', "$Entry->name &raquo; $name");
				}
			}
		}
		$this->_Listing->refresh($this->_Listing->curDir);
		return $error;
	}

	/**
	 * delete files / directories
	 *
	 * @param string $ids
	 * @return string
	 */
	protected function _delete($ids) {
		$errors = array();
		$refreshAll = false;

		if($this->_FileManager->enableDelete && $ids != '') {
			foreach(explode(',', $ids) as $id) {
				if($Entry = $this->_Listing->getEntry($id)) {
					if(!$Entry->delete()) {
						$errors[] = FM_Tools::getMsg('errDelete', $Entry->name);
					}
					else if($Entry->isDir()) $refreshAll = true;
				}
			}
		}
		$this->_Listing->refresh($refreshAll ? '' : $this->_Listing->curDir);
		return implode('<br />', $errors);
	}

	/**
	 * restore files
	 *
	 * @param string $ids
	 * @return string
	 */
	protected function _restore($ids) {
		$errors = array();
		if($this->_FileManager->enableRestore && $ids != '') {
			foreach(explode(',', $ids) as $id) {
				if($Entry = $this->_Listing->getEntry($id)) {
					if(!$Entry->restore()) {
						$errors[] = FM_Tools::getMsg('errRestore', $Entry->name);
					}
				}
			}
		}
		$this->_Listing->refresh($this->_Listing->curDir);
		return implode('<br />', $errors);
	}

	/**
	 * create new directory
	 *
	 * @param string $name
	 * @return string
	 */
	protected function _newDir($name) {
		$error = '';
		if($this->_FileManager->enableNewDir) {
			if($name != '') {
				if(get_magic_quotes_gpc()) $name = stripslashes($name);
				$name = str_replace('\\', '/', $name);

				if($this->_FileManager->replSpacesUpload) {
					$name = str_replace(' ', '_', $name);
				}

				if($this->_FileManager->lowerCaseUpload) {
					$name = strtolower($name);
				}
				$dirs = explode('/', $name);
				$dir = '';

				for($i = 0; $i < count($dirs); $i++) {
					if($dirs[$i] != '') {
						if($dir != '') $dir .= '/';
						$dir .= $dirs[$i];
						$curDir = $this->_Listing->curDir;

						if(!$this->_Listing->mkDir("$curDir/$dir", 0755)) {
							$this->_Listing->refresh($this->_Listing->curDir);
							$error = FM_Tools::getMsg('errDirNew', $dir);
							break;
						}
						else if($this->_FileManager->defaultDirPermissions) {
							if(!$this->_Listing->FileSystem->changePerms("$curDir/$dir", $this->_FileManager->defaultDirPermissions)) {
								$this->_Listing->refresh($this->_Listing->curDir);
								$error = FM_Tools::getMsg('errPermChange', $dir);
								break;
							}
						}
					}
				}
			}
		}
		$this->_Listing->refresh($this->_Listing->curDir);
		return $error;
	}

	/**
	 * upload file(s) via PHP
	 *
	 * @return string
	 */
	protected function _newFile() {
		$uploaded = array('files' => array(), 'errors' => array());
		if($this->_FileManager->enableUpload) {
			$fmFile = $_FILES['fmFile'];
			if(is_array($fmFile)) {
				for($i = 0; $i < count($fmFile['size']); $i++) {
					$file = $fmFile['name'][$i];

					if($fmFile['size'][$i]) {
						if(($newFile = $this->_Listing->upload($fmFile['tmp_name'][$i], $file, $uploaded)) === false) {
							$uploaded['errors'][] = FM_Tools::getMsg('errSave', $file);
						}
					}
					else if($file != '') {
						$maxFileSize = ini_get('upload_max_filesize');
						$postMaxSize = ini_get('post_max_size');
						$info = "PHP settings: upload_max_filesize = $maxFileSize, post_max_size = $postMaxSize";
						$uploaded['errors'][] = FM_Tools::getMsg('error', "$file = 0 B<br />$info");
						$this->_FileManager->Log->add("Could not upload $file ($info)", 'error');
					}
				}
			}

			if($this->_FileManager->mailOnUpload && $uploaded['files']) {
				$this->_FileManager->sendUploadInfo($uploaded['files']);
			}
		}
		$this->_Listing->refresh($this->_Listing->curDir);
		return implode('<br />', $uploaded['errors']);
	}

	/**
	 * read file from URL and save it in current directory
	 *
	 * @param string $url
	 * @return string
	 */
	protected function _saveFromUrl($url) {
		if(!$this->_FileManager->enableUpload) {
			$this->_Listing->view();
			return 'Upload not allowed';
		}

		if($url == '') {
			$this->_Listing->view();
			return 'Missing URL';
		}
		$uploaded = array('files' => array(), 'errors' => array());
		$newFile = FM_Tools::basename($url);
		$src = $this->_FileManager->getUserTmpDir() . '/' . $newFile;

		if(!$this->_Listing->FileSystem->saveFromUrl($url, $this->_FileManager->getUserTmpDir())) {
			$this->_Listing->view();
			return FM_Tools::getMsg('errOpen', $newFile);
		}

		if(($newFile = $this->_Listing->upload($src, $newFile, $uploaded)) === false) {
			$this->_Listing->view();
			return FM_Tools::getMsg('errSave', $newFile);
		}

		if($this->_FileManager->mailOnUpload && $uploaded['files']) {
			$this->_FileManager->sendUploadInfo($uploaded['files']);
		}
		$this->_Listing->refresh($this->_Listing->curDir);
		return $uploaded['errors'];
	}

	/**
	 * read file(s) from upload directory and save it in current directory;
	 * this method is used by the JS, Java and Perl upload engines
	 *
	 * @return string
	 */
	protected function _upload() {
		if(!$this->_FileManager->enableUpload) {
			$this->_Listing->view();
			return 'Upload not allowed';
		}
		$uploaded = array('files' => array(), 'errors' => array());
		$uplDir = $this->_FileManager->getUserUploadDir();

		if($dp = @opendir($uplDir)) {
			$curDir = $this->_Listing->curDir;
			while(($file = @readdir($dp)) !== false) {
				if($file != '.' && $file != '..') {
					if(($newFile = $this->_Listing->upload("$uplDir/$file", $file, $uploaded)) === false) {
						$uploaded['errors'][] = FM_Tools::getMsg('errSave', $file);
					}
				}
			}
			@closedir($dp);
			$this->_Listing->curDir = $curDir;
		}

		if($this->_FileManager->mailOnUpload && $uploaded['files']) {
			$this->_FileManager->sendUploadInfo($uploaded['files']);
		}
		$this->_FileManager->cleanUserUploadDir();
		$this->_Listing->refresh($this->_Listing->curDir);
		return implode('<br />', $uploaded['errors']);
	}

	/**
	 * refresh listing
	 *
	 * @param integer $id	folder ID
	 */
	protected function _refresh($id) {
		if($id != '' && $id != 'all' && $id >= 0) {
			$Folder = $this->_Listing->Explorer->getFolderById($id);
			$dir = $Folder->path;
			$this->_Listing->refresh($dir);
		}
		else $this->_Listing->refresh();
	}

	/**
	 * refresh everything!
	 */
	protected function _refreshAll() {
		$this->_Listing->refreshAll();
	}

	/**
	 * change file / directory permissions
	 *
	 * @param string $ids
	 * @param array $perms
	 * @return string
	 */
	protected function _changePermissions($ids, $perms) {
		$errors = array();
		if($this->_FileManager->enablePermissions && is_array($perms) && $ids != '') {
			$ids = explode(',', $ids);
			foreach($ids as $id) {
				if($Entry = $this->_Listing->getEntry($id)) {
					$mode = '';
					for($i = 0; $i < 9; $i++) {
						$mode .= $perms[$i] ? 1 : 0;
					}
					if(!$Entry->changePerms(bindec($mode))) {
						$errors[] = FM_Tools::getMsg('errPermChange', $Entry->name);
					}
				}
			}
		}
		$this->_Listing->refresh($this->_Listing->curDir);
		return implode('<br />', $errors);
	}

	/**
	 * edit file
	 *
	 * @param integer $id
	 * @return string
	 */
	protected function _editFile($id) {
		if($this->_FileManager->enableEdit && $id != '') {
			if($Entry = $this->_Listing->getEntry($id)) {
				if($_POST['fmText'] != '') {
					$fmText = $_POST['fmText'];
					if(!FM_Tools::isUtf8(FM_Tools::readLocalFile($Entry->getFile()))) {
						$fmText = FM_Tools::utf8Decode($fmText, $this->_FileManager->encoding);
					}

					if(!$Entry->saveFile($fmText)) {
						$this->_Listing->refresh($this->_Listing->curDir);
						return FM_Tools::getMsg('errSave', $Entry->name);
					}
					$this->_Listing->refresh($this->_Listing->curDir);
				}
				else {
					$Editor = new FM_Editor($this->_FileManager);
					$Editor->view($Entry);
				}
			}
		}
		return '';
	}

	/**
	 * perform search
	 *
	 * @param string $value
	 */
	protected function _search($value) {
		$this->_Listing->performSearch($value);
	}

	/**
	 * view or hide deleted files
	 */
	protected function _toggleDeleted() {
		$this->_FileManager->viewDeletedFiles = !$this->_FileManager->viewDeletedFiles;
		$this->_Listing->removeCacheFolder();
		$this->_Listing->view();
	}

	/**
	 * get user permissions
	 */
	protected function _getUserPerms() {
		print '{';
		print 'download:' . (int) $this->_FileManager->enableDownload . ',';
		print 'bulkDownload:' . (int) $this->_FileManager->enableBulkDownload . ',';
		print 'upload:' . (int) $this->_FileManager->enableUpload . ',';
		print 'remove:' . (int) $this->_FileManager->enableDelete . ',';
		print 'restore:' . (int) $this->_FileManager->enableRestore . ',';
		print 'rename:' . (int) $this->_FileManager->enableRename . ',';
		print 'permissions:' . (int) $this->_FileManager->enablePermissions . ',';
		print 'edit:' . (int) $this->_FileManager->enableEdit . ',';
		print 'move:' . (int) $this->_FileManager->enableMove . ',';
		print 'copy:' . (int) $this->_FileManager->enableCopy . ',';
		print 'newDir:' . (int) $this->_FileManager->enableNewDir . ',';
		print 'mediaPlayer:' . (int) $this->_FileManager->enableMediaPlayer . ',';
		print 'docViewer:' . (int) $this->_FileManager->enableDocViewer . ',';
		print 'imgViewer:' . (int) $this->_FileManager->enableImagePreview . ',';
		print 'rotate:' . (int) $this->_FileManager->enableImageRotation . ',';
		print 'search:' . (int) $this->_FileManager->enableSearch . ',';
		print 'hideDisabledIcons:' . (int) $this->_FileManager->hideDisabledIcons;
		print '}';
	}

	/**
	 * get messages
	 */
	protected function _getMessages() {
		global $msg;

		print '{';
		$m = array();
		foreach($msg as $key => $val) $m[] = "$key:'" . addslashes($val) . "'";
		print implode(',', $m);
		print '}';
	}

	/**
	 * get container settings
	 */
	protected function _getContSettings() {
		print '{';
		print "sid:'" . $this->_FileManager->container . session_id() . "',";
		print "tmp:'" . addslashes($this->_FileManager->tmpFilePath) . "',";
		print "language:'" . addslashes($this->_FileManager->language) . "',";
		print "sort:{field:'isDir',order:'asc'},";
		print "listType:'" . addslashes($this->_FileManager->fmView) . "',";
		print 'markNew:' . (int) $this->_FileManager->markNew . ',';
		print 'useRightClickMenu:' . (int) $this->_FileManager->useRightClickMenu . ',';
		print 'viewDeleted:' . (int) $this->_FileManager->viewDeletedFiles . ',';
		print 'smartRefresh:' . (int) $this->_FileManager->smartRefresh . ',';
		print 'logHeight:' . (int) $this->_FileManager->logHeight . ',';
		print 'fmWidth:' . (strstr($this->_FileManager->fmWidth, '%') ? "'" . addslashes($this->_FileManager->fmWidth) . "'" : (int) $this->_FileManager->fmWidth) . ',';
		print 'explorerWidth:' . (strstr($this->_FileManager->explorerWidth, '%') ? "'" . addslashes($this->_FileManager->explorerWidth) . "'" : (int) $this->_FileManager->explorerWidth) . ',';
		print 'thumbMaxWidth:' . (int) $this->_FileManager->thumbMaxWidth . ',';
		print 'thumbMaxHeight:' . (int) $this->_FileManager->thumbMaxHeight . ',';
		print 'mediaPlayerWidth:' . (int) $this->_FileManager->mediaPlayerWidth . ',';
		print 'mediaPlayerHeight:' . (int) $this->_FileManager->mediaPlayerHeight . ',';
		print 'forceFlash:' . (int) $this->_FileManager->forceFlash . ',';
		print 'docViewerWidth:' . (int) $this->_FileManager->docViewerWidth . ',';
		print 'docViewerHeight:' . (int) $this->_FileManager->docViewerHeight . ',';
		print "publicUrl:'" . addslashes($this->_FileManager->publicUrl) . "',";
		print "uploadEngine:'" . addslashes($this->_FileManager->uploadEngine) . "',";
		print 'perlEnabled:' . (int) $this->_FileManager->perlEnabled . ',';
		print 'hideFileCnt:' . (int) (count($this->_FileManager->allowFileTypes) > 0 || count($this->_FileManager->hideFileTypes) > 0) . ',';
		print 'customAction:' . $this->_FileManager->customAction;
		print '}';
	}

	/**
	 * get thumbnail
	 *
	 * @param integer $id
	 */
	protected function _getThumbnail($id) {
		if($Entry = $this->_Listing->getEntry($id)) {
			$width = isset($_REQUEST['width']) ? $_REQUEST['width'] : $this->_FileManager->thumbMaxWidth;
			$height = isset($_REQUEST['height']) ? $_REQUEST['height'] : $this->_FileManager->thumbMaxHeight;
			$Entry->sendImage($width, $height);
		}
	}

	/**
	 * get cached image
	 *
	 * @param integer $id
	 */
	protected function _getCachedImage($name) {
		$cacheDir = $this->_FileManager->getContCacheDir();
		$file = $cacheDir . '/' . $name;

		if(is_file($file)) {
			if($Entry = new FM_Entry($this->_FileManager->getListing(), true)) {
				$Entry->path = $file;
				$Entry->name = FM_Tools::basename($file);
				$Entry->size = @filesize($file);
				$width = isset($_REQUEST['width']) ? $_REQUEST['width'] : $this->_FileManager->thumbMaxWidth;
				$height = isset($_REQUEST['height']) ? $_REQUEST['height'] : $this->_FileManager->thumbMaxHeight;
				$Entry->sendImage($width, $height);
			}
		}
	}

	/**
	 * get file
	 *
	 * @param integer $id		entry ID
	 * @param string $disp		optional: content disposition ('attachment' or 'inline')
	 * @param boolean $enabled	optional: download permission
	 */
	protected function _getFile($id, $disp = '', $enabled = true) {
		if($enabled && $id != '') {
			if($Entry = $this->_Listing->getEntry($id)) {
				$Entry->sendFile($disp);
			}
		}
	}

	/**
	 * get selected files
	 *
	 * @param array $ids		entry IDs
	 * @return array
	 */
	protected function _getFiles($ids) {
		$files = $dirs = array();

		if($this->_FileManager->enableDownload) {
			if(is_array($ids)) foreach($ids as $id) {
				if($Entry = $this->_Listing->getEntry($id)) {
					if($Entry->isDir()) $dirs[] = $Entry;
					else $files[] = $Entry;
				}
			}

			if(count($dirs) > 0) foreach($dirs as $Entry) {
				if($this->_Listing->readDir($Entry->path)) {
					$entries = $this->_Listing->getEntries();

					if(is_array($entries)) {
						$newIds = array();
						foreach($entries as $Entry) $newIds[] = $Entry->id;
						$files = array_merge($files, $this->_getFiles($newIds, $dir));
					}
				}
			}
		}
		return $files;
	}

	/**
	 * send ZIP archive
	 *
	 * @param FM_Entry[] $entries
	 */
	protected function _sendZip($entries) {
		if(is_array($entries) && count($entries) > 0) {
			$downloads = array();
			$memoryLimit = FM_Tools::toBytes(@ini_get('memory_limit'));
			$filename = 'FM_' . date('Y-m-d_H-i-s') . '.zip';

			$Zip = new FM_Zip($this->_FileManager->encoding);
			$Zip->startTransfer($filename);

			foreach($entries as $Entry) {
				$name = FM_Tools::substr($Entry->path, FM_Tools::strlen($this->_Listing->curDir), FM_Tools::strlen($Entry->path));
				$name = preg_replace('%^/%', '', $name);
				$time = @strtotime($Entry->changed);
				$data = '';

				if($memoryLimit) {
					$usedMemory = FM_Tools::getMemoryUsage();
					if($usedMemory + $Entry->size > $memoryLimit) {
						$data = 'Could not add file - size exceeds memory limit.';
						$name .= '.txt';
						$time = time();
					}
				}

				if($data == '') {
					$data = FM_Tools::readLocalFile($Entry->getFile());
					if($this->_FileManager->mailOnDownload) {
						$downloads[] = array('path' => $Entry->path, 'size' => $Entry->size);
					}
					if($this->_FileManager->downloadHook) {
						$this->_FileManager->callDownloadHook($Entry->path, $Entry->size);
					}
				}
				$Zip->addFile($data, $name, $time);
			}

			if($this->_FileManager->mailOnDownload && count($downloads) > 0) {
				$this->_FileManager->sendDownloadInfo($downloads);
			}
			$Zip->finishTransfer();
		}
	}

	/**
	 * read text file
	 *
	 * @param integer $id		entry ID
	 */
	protected function _readTextFile($id) {
		if($id != '') {
			if($Entry = $this->_Listing->getEntry($id)) {
				$Editor = new FM_Editor($this->_FileManager);
				$Editor->view($Entry);
			}
		}
	}

	/**
	 * get directory tree
	 */
	protected function _getExplorer() {
		$cont = $this->_FileManager->container;
		print "{cont:'$cont',explorer:" . $this->_Listing->Explorer->make() . '}';
	}

	/**
	 * move file / directory
	 *
	 * @param string $entryIds
	 * @param integer $folderId
	 * @param boolean $copy			optional: copy file
	 * @return string
	 */
	protected function _move($entryIds, $folderId, $copy = false) {
		$errors = array();
		$Folder = $this->_Listing->Explorer->getFolderById($folderId);

		if($Folder) {
			$entryIds = explode(',', $entryIds);
			foreach($entryIds as $entryId) {
				$Entry = $this->_Listing->getEntry($entryId);
				if($copy && !$Entry->isDir()) {
					if(!$Entry->copyFile($Folder->path . '/' . $Entry->name)) {
						$errors[] = FM_Tools::getMsg('errSave', $Entry->name);
					}
				}
				else if(!$Entry->rename($Folder->path . '/' . $Entry->name)) {
					$errors[] = FM_Tools::getMsg('errRename', $Entry->name);
				}
			}
		}
		else $errors[] = FM_Tools::getMsg('errOpen');

		$this->_Listing->refreshAll();
		return implode('<br />', $errors);
	}

	/**
	 * rotate image
	 *
	 * @param integer $id
	 * @param integer $angle
	 * @return string
	 */
	protected function _rotateImage($id, $angle) {
		$error = '';
		if($this->_FileManager->enableImageRotation) {
			if($Entry = $this->_Listing->getEntry($id)) {
				$error = $Entry->rotateImage($angle);
			}
		}
		$this->_Listing->refresh($this->_Listing->curDir);
		return $error;
	}

	/**
	 * create Java uploader
	 */
	protected function _createJavaUploader() {
		include_once('ext/jupload/jupload.php');

		$allowFileTypes = is_array($this->_FileManager->allowFileTypes) ? implode('/', $this->_FileManager->allowFileTypes) : '';
		$specificHeaders = array();

		if($this->_FileManager->authUser != '') {
			$specificHeaders[] = 'Authorization: Basic ' . base64_encode($this->_FileManager->authUser . ':' . $this->_FileManager->authPassword);
		}

		$appletParameters = array(
			'maxFileSize' => '2G',
			'archive' => 'ext/jupload/wjhk.jupload.jar',
			'width' => 520,
			'height' => 250,
			'lookAndFeel' => 'system',
			'lang' => $this->_FileManager->language,
			'encoding' => $this->_FileManager->encoding,
			'allowedFileExtensions' => $allowFileTypes,
			'specificHeaders' => implode('\n', $specificHeaders),
			'afterUploadURL' => 'javascript:parent.document.fmJavaUpload.submit()'
		);

		$classParameters = array(
			'demo_mode' => false,
			'allow_subdirs' => $this->_FileManager->keepFolderStructure,
			'spaces_in_subdirs' => true,
			'destdir' => $this->_FileManager->getUserUploadDir()
		);

		$juploadPhpSupportClass = new JUpload($appletParameters, $classParameters);

		print "<html>\n<head>\n";
		print "<!--JUPLOAD_JSCRIPT-->\n";
		print "<link rel=\"stylesheet\" href=\"css/filemanager.css\" type=\"text/css\">\n";
		print "</head>\n";
		print "<body class=\"fmTD3\" style=\"margin:0px; padding:0px\">\n";
		print "<div align=\"center\">\n";
		print "<!--JUPLOAD_APPLET-->\n";
		print "</div>\n</body>\n</html>\n";
	}

	/**
	 * check for update
	 *
	 * @return integer		folder ID
	 */
	protected function _checkUpdate() {
		if($this->_FileManager->smartRefresh > 0) {
			print $this->_Listing->checkUpdate();
		}
	}
}

?>