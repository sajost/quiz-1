<?php
// src/AppBundle/Utils/Ses.php
namespace AppBundle\Utils;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 *
 * @author yc0p401
 *         @date 10.02.2015 - 15:42:07
 *        
 */
class Ses {
	
	/** @var User */
	private static $l = null;
	
	/**
	 *
	 * @return unknown
	 */
	public static function lg($fname = 'logg') {
		// if ($l != null) return $this->l;
		$l = new Logger ( 'name' );
		$l->pushHandler ( new StreamHandler ( $fname . '.log', Logger::WARNING ) );
		// $l->addError('File move '.$this->getUploadRootDir().' -> '.$this->getFile()->getClientOriginalName());
		// in the Controller use this only
		// $logger = $this->get('logger');
		// $logger->error('upload...');
		return $l;
	}
	
	/**
	 *
	 * @param $typ -
	 *        	if user-login-name
	 * @return string
	 */
	public static function getUpDirTmp($typ) {
		// the absolute directory where uploaded files should be saved
		return Ses::getUpDirImg () . "/" . $typ;
		/*
		 * switch ($typ){
		 * case "blog":
		 * return Ses::getUpDirTmpBlog();
		 * break;
		 * case "car":
		 * return Ses::getUpDirTmpCar();
		 * break;
		 * case "carlog":
		 * return Ses::getUpDirTmpCarLog();
		 * break;
		 * case "user":
		 * return Ses::getUpDirTmpUser();
		 * break;
		 * case "tmp":
		 * return Ses::getUpDirTemp();
		 * break;
		 * case "img":
		 * return Ses::getUpDirImg();
		 * break;
		 * default:
		 * return Ses::getUpDirImg()."/".$typ;
		 * break;
		 * }
		 */
	}
	public static function getUpDirTmpBlog() {
		// the absolute directory blogs where uploaded files should be saved
		return __DIR__ . '/../../../images/b';
	}
	public static function getUpDirTmpUser() {
		// the absolute directory user where uploaded files should be saved
		return __DIR__ . '/../../../images/u';
	}
	public static function getUpDirTmpCar() {
		// the absolute directory cars where uploaded files should be saved
		return __DIR__ . '/../../../images/c';
	}
	public static function getUpDirTmpCarLog() {
		// the absolute directory carlogs where uploaded files should be saved
		return __DIR__ . '/../../../images/cl';
	}
	public static function getUpDirTemp() {
		// the absolute directory carlogs where uploaded files should be saved
		return __DIR__ . '/../../../images/tmp';
	}
	public static function getUpDirImg() {
		// get rid of the __DIR__ so it doesn't screw up
		// when displaying uploaded doc/image in the view.
		return __DIR__ . '/../../../images';
	}
	public static function getUpDir() {
		return __DIR__ . '/../../../web';
	}
	
