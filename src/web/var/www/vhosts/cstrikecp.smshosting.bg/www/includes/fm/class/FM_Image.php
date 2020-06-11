<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

 include_once('FM_Tools.php');

/**
 * This class manages images and thumbnails.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_Image {

/* PROTECTED PROPERTIES ************************************************************************ */

	/**
	 * image file path
	 *
	 * @var string
	 */
	protected $_file;

	/**
	 * original image width
	 *
	 * @var integer
	 */
	protected $_srcWidth;

	/**
	 * original image height
	 *
	 * @var integer
	 */
	protected $_srcHeight;

	/**
	 * thumbnail width
	 *
	 * @var integer
	 */
	protected $_dstWidth;

	/**
	 * thumbnail height
	 *
	 * @var integer
	 */
	protected $_dstHeight;

	/**
	 * image type
	 *
	 * @var integer
	 */
	protected $_type;

	/**
	 * sharpen thumbnail
	 *
	 * @var boolean
	 */
	protected $_sharpen;

	/**
	 * path to cached image
	 *
	 * @var string
	 */
	protected $_cachePath;

	/**
	 * holds entry object
	 *
	 * @var FM_Entry
	 */
	protected $_Entry;

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constructor
	 *
	 * @param FM_Entry $Entry		entry object
	 * @param integer $maxWidth		optional: max. thumbnail width
	 * @param integer $maxHeight	optional: max. thumbnail height
	 * @return FM_Image
	 */
	public function __construct(FM_Entry $Entry, $maxWidth = null, $maxHeight = null) {
		$this->_Entry = $Entry;
		$this->_file = $Entry->getFile();
		$this->_sharpen = $Entry->FileManager->thumbSharpen;

		if($this->_file != '') {
			list($this->_srcWidth, $this->_srcHeight, $this->_type) = @getimagesize($this->_file);
			$this->_calcDstSize($maxWidth, $maxHeight);
			$this->_cachePath = $this->_getCachePath();
		}
	}

	/**
	 * view resized image
	 */
	public function view() {
		if($this->isCached()) {
			$this->_send(null, $this->_cachePath);
		}
		else if($this->_dstWidth && $this->_dstHeight) {
			if($srcImg = $this->_create()) {
				$dstImg = $this->_resize($srcImg);

				if($this->_cachePath != '') {
					$this->_save($dstImg, $this->_cachePath);
				}
				$this->_send($dstImg);
			}
			else $this->_send();
		}
		else $this->_send();
	}

	/**
	 * save resized image
	 *
	 * @return string		error message
	 */
	public function save() {
		if(!in_array($this->_type, array(1, 2, 3))) {
			return "Image type $this->_type not supported";
		}

		if($this->_dstWidth && $this->_dstHeight) {
			if($srcImg = $this->_create()) {
				$dstImg = $this->_resize($srcImg);
				return $this->_save($dstImg);
			}
			return 'Could not create resized image';
		}
		return '';
	}

	/**
	 * rotate image
	 *
	 * @param integer $angle	must be 90 or 270
	 * @return string			error message
	 */
	public function rotate($angle) {
		if($srcImg = $this->_create()) {
			$dstImg = $this->_rotate($srcImg, $angle);
			return $this->_save($dstImg);
		}
		return 'Could not create rotated image';
	}

	/**
	 * get image width
	 *
	 * @return integer
	 */
	public function getWidth() {
		return (int) $this->_srcWidth;
	}

	/**
	 * get image height
	 *
	 * @return integer
	 */
	public function getHeight() {
		return (int) $this->_srcHeight;
	}

	/**
	 * get image type
	 *
	 * @return integer
	 */
	public function getType() {
		return (int) $this->_type;
	}

	/**
	 * check if image is cached
	 *
	 * @return boolean
	 */
	public function isCached() {
		if(!$this->_Entry->FileManager->useFileCache) return false;
		return ($this->_cachePath != '' && is_file($this->_cachePath));
	}

