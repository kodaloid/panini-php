<?php



namespace App;



if (!defined('__ROOT__')) die('Access denied');



/**
 * The primary class for the engine. Implements a singleton pattern so
 * that the global state/registry can be accessed from anywhere.
 */
final class App {
	// Hold the class instance, and initialized state.
	private static $_instance = null;
	private static $_initialized = false;
	// instance variables.
	private $_data;
	private $_modules;
	private $_request;
	private $_twig_loader;
	private $_twig;



	/**
	 * Called by the singleton pattern to create a single instance.
	 */
	private function __construct() { }



	/**
	 * Magic method for accessing properties in a read-only fashion.
	 */
	public function __get($name) {
		switch ($name) {
			case 'data': return $this->_data;
			case 'modules': return $this->_modules;
			case 'request': return $this->_request;
		}
		return null;
	}



	/**
	 * Get the one instance of PaniniApp.
	 */
	public static function get_instance() {
		return (self::$_instance == null)
			 ? self::$_instance = new App()
			 : self::$_instance;
	}



	/**
	 * Add something to the global data array.
	 * @param string $name The name of the data to add or update.
	 * @param mixed $data The data to add.
	 */
	public function set_data($name, $data) {
		$this->_data[$name] = $data;
		return $data;
	}



	/**
	 * Initialize the app engine.
	 * @param string $templates_path The path where the twig templates reside.
	 */
	public function initialize($templates_path) {
		if (!self::$_initialized) {
			self::$_initialized = true;

			// init the basics.
			$this->_data = array();
			$this->_modules = new \stdClass;
			$this->_request = new RequestHandler;

			// init the event and twig engines.
			$this->_twig_loader = new \Twig\Loader\FilesystemLoader($templates_path);
			$this->_twig = new \Twig\Environment($this->_twig_loader);
			$this->initialize_twig();
		}
	}



	/**
	 * Used during initialize() to load in any extra filters and functions
	 * before Twig is used.
	 */
	private function initialize_twig() {
		$this->_twig->addFilter(new \Twig\TwigFilter('url', function ($string) {
			return Helper::absolute_url($string);
		}));
	}



	/**
	 * Locate and execute a controller bsed on the request uri.
	 */
	public function run() {
		// allow the web folder to exec code before controller is discovered.
		require_once __APP__ . '/app.php';

		// find the route.
		$routes = $this->predict_path(__APP__ . '/controllers', '.php');
		$this->_data['controller_routes'] = $routes;

		// require the last route we added, which is now at index 0.
		require_once reset($routes);
	}


	
	/**
	 * Redirect to another URL.
	 */
	public function redirect($url) {
		if (!headers_sent()) {
			header("Location: $url");
			exit;
	  	}
		
		echo "Cannot redirect, for now please click this <a href=\"{$url}\">link</a> instead\n";
    	exit;
	}



	/**
	 * Locate and execute a controller bsed on the request uri.
	 * @param string $prefix The path prefix.
	 * @param string $default_key The default name/key for a file to locate.
	 * @param bool $force_index When true returns index if no results are found.
	 */
	public function predict_path($prefix, $extension) {
		$routes = [ ];
		$pcount = $this->request->uri_parts_count;
		$max    = $pcount < MAX_ROOT_DEPTH ? $pcount : MAX_ROOT_DEPTH;

		// check for controller matches upto a depth of MAX_ROOT_DEPTH
		for ($i=$max; $i>=1; $i--) {
			// Concatenate a number of pieces from the uri into a path string.
			$bits = Helper::implode_path('/', $this->request->uri_parts, $i);
			$path = "{$prefix}/{$bits}{$extension}";
			// If it exists, add it to the route queue.
			if (!in_array($path, $routes) && file_exists($path)  && !empty($path)) {
				$routes[] = $path;
			}
		}

		// handle missing route arguments.
		if (empty($routes)) {
			array_unshift($routes, "{$prefix}/index{$extension}");
		}

		// require the last route we added, which is now at index 0.
		return array_reverse($routes);
	}



	/**
	 * Load a specific module from the /modules folder.
	 * @param string $alias The key used to access the module once loaded.
	 * @param string $module The module filename (without path).
	 * @param array $args A list of arguments to pass to the constructor.
	 * @return bool True if loaded, false if module is missing.
	 */
	public function load_module($alias, $module, $args) {
		// build the path to the requested module.
		$path = __ROOT__ . "/modules/{$module}.php";
		$instance = null;

		// if exists, require it.
		if (file_exists($path)) {
			require_once($path);
			$class_name = "App\\Modules\\{$module}";
			if (class_exists($class_name)) {
				$class = new \ReflectionClass($class_name);
				$instance = $class->newInstanceArgs($args);
				$this->_modules->$alias = $instance;
				return true;
			}
			else {
				error_log("Module {$module} is missing correct class name.");
			}
		}

		// return the instance.
		return false;
	}


	/**
	 * Check to see if a module has been loaded.
	 */
	public function has_module($alias) {
		return isset($this->_modules->$alias);
	}


	/**
	 * Finds the location of a view file, passing null attempts to predict the path.
	 */
	public function locate_view($view = null) {
		if (is_null($view)) {
			$views = $this->predict_path(__APP__ . '/views', '.twig');
			if (!empty($views)) {
				return reset($views);
			}
		}
		return $view;
	}


	/**
	 * Called by a controller to render a twig template as output.
	 * @param string $view Path to the template, leave empty for auto-detect.
	 * @param array $data The data variables to pass to the twig view.
	 */
	public function present_view($view = null, $data = []) {
		$view = $this->locate_view($view);
		$this->_data['view'] = $view;
		echo $this->_twig->render(basename($view), $data);
		exit;
	}



	/**
	 * Called by a controller to render a JSend JSON output.
	 * @param string $status Either success, fail, or error.
	 * @param mixed $data The data or error message you wish to pass.
	 */
	public function present_json($status, $data = null) {
		if ($status == 'error') {
			$output = array('status' => $status, 'message' => $data);
		}
		else {
			$output = array('status' => $status, 'data' => $data);
		}

		header('Content-Type: application/json');
		echo json_encode($output);
		exit;
	}
}