	/**
	 * Images scaling
	 * 
	 * @param string $ini_path
	 *        	Path to initial image.
	 * @param string $dest_path
	 *        	Path to save new image.
	 * @param array $params
	 *        	[optional] Must be an associative array of params
	 *        	$params['width'] int New image width.
	 *        	$params['height'] int New image height.
	 *        	$params['constraint'] array.$params['constraint']['width'], $params['constraint'][height]
	 *        	If specified the $width and $height params will be ignored.
	 *        	New image will be resized to specified value either by width or height.
	 *        	$params['aspect_ratio'] bool If false new image will be stretched to specified values.
	 *        	If true aspect ratio will be preserved an empty space filled with color $params['rgb']
	 *        	It has no sense for $params['constraint'].
	 *        	$params['crop'] bool If true new image will be cropped to fit specified dimensions. It has no sense for $params['constraint'].
	 *        	$params['rgb'] Hex code of background color. Default 0xFFFFFF.
	 *        	$params['quality'] int New image quality (0 - 100). Default 100.
	 * @return bool True on success.
	 */
	public static function imgResize($ini_path, $dest_path, $params = array()) {
		$width = ! empty ( $params ['width'] ) ? $params ['width'] : null;
		$height = ! empty ( $params ['height'] ) ? $params ['height'] : null;
		$constraint = ! empty ( $params ['constraint'] ) ? $params ['constraint'] : false;
		$rgb = ! empty ( $params ['rgb'] ) ? $params ['rgb'] : 0xFFFFFF;
		$quality = ! empty ( $params ['quality'] ) ? $params ['quality'] : 100;
		$aspect_ratio = isset ( $params ['aspect_ratio'] ) ? $params ['aspect_ratio'] : true;
		$crop = isset ( $params ['crop'] ) ? $params ['crop'] : true;
		
		if (! file_exists ( $ini_path ))
			return false;
		
		if (! is_dir ( $dir = dirname ( $dest_path ) ))
			mkdir ( $dir );
		
		$img_info = getimagesize ( $ini_path );
		if ($img_info === false)
			return false;
		
		$ini_p = $img_info [0] / $img_info [1];
		if ($constraint) {
			$con_p = $constraint ['width'] / $constraint ['height'];
			$calc_p = $constraint ['width'] / $img_info [0];
			
			if ($ini_p < $con_p) {
				$height = $constraint ['height'];
				$width = $height * $ini_p;
			} else {
				$width = $constraint ['width'];
				$height = $img_info [1] * $calc_p;
			}
		} else {
			if (! $width && $height) {
				$width = ($height * $img_info [0]) / $img_info [1];
			} else if (! $height && $width) {
				$height = ($width * $img_info [1]) / $img_info [0];
			} else if (! $height && ! $width) {
				$width = $img_info [0];
				$height = $img_info [1];
			}
		}
		
		$match = [ ];
		preg_match ( '/\.([^\.]+)$/i', basename ( $dest_path ), $match );
		$ext = $match [1];
		$output_format = ($ext == 'jpg') ? 'jpeg' : $ext;
		
		$format = strtolower ( substr ( $img_info ['mime'], strpos ( $img_info ['mime'], '/' ) + 1 ) );
		$icfunc = "imagecreatefrom" . $format;
		
		$iresfunc = "image" . $output_format;
		
		if (! function_exists ( $icfunc ))
			return false;
		
		$dst_x = $dst_y = 0;
		$src_x = $src_y = 0;
		$res_p = $width / $height;
		if ($crop && ! $constraint) {
			$dst_w = $width;
			$dst_h = $height;
			if ($ini_p > $res_p) {
				$src_h = $img_info [1];
				$src_w = $img_info [1] * $res_p;
				$src_x = ($img_info [0] >= $src_w) ? floor ( ($img_info [0] - $src_w) / 2 ) : $src_w;
			} else {
				$src_w = $img_info [0];
				$src_h = $img_info [0] / $res_p;
				$src_y = ($img_info [1] >= $src_h) ? floor ( ($img_info [1] - $src_h) / 2 ) : $src_h;
			}
		} else {
			if ($ini_p > $res_p) {
				$dst_w = $width;
				$dst_h = $aspect_ratio ? floor ( $dst_w / $img_info [0] * $img_info [1] ) : $height;
				$dst_y = $aspect_ratio ? floor ( ($height - $dst_h) / 2 ) : 0;
			} else {
				$dst_h = $height;
				$dst_w = $aspect_ratio ? floor ( $dst_h / $img_info [1] * $img_info [0] ) : $width;
				$dst_x = $aspect_ratio ? floor ( ($width - $dst_w) / 2 ) : 0;
			}
			$src_w = $img_info [0];
			$src_h = $img_info [1];
		}
		
		$isrc = $icfunc ( $ini_path );
		$idest = imagecreatetruecolor ( $width, $height );
		if (($format == 'png' || $format == 'gif') && $output_format == $format) {
			imagealphablending ( $idest, false );
			imagesavealpha ( $idest, true );
			imagefill ( $idest, 0, 0, IMG_COLOR_TRANSPARENT );
			imagealphablending ( $isrc, true );
			$quality = 0;
		} else {
			imagefill ( $idest, 0, 0, $rgb );
		}
		imagecopyresampled ( $idest, $isrc, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );
		$res = $iresfunc ( $idest, $dest_path, $quality );
		
		imagedestroy ( $isrc );
		imagedestroy ( $idest );
		
		return $res;
	}
	
