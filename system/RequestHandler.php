<?php



namespace App;



if (!defined('__ROOT__')) die('Access denied');



/**
 * Utility class that reads the state of the HTTP request made to the app.
 */
final class RequestHandler {
	private $_uri;
	private $_uri_parts;
	private $_user_agent;
	private $_referer_uri;



	/**
	 * Create a new instance of the request handler.
	 */
	public function __construct() {
		// setup variables. 
		$this->_uri = $this->serverVar('REQUEST_URI');

		$url_parts = explode('/', $this->uri);
        
		$site_rel_url = parse_url(SITE_URL, PHP_URL_PATH);
		$site_subs = substr_count($site_rel_url, '/');
		if ($site_subs > 0) {
			// basically if site url implies subdirectories, slice off those
			// sub directory parts from the $url_parts, so we dont include
			// them when routing controllers and views.
			$url_parts = array_slice($url_parts, $site_subs);
		}

		$this->_uri_parts = array_merge($url_parts, ['']); // ensure we always have one part.
		$this->_user_agent = $this->serverVar('HTTP_USER_AGENT');
		$this->_referer_uri = $this->serverVar('HTTP_REFERER');
	}



	/**
	 * Magic method for accessing properties in a read-only fashion.
	 */
	public function __get($name) {
		switch ($name) {
			case 'uri': return $this->_uri;
			case 'uri_parts': return $this->_uri_parts;
			case 'uri_parts_count': return count($this->_uri_parts);
			case 'user_agent': return $this->_user_agent;
			case 'referer_uri': return $this->_referer_uri;
		}
		return null;
	}



	/**
	 * Read a $_GET var, specifying default when not available. 
	 */
	public function getVar($name, $default_value = '') {
		return isset($_GET[$name]) ? $_GET[$name] : $default_value;
	}



	/**
	 * Read a $_REQUEST var, specifying default when not available. 
	 */
	public function requestVar($name, $default_value = '') {
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default_value;
	}



	/**
	 * Read a $_POST var, specifying default when not available. 
	 */
	public function postVar($name, $default_value = '') {
		return isset($_POST[$name]) ? $_POST[$name] : $default_value;
	}



	/**
	 * Read a $_SERVER var, specifying default when not available. 
	 */
	public function serverVar($name, $default_value = '') {
		return isset($_SERVER[$name]) ? $_SERVER[$name] : $default_value;
	}
}