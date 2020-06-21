<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/FileManager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

include_once('FM_Image.php');
include_once('FM_Tools.php');
include_once('FM_FileSystem.php');

/**
 * This class manages directory listing entries.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_Entry {

/* PUBLIC PROPERTIES *************************************************************************** */

	/**
	 * file name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * file owner
	 *
	 * @var string
	 */
	public $owner;

	/**
	 * file group
	 *
	 * @var string
	 */
	public $group;

	/**
	 * file size
	 *
	 * @var integer
	 */
	public $size;

	/**
	 * last modified
	 *
	 * @var string
	 */
	public $changed;

	/**
	 * file permissions
	 *
	 * @var string
	 */
	public $permissions;

	/**
	 * file icon
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * file path
	 *
	 * @var string
	 */
	public $path;

	/**
	 * file type
	 *
	 * @var string
	 */
	public $type;

	/**
	 * image width
	 *
	 * @var integer
	 */
	public $width;

	/**
	 * image height
	 *
	 * @var integer
	 */
	public $height;

	/**
	 * stores entry ID
	 *
	 * @var integer
	 */
	public $id;

	/**
	 * unique identifier
	 *
	 * @var string
	 */
	public $hash;

	/**
	 * symbolic link target - works only on local file system
	 *
	 * @var string
	 */
	public $target;

	/**
	 * ID3 tags
	 *
	 * @var array
	 */
	public $id3Tags;

	/**
	 * holds FileManager object
	 *
	 * @var FileManager
	 */
	public $FileManager;

	/**
	 * holds listing object
	 *
	 * @var FM_Listing
	 */
	public $Listing;

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * file is in local directory (temp directory)
	 *
	 * @var boolean
	 */
	protected $_isLocalFile;

	/**
	 * entry is a directory
	 *
	 * @var boolean
	 */
	protected $_isDir;

	/**
	 * entry is deleted
	 *
	 * @var boolean
	 */
	protected $_isDeleted;

	/**
	 * file extensions
	 *
	 * @var array
	 */
	protected $_extensions = array(
		'text'    => 'txt|[sp]?html?|css|jse?|php\d*|pr?l|pm|cgi|inc|csv|py|asp|ini|sql|cfg|bat|sh|json|xml|xslt?|xsd|xul|rdf|dtd|wsdl',
		'image'   => 'gif|jpe?g|png|w?bmp|tiff?|pict?|ico',
		'archive' => 'zip|[rtj]ar|t?gz|t?bz2?|arj|ace|lzh|lha|xxe|uue?|iso|cab|r\d+',
		'program' => 'exe|com|pif|scr|app',
		'video'   => 'mpe?g|avi|mov|wmv|flv|swf|rm|mp4|3gp|webm|ogv',
		'audio'   => 'wav|mp[321]|voc|midi?|mod|ac3|wma|m4a|aiff?|au|aac|oga'
	);

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constructor
	 *
	 * @param FM_Listing $Listing	listing object
	 * @param boolean $isLocalFile	optional: file is in local directory
	 * @return FM_Entry
	 */
	public function __construct(FM_Listing $Listing, $isLocalFile = false) {
		$this->Listing = $Listing;
		$this->FileManager = $this->Listing->FileManager;
		$this->_isLocalFile = $isLocalFile;
	}

	/**
	 * view entry
	 *
	 * @return string
	 */
	public function view() {
		$entry = $this->_viewHeader() . ',';
		$entry .= $this->_viewType() . ',';
		$entry .= $this->_viewIcon() . ',';
		$entry .= $this->_viewName() . ',';
		$entry .= $this->_viewSize() . ',';
		$entry .= $this->_viewModified() . ',';
		$entry .= $this->_viewPermissions() . ',';
		$entry .= $this->_viewOwner() . ',';
		$entry .= $this->_viewGroup() . ',';
		$entry .= $this->_viewId3Tags() . ',';
		$entry .= $this->_viewHash();
		$entry .= $this->_viewFooter();
		return $entry;
	}

	/**
	 * check if entry is a directory
	 *
	 * @return boolean
	 */
	public function isDir() {
		return ($this->_isDir === true);
	}

	/**
	 * check if entry is deleted
	 *
	 * @return boolean
	 */
	public function isDeleted() {
		return $this->_isDeleted;
	}

	/**
	 * rename file or directory
	 *
	 * @param string $dst		new file/directory path
	 * @return boolean
	 */
	public function rename($dst) {
		return $this->Listing->FileSystem->rename($this->path, $dst);
	}

	/**
	 * copy file
	 *
	 * @param string $dst		file path
	 * @return boolean
	 */
	public function copyFile($dst) {
		return $this->Listing->FileSystem->copyFile($this->path, $dst);
	}

	/**
	 * delete file / directory
	 *
	 * @return boolean
	 */
	public function delete() {
		if($this->isDir()) {
			return $this->Listing->FileSystem->removeDir($this->path);
		}
		return $this->Listing->FileSystem->deleteFile($this->path);
	}

	/**
	 * restore file / directory
	 *
	 * @return boolean
	 */
	public function restore() {
		return $this->isDir()
			? $this->Listing->FileSystem->restoreDir($this->path)
			: $this->Listing->FileSystem->restoreFile($this->path);
	}

	/**
	 * save file data
	 *
	 * @param string $data		file data
	 * @return boolean
	 */
	public function saveFile(&$data) {
		return $this->Listing->FileSystem->writeFile($this->path, $data);
	}

	/**
	 * change file permissions
	 *
	 * @param integer $mode		new mode
	 * @return boolean
	 */
	public function changePerms($mode) {
		return $this->Listing->FileSystem->changePerms($this->path, $mode);
	}

	/**
	 * get document type
	 *
	 * @return integer	0 = no document, 1 = plain text, 2 = Google Docs Viewer
	 */
	public function getDocType() {
		if($this->type == 'text') return 1;
		$ext = FM_Tools::getSuffix($this->name, '.');
		return preg_match('/^(' . FM_EXT_GDVIEWER . ')$/i', $ext) ? 2 : 0;
	}

	/**
	 * send file for download
	 *
	 * @param string $disp		optional: content disposition ('attachment' or 'inline')
	 * @return boolean			false on failure
	 */
	public function sendFile($disp = '') {
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			if(strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) <= strtotime($this->changed)) {
				header('HTTP/1.1 304 Not Modified');
				header('Date: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				header('Cache-Control: public, max-age: 10');
				ob_end_flush();
				exit;
			}
		}

		$ret = $this->getFile(true);

		if(is_array($ret)) {
			$path = isset($ret[0]) ? $ret[0] : '';
			$status = isset($ret[1]) ? $ret[1] : '';
			$ftp = isset($ret[2]) ? $ret[2] : '';
		}
		else {
			$path = $ret;
			$status = $ftp = false;
		}

		if(is_file($path)) {
			$filename = $this->name;
			$File = new FM_File($this->FileManager, $path);
			if(!$disp) $disp = 'attachment';

			if($disp != 'inline') {
				if($this->FileManager->replSpacesDownload) {
					$filename = str_replace(' ', '_', $filename);
				}
				if($this->FileManager->lowerCaseDownload) {
					$filename = strtolower($filename);
				}
				if($this->FileManager->mailOnDownload) {
					$info = array('path' => $this->path, 'size' => $this->size);
					$this->FileManager->sendDownloadInfo(array($info));
				}
				if($this->FileManager->downloadHook) {
					$this->FileManager->callDownloadHook($this->path, $this->size);
				}
			}
			header('Content-Type: ' . $File->getMimeType());
			header('Content-Disposition: ' . $disp . '; filename="' . $filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . (int) $this->size);
			header('Date: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: public, max-age: 10');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($this->changed)) . ' GMT');
			ob_end_flush();

			if($status && $ftp) {
				if($fp = @fopen($path, 'r')) {
					$start = 0;

					while($status == FTP_MOREDATA) {
						@clearstatcache();
						$size = @filesize($path) - $start;
						if($start) @fseek($fp, $start);
						if($size) print @fread($fp, $size);
						$start += $size;
						$status = @ftp_nb_continue($ftp);
					}
					@fclose($fp);
				}
			}
			else readfile($path);
			exit;
		}
		return false;
	}

	/**
	 * send image
	 *
	 * @param integer $width
	 * @param integer $height
	 */
	public function sendImage($width, $height) {
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			if(strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) <= strtotime($this->changed)) {
				header('HTTP/1.1 304 Not Modified');
				header('Date: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				header('Cache-Control: public, max-age: 10');
				ob_end_flush();
				exit;
			}
		}
		header('Date: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: public, max-age: 10');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($this->changed)) . ' GMT');
		$Image = new FM_Image($this, $width, $height);
		$Image->view();
	}

	/**
	 * rotate image
	 *
	 * @param integer $angle
	 * @return string			error message
	 */
	public function rotateImage($angle) {
		$Image = new FM_Image($this);
		return $Image->rotate($angle);
	}

	/**
	 * load file from FTP server if necessary
	 *
	 * @param boolean $useFtpNb		optional: use FTP non-blocking mode
	 * @return string				local file path
	 */
	public function getFile($useFtpNb = false) {
		if($this->_isLocalFile) return $this->path;
		return $this->Listing->FileSystem->getFile($this->path, $useFtpNb);
	}

	/**
	 * set properties
	 *
	 * @param array $file		file information
	 * @param string $dir		directory path
	 */
	public function setProperties(array $file, $dir) {
		$this->_isDir = $file['isDir'];
		$this->_isDeleted = $file['isDeleted'];
		$this->permissions = $file['permissions'];
		$this->owner = $file['owner'];
		$this->group = $file['group'];
		$this->size = $file['size'];
		$this->changed = $file['changed'];
		$this->name = $file['name'];
		$this->path = $file['path'];
		$this->target = $file['target'];
		$this->type = $this->_getType();
		$this->icon = $this->_getIcon();
		$this->id3Tags = $this->Listing->FileSystem->readId3Tags($this->path);
		$this->hash = md5($this->path . $this->size . $this->changed);

		if($this->id3Tags) {
			list($name, $this->width, $this->height) = explode(':', $this->id3Tags['picture']);
			$this->id3Tags['picture'] = $name;
		}
	}