	/*
	 * $x_o & $y_o - coordinates, where to start the cutting
	 * $w_o & h_o - height & width for cutting
	 */
	public static function imgCrop($image, $x_o, $y_o, $w_o, $h_o) {
		if (! file_exists ( $image ))
			return false;
		if (($x_o < 0) || ($y_o < 0) || ($w_o < 0) || ($h_o < 0)) {
			// echo "The input data are not correct";
			return false;
		}
		list ( $w_i, $h_i, $type ) = getimagesize ( $image ); // Get the size and type of image
		$types = array (
				"",
				"gif",
				"jpeg",
				"png" 
		); // Array of all possible image types
		$ext = $types [$type]; // When know the type-number, then get the type-name
		if ($ext) {
			$func = 'imagecreatefrom' . $ext; // Create the name of function, depending on the image type
			$img_i = $func ( $image ); // Create the descriptor for source image
		} else {
			// echo 'Wronge image'; // Return error, if image format is not availible
			return false;
		}
		if ($x_o + $w_o > $w_i)
			$w_o = $w_i - $x_o; // IF W-out is bigger than -in (according to x_o), then make it less
		if ($y_o + $h_o > $h_i)
			$h_o = $h_i - $y_o; // IF H-out is bigger than -in (according to y_o), then make it less
		$img_o = imagecreatetruecolor ( $w_o, $h_o ); // Create the descriptor for target image
		imagecopy ( $img_o, $img_i, 0, 0, $x_o, $y_o, $w_o, $h_o ); // Move the part of image from in to out
		$func = 'image' . $ext; // Create the name of function for image save
		return $func ( $img_o, $image ); // Call the function to save the original image after cropping
	}
	
