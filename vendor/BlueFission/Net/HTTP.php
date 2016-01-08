<?php
namespace BlueFission\Net;

use BlueFission\DevValue;
use BlueFission\DevString;
use BlueFission\DevNumber;

class HTTP {

	static function query( $formdata, $numeric_prefix = null, $key = null ) 
	{
		if (function_exists('http_build_query') && DevValue::isNull( $key ))
		{
			return http_build_query($formdata, $numeric_prefix);
		}
	     $res = array();
	     foreach ((array)$formdata as $k=>$v) 
	     {
	         $tmp_key = urlencode((is_int($k) && $numeric_prefix) ? $numeric_prefix.$k : $k);
	         if ($key) $tmp_key = $key.'['.$tmp_key.']';
	         if ( is_array($v) || is_object($v) ) 
	         {
	             $res[] = HTTP::query($v, null /* or $numeric_prefix if you want to add numeric_prefix to all indexes in array*/, $tmp_key);
	         } else {
	             $res[] = $tmp_key."=".str_replace('%2F', '/', urlencode($v));
	         }
	         /*
	         If you want, you can write this as one string:
	         $res[] = ( ( is_array($v) || is_object($v) ) ? http_build_query($v, null, $tmp_key) : $tmp_key."=".urlencode($v) );
	         */
	     }
	     $separator = ini_get('arg_separator.output');
	     return implode($separator, $res);
	}

	static function urlExists($url)
	{
		if(stristr($url, "http://")) {
			$url = str_replace("http://", "", $url);
			$fp = @fsockopen($url, 80);
			if($fp === false) return false;
			return true;
		} else {
			return false;
		}
	}

	static function domain( $wholedomain = false ) 
	{
		$domain = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
		if ($domain != '') 
		{
			$domain = (strtolower(substr($domain, 0, 4)) == 'www.' && !$wholedomain ) ? substr($domain, 3) : $domain;
			$port = strpos($domain, ':');
			$domain = ($port) ? substr($domain, 0, $port) : $domain;
		}
		return $domain; 
	}
	
	static function href($href = '', $doc = true) 
	{
		if (DevValue::isNull($href)) {
			if (!defined('PAGE_EXTENSION')) define('PAGE_EXTENSION', '.php');
			$href = '';
			if ($doc === false) 
			{
				$href .= $_SERVER['DOCUMENT_ROOT'];
			} 
			else 
			{
				$href = 'http://' . $_SERVER['SERVER_NAME'];
				$href .= $_SERVER['REQUEST_URI'];
				if (DevString::strrpos($href, PAGE_EXTENSION)) $href = substr($href, 0, DevString::strrpos($href, PAGE_EXTENSION) + strlen(PAGE_EXTENSION));
				elseif (DevString::strrpos($href, '/')) $href = substr($href, 0, DevString::strrpos($href, '/') + strlen('/'));
			}
		}
		
		return $href;
	}

	static function cookie($var, $value = null, $expire = null, $path = null, $secure = false)
	{
		if (DevValue::isNull($value))
			return $_COOKIES[$var];
		
		$domain = ($path) ? substr($path, 0, strpos($path, '/')) : HTTP::domain();
		$dir = ($path) ? substr($path, strpos($path, '/'), strlen($path)) : '/';
		$cookiedie = (DevNumber::isValid($expire)) ? time()+(int)$expire : (int)$expire; //expire in one hour
		$cookiesecure = (bool)$secure;
			
		return setcookie ($var, $value, $cookiedie, $dir, $domain, $cookiesecure);
	}

	static function session($var, $value = null, $expire = null, $path = null, $secure = false)
	{
		if (DevValue::isNull($value) )
			return isset( $_SESSION[$var] ) ? $_SESSION[$var] : null;
			
		if (session_id() == '') 
		{
			$domain = ($path) ? substr($path, 0, strpos($path, '/')) : HTTP::domain();
			$dir = ($path) ? substr($path, strpos($path, '/'), strlen($path)) : '/';
			$cookiedie = (DevNumber::isValid($expire)) ? time()+(int)$expire : (int)$expire; //expire in one hour
			$cookiesecure = (bool)$secure;
			
			session_set_cookie_params($cookiedie, $dir, $domain, $cookiesecure);
			session_start();
		}
			
		$status = ($_SESSION[$var] = $value) ? true : false;
		
		return $status;
	}

	static function jsonEncode($a=false)
	{
		if (function_exists('json_encode'))
		{
			return json_encode($a);
		}
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a))
		{
			if (is_float($a))
			{
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}
			
			if (is_string($a))
			{
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			}
			else
				return $a;
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a))
		{
		  if (key($a) !== $i)
		  {
		    $isList = false;
		    break;
		  }
		}
		$result = array();
		if ($isList)
		{
		  foreach ($a as $v) $result[] = json_encode($v);
		  return '[' . join(',', $result) . ']';
		}
		else
		{
		  foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
		  return '{' . join(',', $result) . '}';
		}
	}

	static function redirect($href = '', $request_r = '', $ssl = '', $snapshot = '') {
		$href = HTTP::href($href);
		$request = ($request_r) ? http_build_query($request_r) : "";
		$href = str_replace('http://', '', $href);
		$href = str_replace('https://', '', $href);
		$href = (($ssl == true) ? 'https' : 'http' ) . "://$href" . (($request != '') ? "?$request" : "");
		if ($snapshot != '') HTTP::cookie('href_snapshot', $snapshot);
		header("Location: $href");
	}
}