/* PROTECTED METHODS *************************************************************************** */

	/**
	 * view header
	 *
	 * @return string
	 */
	protected function _viewHeader() {
		$isDir = $this->isDir() ? 1 : 0;
		$deleted = $this->isDeleted() ? 1 : 0;
		$docType = $this->getDocType();
		return "{id:'$this->id',isDir:$isDir,deleted:$deleted,docType:$docType";
	}

	/**
	 * view footer
	 *
	 * @return string
	 */
	protected function _viewFooter() {
		return '}';
	}

	/**
	 * view icon
	 *
	 * @return string
	 */
	protected function _viewIcon() {
		return "icon:'{$this->icon}.png'";
	}

	/**
	 * view type
	 *
	 * @return string
	 */
	protected function _viewType() {
		return "type:'{$this->type}'";
	}

	/**
	 * view file name
	 *
	 * @return string
	 */
	protected function _viewName() {
		$name = $this->isDeleted()? preg_replace('/\.' . FM_EXT_DELETED . '$/', '', $this->name) : $this->name;
		$name = addslashes(FM_Tools::utf8Encode($name, $this->FileManager->encoding));
		$path = $this->Listing->FileSystem->checkPath($this->path);
		$dir = preg_replace('%^/$%', '', FM_Tools::dirname($path));
		$dir = addslashes(FM_Tools::utf8Encode($dir, $this->FileManager->encoding));
		$fullName = $this->FileManager->hideFilePath ? $name : "$dir/$name";

		if(!$this->FileManager->hideLinkTarget && $this->target != '') {
			$fullName .= addslashes(' => ' . $this->target);
		}
		$json = "name:'$name',fullName:'$fullName'";
		if($this->Listing->searchString != '') $json .= ",dir:'$dir'";
		return $json;
	}

	/**
	 * view file size
	 *
	 * @return string
	 */
	protected function _viewSize() {
		if($this->type == 'cdup') {
			$size = '';
		}
		else if($this->size < 1000) {
			$size = $this->size . ' B';
		}
		else {
			$size = $this->size / 1024;
			if($size > 999) $size = number_format($size / 1024, 1) . ' M';
			else $size = number_format($size, 1) . ' K';
		}
		return "size:'$size',width:" . (int) $this->width . ',height:' . (int) $this->height;
	}

	/**
	 * view last modification date
	 *
	 * @return string
	 */
	protected function _viewModified() {
		return "changed:'$this->changed'";
	}

	/**
	 * view permissions
	 *
	 * @return string
	 */
	protected function _viewPermissions() {
		return "permissions:'$this->permissions'";
	}

	/**
	 * view owner
	 *
	 * @return string
	 */
	protected function _viewOwner() {
		return "owner:'$this->owner'";
	}

	/**
	 * view group
	 *
	 * @return string
	 */
	protected function _viewGroup() {
		return "group:'$this->group'";
	}

	/**
	 * view ID3 tags
	 *
	 * @return string
	 */
	protected function _viewId3Tags() {
		$tags = array();
		if(is_array($this->id3Tags)) foreach($this->id3Tags as $key => $value) {
			$tags[] = ucfirst($key) . ":'" . addslashes($value) . "'";
		}
		return 'id3:{' . implode(',', $tags) . '}';
	}

	/**
	 * view hash
	 *
	 * @return string
	 */
	protected function _viewHash() {
		$hash = $this->hash ? addslashes($this->hash) : '';
		return "hash:'$hash'";
	}

	/**
	 * get icon name
	 *
	 * @return string
	 */
	protected function _getIcon() {
		if(!$this->type) $this->type = $this->_getType();
		if($this->type == 'cdup' || $this->isDir()) return 'folder';
		$ext = FM_Tools::getSuffix(strtolower($this->name), '.');
		if($ext == FM_EXT_DELETED) return 'recycle_bin';
		$icon = $this->FileManager->getIconDir() . '/big/file_extension_' . $ext . '.png';
		if(is_file($icon)) return 'file_extension_' . $ext;
		$ext = substr($ext, 0, 3);
		$icon = $this->FileManager->getIconDir() . '/big/file_extension_' . $ext . '.png';
		if(is_file($icon)) return 'file_extension_' . $ext;
		$type = ($this->type == 'text') ? 'txt' : $this->type;
		$icon = $this->FileManager->getIconDir() . '/big/file_extension_' . $type . '.png';
		return is_file($icon) ? 'file_extension_' . $type : 'document_yellow';
	}

	/**
	 * get file type
	 *
	 * @return string
	 */
	protected function _getType() {
		if($this->name == '..' || $this->name == '') return 'cdup';
		if($this->isDir()) return 'dir';
		$ext = FM_Tools::getSuffix($this->name, '.');

		foreach($this->_extensions as $key => $types) {
			if(preg_match('/^(' . $types . ')$/i', $ext)) {
				return $key;
			}
		}
		return 'file';
	}
}

?>