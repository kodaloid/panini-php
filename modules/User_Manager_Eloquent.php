<?php



namespace App\Modules;



if (!defined('__ROOT__')) die('Access denied');
if (!class_exists('\Illuminate\Database\Capsule\Manager')) {
	error_log('');
	exit;
}



use \Illuminate\Database\Capsule\Manager as Capsule;



/**
 * If you decide to install Eloquent, this utility class can be used to used
 * to setup basic user management.
 * 
 * Call $users = User_Manager_Eloquent::get_instance() in app/app.php or a controller.
 * Also $user = $users->get_user(32);
 */
class User_Manager_Eloquent {
	/** @var \Illuminate\Database\Capsule\Manager */
	private $_connection;



	/**
	 * Called by the singleton pattern to create a single instance.
	 * @param \Illuminate\Database\Capsule\Manager $connection
	 */
	public function __construct($connection = null) {
		if (is_null($connection)) {
			$class_name = '\Illuminate\Database\Capsule\Manager';
			$connection = call_user_func($class_name . '::connection');
		}

		$this->_connection = $connection;

		// attempt to get the schema builder for the connection.
		$builder = $connection->getSchemaBuilder();

		// create user table if it doesn't already exist.
		if (!$builder->hasTable('users')) {
			$connection->getSchemaBuilder()->create('users', function ($table) {
				$table->increments('id');
				$table->string('email')->unique();
				$table->timestamps();
			});
		}
	}



	/**
	 * Get a user!
	 */
	public function get_user($user_id) {
		$table = $this->_connection->table('users');
		return $table->select()->where('id', '=', $user_id)->first();
	}
}