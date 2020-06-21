<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

 include_once('FM_FileBase.php');
 include_once('FM_Tools.php');

/**
 * This class handles file operations on a single file.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_File extends FM_FileBase {

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * MIME types
	 *
	 * @var array
	 */
	protected $_mimeTypes = array(
		'dwg'     => 'application/acad',
		'asd'     => 'application/astound',
		'tsp'     => 'application/dsptype',
		'dxf'     => 'application/dxf',
		'spl'     => 'application/futuresplash',
		'gz'      => 'application/gzip',
		'json'    => 'application/json',
		'ptlk'    => 'application/listenup',
		'hqx'     => 'application/mac-binhex40',
		'mbd'     => 'application/mbedlet',
		'mif'     => 'application/mif',
		'xls'     => 'application/msexcel',
		'xla'     => 'application/msexcel',
		'hlp'     => 'application/mshelp',
		'chm'     => 'application/mshelp',
		'ppt'     => 'application/mspowerpoint',
		'ppz'     => 'application/mspowerpoint',
		'pps'     => 'application/mspowerpoint',
		'pot'     => 'application/mspowerpoint',
		'doc'     => 'application/msword',
		'dot'     => 'application/msword',
		'bin'     => 'application/octet-stream',
		'oda'     => 'application/oda',
		'pdf'     => 'application/pdf',
		'ai'      => 'application/postscript',
		'eps'     => 'application/postscript',
		'ps'      => 'application/postscript',
		'rtc'     => 'application/rtc',
		'smp'     => 'application/studiom',
		'tbk'     => 'application/toolbook',
		'vmd'     => 'application/vocaltec-media-desc',
		'vmf'     => 'application/vocaltec-media-file',
		'xhtml'   => 'application/xhtml+xml',
		'bcpio'   => 'application/x-bcpio',
		'z'       => 'application/x-compress',
		'cpio'    => 'application/x-cpio',
		'csh'     => 'application/x-csh',
		'dcr'     => 'application/x-director',
		'dir'     => 'application/x-director',
		'dxr'     => 'application/x-director',
		'dvi'     => 'application/x-dvi',
		'evy'     => 'application/x-envoy',
		'gtar'    => 'application/x-gtar',
		'hdf'     => 'application/x-hdf',
		'php'     => 'application/x-httpd-php',
		'phtml'   => 'application/x-httpd-php',
		'latex'   => 'application/x-latex',
		'mif'     => 'application/x-mif',
		'nc'      => 'application/x-netcdf',
		'cdf'     => 'application/x-netcdf',
		'nsc'     => 'application/x-nschat',
		'sh'      => 'application/x-sh',
		'shar'    => 'application/x-shar',
		'swf'     => 'application/x-shockwave-flash',
		'cab'     => 'application/x-shockwave-flash',
		'spr'     => 'application/x-sprite',
		'sprite'  => 'application/x-sprite',
		'sit'     => 'application/x-stuffit',
		'sca'     => 'application/x-supercard',
		'sv4cpio' => 'application/x-sv4cpio',
		'sv4crc'  => 'application/x-sv4crc',
		'tar'     => 'application/x-tar',
		'tcl'     => 'application/x-tcl',
		'tex'     => 'application/x-tex',
		'texinfo' => 'application/x-texinfo',
		'texi'    => 'application/x-texinfo',
		't'       => 'application/x-troff',
		'tr'      => 'application/x-troff',
		'roff'    => 'application/x-troff',
		'troff'   => 'application/x-troff',
		'ustar'   => 'application/x-ustar',
		'src'     => 'application/x-wais-source',
		'zip'     => 'application/zip',
		'au'      => 'audio/basic',
		'snd'     => 'audio/basic',
		'es'      => 'audio/echospeech',
		'tsi'     => 'audio/tsplayer',
		'vox'     => 'audio/voxware',
		'aif'     => 'audio/x-aiff',
		'aiff'    => 'audio/x-aiff',
		'aifc'    => 'audio/x-aiff',
		'dus'     => 'audio/x-dspeeh',
		'cht'     => 'audio/x-dspeeh',
		'mid'     => 'audio/x-midi',
		'midi'    => 'audio/x-midi',
		'mp1'     => 'audio/mpeg',
		'mp2'     => 'audio/mpeg',
		'mp3'     => 'audio/mpeg',
		'm4a'     => 'audio/mp4',
		'ram'     => 'audio/x-pn-realaudio',
		'ra'      => 'audio/x-pn-realaudio',
		'rpm'     => 'audio/x-pn-realaudio-plugin',
		'stream'  => 'audio/x-qt-stream',
		'wav'     => 'audio/wav',
		'wma'     => 'audio/x-ms-wma',
		'oga'     => 'audio/ogg',
		'aac'     => 'audio/aac',
		'dwf'     => 'drawing/x-dwf',
		'cod'     => 'image/cis-cod',
		'ras'     => 'image/cmu-raster',
		'fif'     => 'image/fif',
		'gif'     => 'image/gif',
		'ief'     => 'image/ief',
		'jpeg'    => 'image/jpeg',
		'jpg'     => 'image/jpeg',
		'jpe'     => 'image/jpeg',
		'tiff'    => 'image/tiff',
		'tif'     => 'image/tiff',
		'mcf'     => 'image/vasa',
		'wbmp'    => 'image/vnd.wap.wbmp',
		'fh4'     => 'image/x-freehand',
		'fh5'     => 'image/x-freehand',
		'fhc'     => 'image/x-freehand',
		'pnm'     => 'image/x-portable-anymap',
		'pbm'     => 'image/x-portable-bitmap',
		'pgm'     => 'image/x-portable-graymap',
		'ppm'     => 'image/x-portable-pixmap',
		'rgb'     => 'image/x-rgb',
		'xwd'     => 'image/x-windowdump',
		'xbm'     => 'image/x-xbitmap',
		'xpm'     => 'image/x-xpixmap',
		'csv'     => 'text/comma-separated-values',
		'css'     => 'text/css',
		'htm'     => 'text/html',
		'html'    => 'text/html',
		'shtml'   => 'text/html',
		'js'      => 'text/javascript',
		'txt'     => 'text/plain',
		'rtx'     => 'text/richtext',
		'rtf'     => 'text/rtf',
		'tsv'     => 'text/tab-separated-values',
		'wml'     => 'text/vnd.wap.wml',
		'wmlc'    => 'application/vnd.wap.wmlc',
		'wmls'    => 'text/vnd.wap.wmlscript',
		'wmlsc'   => 'application/vnd.wap.wmlscriptc',
		'xml'     => 'text/xml',
		'etx'     => 'text/x-setext',
		'sgm'     => 'text/x-sgml',
		'sgml'    => 'text/x-sgml',
		'talk'    => 'text/x-speech',
		'spc'     => 'text/x-speech',
		'mpeg'    => 'video/mpeg',
		'mpg'     => 'video/mpeg',
		'mpe'     => 'video/mpeg',
		'qt'      => 'video/quicktime',
		'mov'     => 'video/quicktime',
		'viv'     => 'video/vnd.vivo',
		'vivo'    => 'video/vnd.vivo',
		'avi'     => 'video/x-msvideo',
		'movie'   => 'video/x-sgi-movie',
		'flv'     => 'video/x-flv',
		'wmv'     => 'video/x-ms-wmv',
		'mp4'     => 'video/mp4',
		'm4v'     => 'video/mp4',
		'webm'    => 'video/webm',
		'ogv'     => 'video/ogg',
		'vts'     => 'workbook/formulaone',
		'vtts'    => 'workbook/formulaone',
		'3dmf'    => 'x-world/x-3dmf',
		'3dm'     => 'x-world/x-3dmf',
		'qd3d'    => 'x-world/x-3dmf',
		'qd3'     => 'x-world/x-3dmf',
		'wrl'     => 'x-world/x-vrml'
	);

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * upload file
	 *
	 * @param string $dstPath	destination path (remote / target dir)
	 * @return boolean
	 */
	public function put($dstPath) {
		/* remove old versions from cache */
		$cache = $this->_FileManager->getContCacheDir();
		if($this->_FileManager->ftpHost) {
			$name = md5($this->_FileManager->ftpUser . $dstPath);
		}
		else $name = md5($dstPath);
		FM_Tools::removeFiles($cache, '/^' . $name . '/');

		if($this->_ftp) {
			return @ftp_put($this->_ftp, $dstPath, $this->_path, FTP_BINARY);
		}
		return @copy($this->_path, $dstPath);
	}

	/**
	 * delete file
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
			? @ftp_delete($this->_ftp, $this->_path)
			: @unlink($this->_path);
	}

	/**
	 * copy file
	 *
	 * @param string $dstPath	destination path
	 * @return boolean
	 */
	public function copy($dstPath) {
		if($this->_ftp) {
			$tmp = $this->_FileManager->getContCacheDir() . '/' . md5($this->_path);

			if(@ftp_get($this->_ftp, $tmp, $this->_path, FTP_BINARY)) {
				return @ftp_put($this->_ftp, $dstPath, $tmp, FTP_BINARY);
			}
			return false;
		}
		return @copy($this->_path, $dstPath);
	}

	/**
	 * get MIME type
	 *
	 * @return string
	 */
	public function getMimeType() {
		$ext = strtolower(FM_Tools::getSuffix($this->_path, '.'));

		if($this->_mimeTypes[$ext]) {
			return $this->_mimeTypes[$ext];
		}
		return 'application/octet-stream';
	}
}

?>