/* PROTECTED METHODS *************************************************************************** */

	/**
	 * create image
	 *
	 * @return resource
	 */
	protected function _create() {
		$srcImg = null;

		switch($this->_type) {

			case IMAGETYPE_GIF:
				if(function_exists('ImageCreateFromGIF')) {
					$srcImg = @ImageCreateFromGIF($this->_file);
				}
				break;

			case IMAGETYPE_JPEG:
				if(function_exists('ImageCreateFromJPEG')) {
					$srcImg = @ImageCreateFromJPEG($this->_file);
				}
				break;

			case IMAGETYPE_PNG:
				if(function_exists('ImageCreateFromPNG')) {
					$srcImg = @ImageCreateFromPNG($this->_file);
				}
				break;
		}
		return $srcImg;
	}

	/**
	 * resize image
	 *
	 * @param resource $srcImg		original image
	 * @return resource				resized image
	 */
	protected function _resize($srcImg) {
		if($this->_type != IMAGETYPE_GIF && function_exists('ImageCreateTrueColor')) {
			$dstImg = @ImageCreateTrueColor($this->_dstWidth, $this->_dstHeight);
		}
		else $dstImg = @ImageCreate($this->_dstWidth, $this->_dstHeight);

		if(function_exists('ImageCopyResampled')) {
			@ImageCopyResampled($dstImg, $srcImg, 0, 0, 0, 0, $this->_dstWidth, $this->_dstHeight, $this->_srcWidth, $this->_srcHeight);
		}
		else @ImageCopyResized($dstImg, $srcImg, 0, 0, 0, 0, $this->_dstWidth, $this->_dstHeight, $this->_srcWidth, $this->_srcHeight);

		/* do not sharpen GIF images! */
		if($this->_sharpen && $this->_type != IMAGETYPE_GIF) {
			$dstImg = $this->_unsharpMask($dstImg);
		}
		return $dstImg;
	}

    /**
	 * rotate image
	 *
	 * @param resource $srcImg		original image
	 * @param integer $angle		must be 90 or 270
	 * @return resource				rotated image
	 */
	protected function _rotate($srcImg, $angle) {
		if(!in_array($angle, array(90, 270))) {
			return $srcImg;
		}

		if(function_exists('ImageRotate')) {
			return @ImageRotate($srcImg, $angle, 0);
		}

		if($this->_type != IMAGETYPE_GIF && function_exists('ImageCreateTrueColor')) {
			$dstImg = @ImageCreateTrueColor($this->_srcHeight, $this->_srcWidth);
		}
		else $dstImg = @ImageCreate($this->_srcHeight, $this->_srcWidth);

		switch($angle) {

			case 90:
				for($x = 0; $x < $this->_srcWidth; $x++) {
					for($y = 0; $y < $this->_srcHeight; $y++) {
						@ImageCopy($dstImg, $srcImg, $y, $this->_srcWidth - $x - 1, $x, $y, 1, 1);
					}
				}
				return $dstImg;

			case 270:
				for($x = 0; $x < $this->_srcWidth; $x++) {
					for($y = 0; $y < $this->_srcHeight; $y++) {
						@ImageCopy($dstImg, $srcImg, $this->_srcHeight - $y - 1, $x, $x, $y, 1, 1);
					}
				}
				return $dstImg;
		}
		return $srcImg;
	}

	/**
	 * send image
	 *
	 * @param resource $img		optional: image resource
	 * @param string $path		optional: file path
	 */
	protected function _send($img = null, $path = '') {
		if($path == '') $path = $this->_file;

		switch($this->_type) {

			case IMAGETYPE_GIF:
				if($img && function_exists('ImageGIF')) {
					header('Content-type: image/gif');
					@ImageGIF($img);
				}
				else if($img && function_exists('ImagePNG')) {
					header('Content-type: image/png');
					@ImagePNG($img);
				}
				else if($path != '') {
					header('Content-type: image/gif');
					readfile($path);
				}
				break;

			case IMAGETYPE_JPEG:
				if($img && function_exists('ImageJPEG')) {
					header('Content-type: image/jpeg');
					@ImageJPEG($img);
				}
				else if($path != '') {
					header('Content-type: image/jpeg');
					readfile($path);
				}
				break;

			case IMAGETYPE_PNG:
				if($img && function_exists('ImagePNG')) {
					header('Content-type: image/png');
					@ImagePNG($img);
				}
				else if($path != '') {
					header('Content-type: image/png');
					readfile($path);
				}
				break;

			default:
				header('Content-type: image/gif');
				print base64_decode('R0lGODlhCgAKAIAAAMDAwAAAACH5BAEAAAAALAAAAAAKAAoAAAIIhI+py+0PYysAOw==');
		}
		exit;
	}

	/**
	 * save image
	 *
	 * @param resource $img		image resource
	 * @param string $path		optional: destination path
	 * @return string			error message
	 */
	protected function _save($img, $path = '') {
		if($path == '') $path = $this->_file;

		switch($this->_type) {

			case IMAGETYPE_GIF:
				if(function_exists('ImageGIF')) {
					@ImageGIF($img, $path);
					return '';
				}
				return 'GIF images not supported';

			case IMAGETYPE_JPEG:
				if(function_exists('ImageJPEG')) {
					@ImageJPEG($img, $path);
					return '';
				}
				return 'JPG images not supported';

			case IMAGETYPE_PNG:
				if(function_exists('ImagePNG')) {
					@ImagePNG($img, $path);
					return '';
				}
				return 'PNG images not supported';
		}
		return "Image type $this->_type not supported";
	}

	/**
	 * calculate thumbnail size
	 *
	 * @param integer $maxWidth		max. thumbnail width
	 * @param integer $maxHeight	max. thumbnail height
	 */
	protected function _calcDstSize($maxWidth, $maxHeight) {
		if($this->_srcWidth > $maxWidth || $this->_srcHeight > $maxHeight) {
			$pw = $maxWidth / $this->_srcWidth;
			$ph = $maxHeight / $this->_srcHeight;
			$p = ($pw < $ph) ? $pw : $ph;
			$this->_dstWidth = round($this->_srcWidth * $p);
			$this->_dstHeight = round($this->_srcHeight * $p);
		}
	}

	/**
	 * get path to image in cache
	 *
	 * @return string
	 */
	protected function _getCachePath() {
		$dir = $this->_Entry->FileManager->getContCacheDir();
		if(!preg_match('%^' . preg_quote($dir, '%') . '%', $this->_file)) {
			$name = md5($this->_Entry->Listing->curDir . '/' . $this->_Entry->name);
		}
		else $name = FM_Tools::getPrefix(FM_Tools::basename($this->_file), '.');

		$name .= '-' . $this->_Entry->size;
		if($this->_dstWidth && $this->_dstHeight) {
			$name .= '-' . $this->_dstWidth . 'x' . $this->_dstHeight;
		}
		$name .= '.' . strtolower(FM_Tools::getSuffix($this->_Entry->name, '.'));
		return $dir . '/' . $name;
	}

	/**
	 * Unsharp Mask for PHP - version 2.1.1
	 *
	 * Unsharp mask algorithm by Torstein H�nsi 2003-07.
	 * thoensi_at_netcom_dot_no.
	 */
	protected function _unsharpMask($img, $amount = 80, $radius = 0.5, $threshold = 3) {
		if($amount > 500) $amount = 500;
		$amount = $amount * 0.016;
		if($radius > 50) $radius = 50;
		$radius = $radius * 2;
		if($threshold > 255) $threshold = 255;

		$radius = abs(round($radius));
		if($radius == 0) return $img;
		$w = imagesx($img);
		$h = imagesy($img);
		$imgCanvas = imagecreatetruecolor($w, $h);
		$imgBlur = imagecreatetruecolor($w, $h);

		if(function_exists('imageconvolution')) { // PHP >= 5.1
			$matrix = array(
				array(1, 2, 1),
				array(2, 4, 2),
				array(1, 2, 1)
			);
			imagecopy($imgBlur, $img, 0, 0, 0, 0, $w, $h);
			imageconvolution($imgBlur, $matrix, 16, 0);
		}
		else {
			for($i = 0; $i < $radius; $i++) {
				imagecopy($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left
				imagecopymerge($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right
				imagecopymerge($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center
				imagecopy($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);
				imagecopymerge($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333 ); // up
				imagecopymerge($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down
			}
		}

		if($threshold > 0) {
			for($x = 0; $x < $w-1; $x++) { // each row
				for($y = 0; $y < $h; $y++) { // each pixel
					$rgbOrig = ImageColorAt($img, $x, $y);
					$rOrig = (($rgbOrig >> 16) & 0xFF);
					$gOrig = (($rgbOrig >> 8) & 0xFF);
					$bOrig = ($rgbOrig & 0xFF);

					$rgbBlur = ImageColorAt($imgBlur, $x, $y);

					$rBlur = (($rgbBlur >> 16) & 0xFF);
					$gBlur = (($rgbBlur >> 8) & 0xFF);
					$bBlur = ($rgbBlur & 0xFF);

					$rNew = (abs($rOrig - $rBlur) >= $threshold)
						? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
						: $rOrig;
					$gNew = (abs($gOrig - $gBlur) >= $threshold)
						? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
						: $gOrig;
					$bNew = (abs($bOrig - $bBlur) >= $threshold)
						? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
						: $bOrig;

					if(($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
						$pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
						ImageSetPixel($img, $x, $y, $pixCol);
					}
				}
			}
		}
		else {
			for($x = 0; $x < $w; $x++) { // each row
				for($y = 0; $y < $h; $y++) { // each pixel
					$rgbOrig = ImageColorAt($img, $x, $y);
					$rOrig = (($rgbOrig >> 16) & 0xFF);
					$gOrig = (($rgbOrig >> 8) & 0xFF);
					$bOrig = ($rgbOrig & 0xFF);

					$rgbBlur = ImageColorAt($imgBlur, $x, $y);

					$rBlur = (($rgbBlur >> 16) & 0xFF);
					$gBlur = (($rgbBlur >> 8) & 0xFF);
					$bBlur = ($rgbBlur & 0xFF);

					$rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
					if($rNew > 255) $rNew = 255;
					elseif($rNew < 0) $rNew = 0;
					$gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
					if($gNew > 255) $gNew = 255;
					elseif($gNew < 0) $gNew = 0;
					$bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
					if($bNew > 255) $bNew = 255;
					elseif($bNew < 0) $bNew = 0;
					$rgbNew = ($rNew << 16) + ($gNew << 8) + $bNew;
					ImageSetPixel($img, $x, $y, $rgbNew);
				}
			}
		}
		imagedestroy($imgCanvas);
		imagedestroy($imgBlur);
		return $img;
	}
}

?>