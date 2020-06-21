<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

@set_time_limit(6000);
@ini_set('session.gc_maxlifetime', 6000);
@ini_set('include_path', @ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__));
@ini_set('default_charset', '');

if(isset($_SERVER['PHP_AUTH_USER'])) {
	$path = preg_quote(DIRECTORY_SEPARATOR . $_SERVER['PHP_AUTH_USER'], '%');
	if($_SERVER['PHP_AUTH_USER'] != '' && !preg_match('%' . $path . '$%', @session_save_path())) {
		$sessionDir = preg_replace('/^\d+;/', '', @session_save_path()) . DIRECTORY_SEPARATOR . $_SERVER['PHP_AUTH_USER'];
		if(!is_dir($sessionDir)) @mkdir($sessionDir);
		@session_save_path($sessionDir);
		@ini_set('session.gc_probability', 1); # workaround for garbage collection on Debian systems
	}
}
if(!session_id()) @session_start();

/* global constants */
define('FM_EXT_DELETED', 'deleted');
define('FM_EXT_GDVIEWER', 'docx?|xlsx?|pptx?|pdf|pages|ai|psd|tiff|dxf|svg|eps|ps|ttf|xps|zip|rar');
define('FM_CACHE_DAYS', 7);

include_once('FM_Listing.php');
include_once('FM_Event.php');
include_once('FM_Log.php');
include_once('FM_Tools.php');

/* check memory limit */
$memoryLimit = @ini_get('memory_limit');
if($memoryLimit && FM_Tools::toBytes($memoryLimit) < 134217728) {
	@ini_set('memory_limit', '128M');
}

