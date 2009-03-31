<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for working with text.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die(_("Access Denied."));
class TextHelper { 
	
	/** 
	 * Takes text and returns it in the "lowercase_and_underscored_with_no_punctuation" format
	 * @param string $handle
	 * @return string
	 */
	function sanitizeFileSystem($handle, $leaveSlashes=false) {
		$handle = trim($handle);
		$searchNormal = array("/[&]/", "/[\s|.]+/", "/[^0-9A-Za-z-_]/", "/--/");
		$searchSlashes = array("/[&]/", "/[\s|.]+/", "/[^0-9A-Za-z-_\/]/", "/--/");
		$replace = array("and", "_", "", "_");
		
		$search = $searchNormal;
		if ($leaveSlashes) {
			$search = $searchSlashes;
		}

		$handle = preg_replace($search, $replace, $handle);
		if (function_exists('mb_substr')) {
			$handle = mb_strtolower(mb_substr($handle, 0, 48, APP_CHARSET), APP_CHARSET);
		} else {
			$handle = strtolower(substr($handle, 0, 48));
		}
		return $handle;
	}

	/** 
	 * Strips tags and optionally reduces string to specified length.
	 * @param string $string
	 * @param int $maxlength
	 * @return string
	 */
	function sanitize($string, $maxlength = 0) {
		$text = trim(strip_tags($string));
		if ($maxlength > 0) {
			if (function_exists('mb_substr')) {
				$text = mb_substr($text, 0, $maxlength, APP_CHARSET);
			} else {
				$text = substr($text, 0, $maxlength);
			}
		}
		if ($text == null) {
			return ""; // we need to explicitly return a string otherwise some DB functions might insert this as a ZERO.
		}
		return $text;
	}

	 
	/**
	 * Like sanitize, but requiring a certain number characters, and assuming a tail
	 * @param string $textStr
	 * @param int $numChars
	 * @param string $tail
	 */
	public function shorten($textStr, $numChars = 255, $tail = '...') {
		return $this->shortText($textStr, $numChars, $tail);
	}
	
	/** 
	 * An alias for shorten()
	 */	
	function shortText($textStr, $numChars=255, $tail='...') {
		if (intval($numChars)==0) $numChars=255;
		$textStr=strip_tags($textStr);
		if (function_exists('mb_substr')) {
			if (mb_strlen($textStr, APP_CHARSET) > $numChars) { 
				$textStr = mb_substr($textStr, 0, $numChars, APP_CHARSET) . $tail;
			}
		} else {
			if (strlen($textStr) > $numChars) { 
				$textStr = substr($textStr, 0, $numChars) . $tail;
			}
		}
		return $textStr;				
	}
	
	/**
	 * Takes a string and turns it into the CamelCase or StudlyCaps version
	 * @param string $string
	 * @return string
	 */
	public function camelcase($string) {
		return Object::camelcase($string);
	}
	
	/** 
	 * Scans passed text and automatically hyperlinks any URL inside it
	 * @param string $input
	 * @return string $output
	 */
	public function autolink($input) {
		$output = preg_replace("/(http:\/\/|https:\/\/|(www\.))(([^\s<]{4,80})[^\s<]*)/", '<a href="http://$2$3" rel="nofollow">http://$2$4</a>', $input);
		return ($output);
	}
	
	/**
	 * Runs a number of text functions, including autolink, nl2br, strip_tags. Assumes that you want simple
	 * text comments but witih a few niceties.
	 * @param string $input
	 * @return string $output
	 */
	public function makenice($input) {
		$output = strip_tags($input);
		$output = $this->autolink($output);
		$output = nl2br($output);
		return $output;
	}
	
	/** 
	 * Takes a CamelCase string and turns it into camel_case
	 */
	public function uncamelcase($string) {
		$v = preg_split('/([A-Z])/', $string, false, PREG_SPLIT_DELIM_CAPTURE);
		$a = array();
		array_shift($v);
		for($i = 0; $i < count($v); $i++) {
			if ($i % 2) {
				if (function_exists('mb_strtolower')) {
					$a[] = mb_strtolower($v[$i - 1] . $v[$i], APP_CHARSET);
				} else {
					$a[] = strtolower($v[$i - 1] . $v[$i]);
				}
			}
		}
		return implode('_', $a);
	}
	
	/**
	 * Takes a handle-based string like "blah_blah" and turns it into "Blah Blah"
	 * @param string $string
	 * @return string
	 */
	public function unhandle($string) {
		// takes something like collection_types and turns it into "Collection Types"
		$r1 = ucwords(str_replace(array('_', '/'), ' ', $string));
		return $r1;
	}

	/**
	 * Strips out non-alpha-numeric characters
	 * @param string $val
	 * @return string
	 */
	public function filterNonAlphaNum($val){ return preg_replace('/[^[:alnum:]]/', '', $val);  }
	
	/** 
	 * Useful for highlighting search strings within results (for nice display)
	 */
	 
	public function highlightSearch($value, $searchString) {
		return str_ireplace($searchString, '<em class="ccm-highlight-search">' . $searchString . '</em>', $value);
	}
}

?>