	/*
	 * $length - the length of the returned unid
	 */
	public static function uid($length = 32) {
		$token = "";
		$codeAlphabet = "";
		// $codeAlphabet. = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet .= "0123456789";
		for($i = 0; $i < $length; $i ++) {
			$token .= $codeAlphabet [Ses::randomInteger ( 0, strlen ( $codeAlphabet ) )];
		}
		return $token;
	}
	public static function randomInteger($min, $max) {
		$range = $max - $min;
		if ($range < 0)
			return $min; // not so random...
		$log = log ( $range, 2 );
		$bytes = ( int ) ($log / 8) + 1; // length in bytes
		$bits = ( int ) $log + 1; // length in bits
		$filter = ( int ) (1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec ( bin2hex ( openssl_random_pseudo_bytes ( $bytes ) ) );
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ( $rnd >= $range );
		return $min + $rnd;
	}
	public static function after($this, $inthat) {
		if (! is_bool ( strpos ( $inthat, $this ) ))
			return substr ( $inthat, strpos ( $inthat, $this ) + strlen ( $this ) );
		return $inthat;
	}
	public static function after_last($this, $inthat) {
		if (! is_bool ( Ses::strrevpos ( $inthat, $this ) ))
			return substr ( $inthat, Ses::strrevpos ( $inthat, $this ) + strlen ( $this ) );
		return $inthat;
	}
	public static function before($this, $inthat) {
		if (! is_bool ( strpos ( $inthat, $this ) ))
			return substr ( $inthat, 0, strpos ( $inthat, $this ) );
		return $inthat;
	}
	public static function before_last($this, $inthat) {
		if (! is_bool ( Ses::strrevpos ( $inthat, $this ) ))
			return substr ( $inthat, 0, Ses::strrevpos ( $inthat, $this ) );
		return $inthat;
	}
	
	// TODO: check if it returns not double strings
	public static function between($this, $that, $inthat) {
		return before ( $that, after ( $this, $inthat ) );
	}
	public static function between_last($this, $that, $inthat) {
		return after_last ( $this, before_last ( $that, $inthat ) );
	}
	
	// use strrevpos function in case your php version does not include it
	public static function strrevpos($instr, $needle) {
		$rev_pos = strpos ( strrev ( $instr ), strrev ( $needle ) );
		if ($rev_pos === false)
			return false;
		else
			return strlen ( $instr ) - $rev_pos - strlen ( $needle );
	}
	
	/**
	 * Class casting
	 *
	 * @param string|object $destination        	
	 * @param object $sourceObject        	
	 * @return object
	 */
	public static function cast($destination, $sourceObject) {
		if (is_string ( $destination )) {
			$destination = new $destination ();
		}
		$sourceReflection = new \ReflectionObject ( $sourceObject );
		$destinationReflection = new \ReflectionObject ( $destination );
		$sourceProperties = $sourceReflection->getProperties ();
		foreach ( $sourceProperties as $sourceProperty ) {
			$sourceProperty->setAccessible ( true );
			$name = $sourceProperty->getName ();
			$value = $sourceProperty->getValue ( $sourceObject );
			if ($destinationReflection->hasProperty ( $name )) {
				$propDest = $destinationReflection->getProperty ( $name );
				$propDest->setAccessible ( true );
				$propDest->setValue ( $destination, $value );
			} else {
				$destination->$name = $value;
			}
		}
		return $destination;
	}
	
	/**
	 * Cast an object into a different class.
	 *
	 * Currently this only supports casting DOWN the inheritance chain,
	 * that is, an object may only be cast into a class if that class
	 * is a descendant of the object's current class.
	 *
	 * This is mostly to avoid potentially losing data by casting across
	 * incompatable classes.
	 *
	 * @param string $class
	 *        	The class to cast the object into.
	 * @param object $object
	 *        	The object to cast.
	 * @return object
	 */
	public static function descendant($class, $object) {
		if (! is_object ( $object ))
			throw new \InvalidArgumentException ( '$object must be an object.' );
		if (! is_string ( $class ))
			throw new \InvalidArgumentException ( '$class must be a string.' );
		if (! class_exists ( $class ))
			throw new \InvalidArgumentException ( sprintf ( 'Unknown class: %s.', $class ) );
		if (! is_subclass_of ( $class, get_class ( $object ) ))
			throw new \InvalidArgumentException ( sprintf ( '%s is not a descendant of $object class: %s.', $class, get_class ( $object ) ) );
		/**
		 * This is a beautifully ugly hack.
		 *
		 * First, we serialize our object, which turns it into a string, allowing
		 * us to muck about with it using standard string manipulation methods.
		 *
		 * Then, we use preg_replace to change it's defined type to the class
		 * we're casting it to, and then serialize the string back into an
		 * object.
		 */
		return unserialize ( preg_replace ( '/^O:\d+:"[^"]++"/', 'O:' . strlen ( $class ) . ':"' . $class . '"', serialize ( $object ) ) );
	}
	
	/**
	 *
	 * @param unknown $array        	
	 * @param unknown $key1        	
	 * @param unknown $key2        	
	 * @param unknown $key3        	
	 * @return unknown[]
	 */
	public static function array_uni($array, $k1 = null, $k2 = null, $k3 = null) {
		$temp_array = array ();
		$i = 0;
		$key_array1 = array ();
		
		// if by 3 keys
		if (! is_null ( $k3 )) {
			$key_array3 = array ();
			$key_array2 = array ();
			foreach ( $array as $val ) {
				if (! in_array ( $val [$k1], $key_array1 ) && ! in_array ( $val [$k2], $key_array2 ) && ! in_array ( $val [$k3], $key_array3 )) {
					$key_array1 [$i] = $val [$k1];
					$key_array2 [$i] = $val [$k2];
					$key_array3 [$i] = $val [$k3];
					$temp_array [$i] = $val;
				}
				$i ++;
			}
			return $temp_array;
		}
		// if by 2 keys
		if (! is_null ( $k2 )) {
			$key_array2 = array ();
			foreach ( $array as $val ) {
				if (! in_array ( $val [$k1], $key_array1 ) && ! in_array ( $val [$k2], $key_array2 )) {
					$key_array1 [$i] = $val [$k1];
					$key_array2 [$i] = $val [$k2];
					$temp_array [$i] = $val;
				}
				$i ++;
			}
			return $temp_array;
		}
		// if by 1 keys
		foreach ( $array as $val ) {
			if (! in_array ( $val [$k1], $key_array1 )) {
				$key_array1 [$i] = $val [$k1];
				$temp_array [$i] = $val;
			}
			$i ++;
		}
		return $temp_array;
	}
}