/**
 * This is the main class.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FileManager {

/* PUBLIC PROPERTIES *************************************************************************** */

	/* configuration variables; will be filled with content from config file */

	public $ftpHost;
	public $ftpUser;
	public $ftpPassword;
	public $ftpPort;
	public $ftpPassiveMode;
	public $ftpSSL;
	public $authUser;
	public $authPassword;
	public $language;
	public $locale;
	public $encoding;
	public $rootDir;
	public $defaultDir;
	public $startSubDirs;
	public $startSearch;
	public $fmWebPath;
	public $fmWidth;
	public $fmHeight;
	public $fmMargin;
	public $fmView;
	public $fmPrefix;
	public $fmCaption;
	public $debugInfo;
	public $logHeight;
	public $logSave;
	public $explorerWidth;
	public $explorerExpandAll;
	public $enableImagePreview;
	public $enableImageRotation;
	public $thumbMaxWidth;
	public $thumbMaxHeight;
	public $thumbSharpen;
	public $enableMediaPlayer;
	public $mediaPlayerWidth;
	public $mediaPlayerHeight;
	public $forceFlash;
	public $enableDocViewer;
	public $docViewerWidth;
	public $docViewerHeight;
	public $publicUrl;
	public $enableId3Tags;
	public $defaultFilePermissions;
	public $defaultDirPermissions;
	public $allowFileTypes;
	public $hideFileTypes;
	public $hideDirNames;
	public $hideSystemFiles;
	public $hideSystemType;
	public $hideFilePath;
	public $hideLinkTarget;
	public $hideDisabledIcons;
	public $hideColumns;
	public $hideTitleBar;
	public $markNew;
	public $useRightClickMenu;
	public $uploadEngine;
	public $keepFolderStructure;
	public $enableUpload;
	public $enableDownload;
	public $enableBulkDownload;
	public $enableEdit;
	public $enableDelete;
	public $enableRestore;
	public $enableRename;
	public $enablePermissions;
	public $enableMove;
	public $enableCopy;
	public $enableNewDir;
	public $enableSearch;
	public $createBackups;
	public $replSpacesUpload;
	public $replSpacesDownload;
	public $lowerCaseUpload;
	public $lowerCaseDownload;
	public $maxImageWidth;
	public $maxImageHeight;
	public $loginPassword;
	public $mailOnUpload;
	public $mailOnUploadSubject;
	public $mailOnDownload;
	public $mailOnDownloadSubject;
	public $uploadHook;
	public $downloadHook;
	public $tmpFilePath;
	public $logFilePath;
	public $logFilePrefix;
	public $useFileCache;
	public $viewDeletedFiles;
	public $smartRefresh;
	public $customAction;

	/**
	 * HTML container name
	 *
	 * @var string
	 */
	public $container;

	/**
	 * holds Log object
	 *
	 * @var FM_Log
	 */
	public $Log;

	/**
	 * binary modes
	 *
	 * @var array
	 */
	public $binaryModes = array('getFile', 'getFiles', 'loadFile', 'getThumbnail', 'getCachedImage');

	/**
	 * true if Perl is enabled
	 *
	 * @var boolean
	 */
	public $perlEnabled;

	/**
	 * path to temporary directory
	 *
	 * @var string
	 */
	public $tmpDir;

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * file manager directory path (for includes)
	 *
	 * @var string
	 */
	protected $_incPath;

	/**
	 * holds listing object
	 *
	 * @var FM_Listing
	 */
	protected $_Listing;

	/**
	 * HTML container name
	 *
	 * @var string
	 */
	protected $_titleCont;

	/**
	 * HTML container name
	 *
	 * @var string
	 */
	protected $_listCont;

	/**
	 * HTML container name
	 *
	 * @var string
	 */
	protected $_expCont;

	/**
	 * HTML container name
	 *
	 * @var string
	 */
	protected $_logCont;

	/**
	 * HTML container name
	 *
	 * @var string
	 */
	protected $_infoCont;

	/**
	 * user access
	 *
	 * @var boolean
	 */
	protected $_access;

	/**
	 * Perl version (if available)
	 *
	 * @var string
	 */
	protected $_perlVersion;

	/**
	 * config variables that should be converted to arrays
	 *
	 * @var array
	 */
	protected $_arrays = array('startSubDirs', 'allowFileTypes', 'hideFileTypes', 'hideDirNames', 'hideColumns', 'loginPassword');

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constructor
	 *
	 * @param string $rootDir		optional: directory path
	 * @return FileManager
	 */
	public function __construct($rootDir = '') {
		$this->_incPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/..'));
		$this->tmpDir = $this->_incPath . '/tmp';
		$this->initFromConfig();

		if($rootDir != '') $this->rootDir = $rootDir;
		if($this->fmWebPath == '') $this->fmWebPath = FM_Tools::getWebPath();
		if($this->locale) @setlocale(LC_ALL, $this->locale);
	}

	/**
	 * initialization from config file
	 */
	public function initFromConfig() {
		$config = parse_ini_file($this->_incPath . '/config.inc.php');

		foreach($config as $key => $val) {
			if($key == 'defaultFilePermissions' || $key == 'defaultDirPermissions') {
				$val = octdec(preg_replace('/^0/', '', $val));
			}
			else if(in_array($key, $this->_arrays)) {
				$val = preg_split('/\s*,\s*/', $val);
				if($val[0] == '') $val = array();
			}
			$this->$key = $val;
		}
		if(!$this->ftpPort) $this->ftpPort = 21;
	}

	/**
	 * create file manager
	 *
	 * @return string	HTML code
	 */
	public function create() {
		global $fmCnt, $fmWebPath, $fmEncoding, $fmReplSpacesUpload, $fmLowerCaseUpload;

		ob_start();

		$this->_checkVars();

		if(!is_dir($this->tmpDir)) {
			FM_Tools::makeDir($this->tmpDir);
		}

		if(!$fmCnt) {
			$fmCnt = 1;
			$fmWebPath = $this->fmWebPath;
			$fmReplSpacesUpload = $this->replSpacesUpload;
			$fmLowerCaseUpload = $this->lowerCaseUpload;
			include_once($this->_incPath . '/template.inc.php');
			$this->_cleanTmpDirs();
		}

		/* ugly workaround for FM_Tools::getMsg */
		$fmEncoding[$fmCnt] = $this->encoding;

		if($this->logSave) {
			$logDir = ($this->logFilePath != '') ? $this->logFilePath : $this->_incPath . '/log';
		}
		else $logDir = '';

		$this->Log = new FM_Log($this->encoding, $logDir, $this->logFilePrefix);

		if($this->uploadEngine == 'perl') $this->_checkPerl();
		$this->_getLanguageFile();

		$this->container = $this->fmPrefix . 'Cont' . $fmCnt;
		$this->_titleCont = $this->container . 'Title';
		$this->_listCont = $this->container . 'List';
		$this->_expCont = $this->container . 'Exp';
		$this->_logCont = $this->container . 'Log';
		$this->_infoCont = $this->container . 'Info';

		if(isset($_COOKIE[$this->container . 'LoginPwd'])) {
			$this->_checkAccess($_COOKIE[$this->container . 'LoginPwd']);
		}
		$this->_checkRootDir();
		$this->_save();

		$this->_viewHeader();
		$this->_viewFooter();

		$fmCnt++;

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 * return listing object
	 *
	 * @return FM_Listing
	 */
	public function getListing() {
		if(!$this->_Listing) {
			if($this->defaultDir != '') {
				$dir = $this->rootDir . '/' . $this->defaultDir;
			}
			else $dir = '';

			$this->_Listing = new FM_Listing($this, $dir);
		}
		return $this->_Listing;
	}

	/**
	 * get user's temporary directory
	 *
	 * @param boolean $create		create directory if it doesn't exist
	 * @return string
	 */
	public function getUserTmpDir($create = true) {
		$dir = $this->tmpDir . '/tmp/' . $this->container . session_id();
		if($create && !is_dir($dir)) FM_Tools::makeDir($dir, $this);
		return $dir;
	}

	/**
	 * get temporary directory of container
	 *
	 * @param boolean $create		create directory if it doesn't exist
	 * @return string
	 */
	public function getContTmpDir($create = true) {
		$dir = $this->tmpDir . '/tmp/' . $this->container;
		if($create && !is_dir($dir)) FM_Tools::makeDir($dir, $this);
		return $dir;
	}

	/**
	 * get cache directory of container
	 *
	 * @param boolean $create		create directory if it doesn't exist
	 * @return string
	 */
	public function getContCacheDir($create = true) {
		$cacheDir = $this->tmpDir . '/cache/' . $this->container;
		if($create && !is_dir($cacheDir)) FM_Tools::makeDir($cacheDir);
		return $cacheDir;
	}

	/**
	 * get user's upload directory
	 *
	 * @param boolean $create		create directory if it doesn't exist
	 * @return string
	 */
	public function getUserUploadDir($create = true) {
		$dir = $this->tmpDir . '/upload/' . $this->container . session_id();
		if($create && !is_dir($dir)) FM_Tools::makeDir($dir);

		if($this->uploadEngine == 'perl') {
			$dir .= '/files';
			if($create && !is_dir($dir)) FM_Tools::makeDir($dir);
		}
		return $dir;
	}

	/**
	 * get path to icons
	 *
	 * @return string
	 */
	public function getIconDir() {
		return $this->_incPath . '/icn';
	}

	/**
	 * remove user's upload directory
	 */
	public function removeUserUploadDir() {
		FM_Tools::removeDir($this->tmpDir . '/upload/' . $this->container . session_id());
	}

	/**
	 * clean user's upload directory
	 */
	public function cleanUserUploadDir() {
		FM_Tools::cleanDir($this->tmpDir . '/upload/' . $this->container . session_id(), null, true);
	}

	/**
	 * perform requested action
	 */
	public function action() {
		$this->_getLanguageFile();

		$fmMode = isset($_REQUEST['fmMode']) ? $_REQUEST['fmMode'] : '';
		$fmObject = isset($_REQUEST['fmObject']) ? $_REQUEST['fmObject'] : '';
		$fmName = isset($_REQUEST['fmName']) ? $_REQUEST['fmName'] : '';
		$loginPwd = isset($_COOKIE[$this->container . 'LoginPwd']) ? $_COOKIE[$this->container . 'LoginPwd'] : '';

		/* handle special events */
		switch($fmMode) {
			case 'getContSettings':
			case 'getMessages':
			case 'getUserPerms':
			case 'getThumbnail':
			case 'getCachedImage':
			case 'getFile':
			case 'getFiles':
			case 'loadFile':
			case 'jupload':
				$this->getListing();
				$Event = new FM_Event($this);
				$Event->handle($fmMode, $fmObject, $fmName);
				break;

			case 'getExplorer':
			case 'checkUpdate':
				$this->getListing();
				$Event = new FM_Event($this);
				$Event->handle($fmMode, $fmObject, $fmName);
				$this->_save();
				break;

			case 'login':
				$loginPwd = $fmName;
				if($this->startSearch != '') {
					$fmMode = 'search';
					$fmName = $this->startSearch;
				}
				else {
					$fmMode = 'refresh';
					$fmName = '';
				}
				/* fall through */

			default:
				if(!$this->ftpHost && $this->rootDir == '') {
					FileManager::error('SECURITY ALERT:<br/>Please set a start directory or an FTP server!');
				}
				else if(!is_writable($this->tmpDir)) {
					FileManager::error('FileManager cannot use tmp directory!');
				}
				else {
					print '{';

					if($this->_checkAccess($loginPwd)) {
						$this->getListing();
						$this->_Listing->Explorer->setCacheFile();
						$Event = new FM_Event($this);
						$error = $Event->handle($fmMode, $fmObject, $fmName);
						if($error != '') print ",error:'" . addslashes($error) . "'";
						if($this->ftpHost) $this->_Listing->FileSystem->ftpClose();
					}
					else $this->_viewLogin();

					if($this->debugInfo) print ',' . $this->_getDebugInfo();
					if(($log = $this->_getLogMessages()) !== '') print ",messages:[$log]";
					print ',end:1}';
					$this->_save();
				}
		}
	}

	/**
	 * send upload info e-mail
	 *
	 * @param array $files
	 */
	public function sendUploadInfo($files) {
		if(is_array($files) && strstr($this->mailOnUpload, '@')) {
			$date = @date('Y-m-d H:i:s');
			$ip = $this->Log->remoteIp;
			$body = "The following files have been uploaded on $date by IP address $ip:\n\n";
			foreach($files as $file) $body .= $file['path'] . ' ' . $file['size'] . " B\n";
			@mail($this->mailOnUpload, $this->mailOnUploadSubject, $body);
		}
	}

	/**
	 * send download info e-mail
	 *
	 * @param array $files
	 */
	public function sendDownloadInfo($files) {
		if(is_array($files) && strstr($this->mailOnDownload, '@')) {
			$date = @date('Y-m-d H:i:s');
			$ip = $this->Log->remoteIp;
			$body = "The following files have been downloaded on $date by IP address $ip:\n\n";
			foreach($files as $file) $body .= $file['path'] . ' ' . $file['size'] . " B\n";
			@mail($this->mailOnDownload, $this->mailOnDownloadSubject, $body);
		}
	}

	/**
	 * call upload hook
	 *
	 * @param string $path
	 * @param integer $size
	 * @return string
	 */
	public function callUploadHook($path, $size) {
		$ip = $this->Log->remoteIp;
		$path = urlencode($path);
		$url = $this->uploadHook;
		$url .= (strstr($url, '?') ? '&' : '?') . "size=$size&ip=$ip&file=$path";
		return FM_Tools::callUrl($url, $errstr);
	}

	/**
	 * call download hook
	 *
	 * @param string $path
	 * @param integer $size
	 * @return string
	 */
	public function callDownloadHook($path, $size) {
		$ip = $this->Log->remoteIp;
		$path = urlencode($path);
		$url = $this->downloadHook;
		$url .= (strstr($url, '?') ? '&' : '?') . "size=$size&ip=$ip&file=$path";
		return FM_Tools::callUrl($url, $errstr);
	}

/* PUBLIC STATIC METHODS *********************************************************************** */

	/**
	 * exit FileManager with error message
	 *
	 * @param string $msg
	 */
	public static function error($msg) {
		$cont = isset($_REQUEST['fmContainer']) ? $_REQUEST['fmContainer'] : '';
		$d = @date('Y-m-d H:i:s');
		$m = addslashes($msg);
		die("{cont:'$cont',lang:'en',title:'ERROR',error:'$m',messages:[{time:'$d',text:'$m',type:'error'}],end:1}");
	}

/* PROTECTED METHODS *************************************************************************** */

	/**
	 * view header
	 */
	protected function _viewHeader() {
		$fmWidth = strstr($this->fmWidth, '%') ? $this->fmWidth : (int) $this->fmWidth . 'px';
		$fmHeight = strstr($this->fmHeight, '%') ? $this->fmHeight : (int) $this->fmHeight . 'px';
		$expWidth = strstr($this->explorerWidth, '%') ? $this->explorerWidth : (int) $this->explorerWidth;
		$listWidth = strstr($this->explorerWidth, '%') ? 100 - (int) $this->explorerWidth . '%' : '';

		print "<div id=\"$this->container\" class=\"fmTH1\" onMouseOver=\"fmLib.setContMenu(false)\" onMouseOut=\"fmLib.setContMenu(true)\" ";
		print "style=\"position:relative; width:$fmWidth; height:$fmHeight; padding:1px; margin:{$this->fmMargin}px\">\n";
		if(!$this->hideTitleBar) print "<div id=\"$this->_titleCont\" class=\"fmTH1\" style=\"height:25px; overflow:hidden\"></div>\n";
		print "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr>\n";
		if($this->explorerWidth) print "<td width=\"$expWidth\"><div id=\"$this->_expCont\" class=\"fmTD2\" style=\"overflow:auto\"></div></td>\n";
		print "<td width=\"$listWidth\"><div id=\"$this->_listCont\" class=\"fmTD1\" style=\"overflow:auto\"></div></td>\n";
		print "</tr></table>\n";
	}

	/**
	 * view footer
	 */
	protected function _viewFooter() {
		if($this->logHeight > 5) {
			print "<div class=\"fmLogWindow\" style=\"height:{$this->logHeight}px; margin-top:1px\">";
			print "<div id=\"$this->_logCont\" class=\"fmLogWindow\" ";
			print "style=\"height:{$this->logHeight}px; text-align:left; ";
			print 'padding-top:4px; padding-left:4px; overflow:auto">';
			print "</div></div>\n";
		}
		print "</div>\n";

		if($this->debugInfo) {
			$fmWidth = strstr($this->fmWidth, '%') ? $this->fmWidth : (int) $this->fmWidth . 'px';
			print "<div id=\"$this->_infoCont\" class=\"fmDebugInfo\" ";
			print "style=\"width:$fmWidth; height:150px; overflow:auto; margin:{$this->fmMargin}; margin-top:10px\">";
			print "</div>\n";
		}
		$url = $this->fmWebPath . '/action.php?fmContainer=' . $this->container;
		$mode = ($this->startSearch != '') ? 'search&fmName=' . addslashes($this->startSearch) : 'refresh';
		print "<script type=\"text/javascript\">\n";
		print "fmLib.initFileManager('$url', '$mode');\n";
		print "</script>\n";
	}

	/**
	 * view login form
	 */
	protected function _viewLogin() {
		print "title:'" . addslashes(FM_Tools::getMsg('cmdLogin')) . "',cont:'$this->container',";
		print "lang:'$this->language',width:'$this->fmWidth',";
		print "login:{submit:'{$this->container}Login'}";
	}

	/**
	 * get language file
	 */
	protected function _getLanguageFile() {
		global $msg;

		if($this->language == '') $this->language = 'en';
		$file = $this->_incPath . '/languages/lang_' . $this->language . '.inc';
		$data = FM_Tools::readLocalFile($file);

		if(preg_match_all('/(\w+)\s*=\s*(.+)/', $data, $m)) {
			for($i = 0; $i < count($m[0]); $i++) {
				$key = trim($m[1][$i]);
				$val = trim($m[2][$i]);
				$msg[$key] = $val;
			}
		}
	}

	/**
	 * get debug info
	 */
	protected function _getDebugInfo() {
		$cookie = array();

		foreach(session_get_cookie_params() as $key => $val) {
			$cookie[] = "$key => $val";
		}
		$json =  "debug:{cookie:'" . addslashes(implode('<br/>', $cookie)) . "',";
		$json .= "phpVersion:'" . addslashes(phpversion()) . "',";
		$json .= "perlVersion:'" . addslashes($this->_perlVersion) . "',";
		$json .= "memoryLimit:'" . FM_Tools::bytes2string(FM_Tools::toBytes(@ini_get('memory_limit'))) . "',";
		$json .= "memoryUsage:'" . FM_Tools::bytes2string(FM_Tools::getMemoryUsage()) . "',";
		$json .= "lang:'$this->language',";
		$json .= "locale:'$this->locale (system: " . addslashes(preg_replace('/;[^\s]/', '; ', @setlocale(LC_ALL, '0'))) . ")',";
		$json .= "encoding:'" . addslashes($this->encoding) . "',";
		$json .= "uploadEngine:'" . addslashes(in_array($this->uploadEngine, array('php', 'js')) ? strtoupper($this->uploadEngine) : ucfirst($this->uploadEngine)) . "',";
		$json .= "perlEnabled:'" . ($this->perlEnabled ? 'yes' : 'no') . "',";
		$json .= "webPath:'" . addslashes($this->fmWebPath) . "',";
		$json .= "rootDir:'" . addslashes($this->rootDir) . "',";
		$json .= 'maxImageWidth:' . (int) $this->maxImageWidth . ',';
		$json .= 'maxImageHeight:' . (int) $this->maxImageHeight . ',';
		$json .= "forceFlash:'" . ($this->forceFlash ? 'yes' : 'no') . "',";
		$json .= "refresh:'" . (($this->smartRefresh > 0) ? (int) $this->smartRefresh . ' sec' : 'off') . "',";
		$json .= "curDir:'" . ($this->_Listing ? addslashes($this->_Listing->curDir) : '') . "',";
		$json .= "search:'" . ($this->_Listing ? addslashes($this->_Listing->searchString) : '') . "',";
		$json .= 'cache:' . FM_Tools::getFileCount($this->getContCacheDir(false)) . '}';
		return $json;
	}

	/**
	 * get log messages
	 *
	 * @return string
	 */
	protected function _getLogMessages() {
		if($this->logHeight > 0 && $this->_Listing) {
			return $this->Log->get();
		}
		return '';
	}

	/**
	 * save FileManager object
	 */
	protected function _save() {
		$_SESSION[$this->container] = serialize($this);
	}

	/**
	 * check if Perl uploader can be used
	 */
	protected function _checkPerl() {
		$url = $this->fmWebPath . '/cgi/check.pl';

		if($this->authUser != '') {
			$url = preg_replace('%^(https?://)(.+)$%i', '$1' . $this->authUser . ':' . $this->authPassword . '@$2', $url);
		}
		$response = FM_Tools::callUrl($url, $errstr);

		if($errstr != '') {
			$this->Log->add(trim($errstr), 'error');
		}
		else {
			list($header, $content) = explode("\r\n\r\n", $response);

			if(strlen($content) < 50 && preg_match('/^Perl ([\d\.]+)/i', trim($content), $m)) {
				$this->_perlVersion = $m[1];
				if($this->uploadEngine == 'perl') $this->perlEnabled = true;
				$this->Log->add("$m[0] detected", 'info');
			}
		}
	}

	/**
	 * clean temporary directories
	 */
	protected function _cleanTmpDirs() {
		$time1 = time() - FM_CACHE_DAYS * 86400;
		$time2 = time() - 86400;
		FM_Tools::cleanDir($this->tmpDir . '/cache', $time1, true);
		FM_Tools::cleanDir($this->tmpDir . '/upload', $time2, true);
		FM_Tools::cleanDir($this->tmpDir . '/tmp', $time2, true);
	}

	/**
	 * check root directory
	 */
	protected function _checkRootDir() {
		/* ugly workaround for deprecated $startDir variable */
		if($this->rootDir == '' && isset($this->startDir)) {
			$this->rootDir = $this->startDir;
		}

		if($this->rootDir != '') {
			if(!$this->ftpHost) $this->rootDir = realpath($this->rootDir);
			$this->rootDir = str_replace('\\', '/', $this->rootDir);

			if($this->ftpHost) {
				$this->rootDir = preg_replace('%/*\.\.%', '', $this->rootDir);
				$this->rootDir = preg_replace('%^/+%', '', $this->rootDir);
			}
		}
		if($this->ftpHost && $this->rootDir == '') $this->rootDir = '/';
	}

	/**
	 * check access
	 *
	 * @param string $loginPassword
	 * @return boolean
	 */
	protected function _checkAccess($loginPassword) {
		if(!$this->_access) {
			if(is_array($this->loginPassword) && count($this->loginPassword) > 0) {
				foreach($this->loginPassword as $val) {
					if(strstr($val, '::')) {
						list($pwd, $dir) = preg_split('/\s*::\s*/', $val);
					}
					else {
						$pwd = $val;
						$dir = '';
					}

					if($pwd == $loginPassword) {
						if($dir != '') $this->rootDir = $dir;
						$this->_access = true;
						break;
					}
				}
			}
			else $this->_access = true;
		}
		return $this->_access;
	}

	/**
	 * check/validate config variables
	 */
	protected function _checkVars() {
		foreach($this->_arrays as $prop) {
			if(!is_array($this->$prop)) {
				$val = preg_split('/\s*,\s*/', $this->$prop);
				if($val[0] == '') $val = array();
				$this->$prop = $val;
			}
		}
		if($this->tmpFilePath != '') $this->tmpDir = $this->tmpFilePath;
		$this->uploadEngine = strtolower($this->uploadEngine);
		$this->language = str_replace('_', '-', $this->language);

		if(!in_array($this->uploadEngine, array('js', 'java', 'perl', 'php'))) {
			$this->uploadEngine = 'php';
		}
		$this->encoding = strtoupper($this->encoding);
		if($this->encoding == 'UTF8') $this->encoding = 'UTF-8';
	}
}

?>