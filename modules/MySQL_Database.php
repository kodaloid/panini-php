<?php



namespace App\Modules;



if (!defined('__ROOT__')) die('Access denied');



/**
 * Offers basic MySQL database functionality if you don't wish to use Composer.
 * 
 * Call $db = MySQL_Database::get_instance() in app/app.php or a controller.
 * Also $db->connect(..); before anything else.
 * Then $result = $db->select('SELECT * FROM users');
 * Also $db->insert('users', ['email' => 'test@test.com']);
 * Also $db->delete('users', 'id', 3);
 */
class MySQL_Database {
    /** @var \mysqli The connection. */
    private $_connection;



    /**
     * Called by the singleton pattern to create a single instance.
     */
    function __construct($host, $database, $user, $pass, $port = 3306) {
		$this->_connection = new \mysqli($host, $user, $pass, $database, $port);
    }



    /**
     * Make a select query that returns either an array or null on failure.
     * @param string $query The MySQL query to execute, ? for data placeholder.
     * @param array $data An array of data for arguments.
     * @param bool $assoc True returns arrays, false returns objects.
     */
    function select($query, $data = [], $assoc = true) {
        $format = [];
        foreach ($data as $key => $value) {
            $format[] = is_int($value) ? '%d' : '%s';
        }

        // Prepare our query for binding
		$stmt = $this->_connection->prepare($query);
		
        // Only bind if we have something to bind
        if (count($data) > 0) {
            //Normalize format
		    $format = implode('', $format); 
            $format = str_replace('%', '', $format);
        
            // Prepend $format onto $values
            array_unshift($data, $format);
            
            //Dynamically bind values
            call_user_func_array(array($stmt, 'bind_param'), self::ref_vals($data));
        }
		
		//Execute the query
		$stmt->execute();
		
		//Fetch results
		$result = $stmt->get_result();
		$results = [];
		
		//Create results object
		while ($row = $result->fetch_assoc()) {
			$results[] = $assoc ? $row : (object)$row;
        }
		return $results;
    }
    

    
    /**
     * Make a select query that returns a row on success.
     * @param string $query The MySQL query to execute, ? for data placeholder.
     * @param array $data An array of data for arguments.
     * @param bool $assoc True returns arrays, false returns objects.
     */
    function select_row($query, $data, $assoc = true) {
		$rows = $this->select($query, $data, $assoc);
		if (is_array($rows) && count($rows) > 0) {
			return $rows[0];
		}
		return null;
	}



    /**
     * Make a select query that returns a single scalar value.
     * @param string $query The MySQL query to execute, ? for data placeholder.
     * @param array $data An array of data for arguments.
     * @param bool $assoc True returns arrays, false returns objects.
     */
	function select_scalar($query, $data, $assoc = true) {
		$row = $this->select_row($query, $data, $assoc);
		if (is_array($row) && count($row) > 0) {
			return reset($row);
		}
		return null;
    }
    


    /**
     * Insert new data into the database.
     * @param string $table The table name.
     * @param array $data An array of data for arguments.
     */
    function insert($table, $data) {
		// Check for $table or $data not set
		if ( empty( $table ) || empty( $data ) ) {
			return false;
        }
        
        // Generate format codes.
        $format = [];
        foreach ($data as $key => $value) {
            $format[] = is_int($value) ? '%d' : '%s';
        }
			
		// Connect to the database
		$db = $this->_connection;
			
	    // Cast $data and $format to arrays
		$data = (array) $data;
		$format = (array) $format;
			
		// Build format string
		$format = implode('', $format); 
		$format = str_replace('%', '', $format);
			
		list($fields, $placeholders, $values) = $this->prep_query($data);
		
		// Prepend $format onto $values
		array_unshift($values, $format);
		
		// Prepary our query for binding
		$query = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
		$stmt = $db->prepare($query);
		
		if ($stmt == null) {
			die('prepare() failed: ' . htmlspecialchars($db->error));
		}

		// Dynamically bind values
		call_user_func_array(array($stmt, 'bind_param'), self::ref_vals($values));
			
		// Execute the query
		$stmt->execute();
			
		// Check for successful insertion
		if ($stmt->affected_rows) {
			return mysqli_insert_id($db);
		}			
		return false;
	}
    
    
    
    /**
     * Perform a database update query.
     * @param string $table The table name.
     * @param array $data An array of data for arguments.
     * @param array $where Keyed array specifying update conditions.
     */
    function update($table, $data, $where) {
		// Check for $table or $data not set
		if ( empty( $table ) || empty( $data ) ) {
			return false;
        }
        
        // Generate format codes.
        $format = [];
        foreach ($data as $key => $value) {
            $format[] = is_int($value) ? '%d' : '%s';
        }
        $where_format = [];
        foreach ($where as $key => $value) {
            $where_format[] = is_int($value) ? '%d' : '%s';
        }
			
		// Connect to the database
		$db = $this->connection;
			
		// Cast $data and $format to arrays
		$data = (array) $data;
		$format = (array) $format;
			
		// Build format array
		$format = implode('', $format); 
		$format = str_replace('%', '', $format);
		$where_format = implode('', $where_format); 
		$where_format = str_replace('%', '', $where_format);
		$format .= $where_format;
			
		list($f, $placeholders, $values) = self::prep_query($data, 'update');
			
		//Format where clause
		$where_clause = '';
		$where_values = [];
		$count = 0;
			
		foreach ( $where as $field => $value ) {
			if ( $count > 0 ) {
				$where_clause .= ' AND ';
			}
			$where_clause .= $field . '=?';
			$where_values[] = $value;
			$count++;
		}

		// Prepend $format onto $values
		array_unshift($values, $format);
		$values = array_merge($values, $where_values);
		// Prepary our query for binding
		$query = "UPDATE {$table} SET {$placeholders} WHERE {$where_clause}";
		$stmt = $db->prepare($query);
	
		// Dynamically bind values
		call_user_func_array(array($stmt, 'bind_param'), self::ref_vals($values));
			
		// Execute the query
		$stmt->execute();
			
		// Check for successful insertion
		return $stmt->affected_rows > 0;
	}



    /**
     * Delete something from the database.
     * @param string $table The table name.
     * @param string $col_name The name of the ID column.
     * @param int $id The id to find.
     */
    function delete($table, $col_name = 'ID', $id = -1) {
		// Connect to the database
		$db = $this->_connection;
		// Prepary our query for binding
		$stmt = $db->prepare("DELETE FROM {$table} WHERE {$col_name} = ?");
		// Dynamically bind values
		$stmt->bind_param('d', $id);
		// Execute the query
		$stmt->execute();		
		// Check for successful insertion
		return $stmt->affected_rows;
    }
    

    
    /**
     * Delete something from the database.
     * @param string $table The table name.
     * @param string $col_name The name of the ID column.
     * @param int $id The id to find.
     */
    function truncate($table) {
		// Prepary our query.
		$stmt =$this->_connection->prepare("TRUNCATE {$table}");
		// Execute the query.
		$stmt->execute();		
		// Check for successful truncation.
		return $stmt->affected_rows;
	}

    

    /**
     * Helper function for building queries for inserting and updating.
     */
    static function prep_query($data, $type='insert') {
		// Instantiate $fields and $placeholders for looping
		$fields = '';
		$placeholders = '';
		$values = array();
			
		// Loop through $data and build $fields, $placeholders, and $values			
		foreach ( $data as $field => $value ) {
			$fields       .= "{$field},";
			$values[]      = $value;
            $placeholders .= ($type == 'update') ? "{$field}=?" : '?,';
		}
			
		// Normalize $fields and $placeholders for inserting
		$fields = substr($fields, 0, -1);
		$placeholders = substr($placeholders, 0, -1);
			
		return array( $fields, $placeholders, $values );
	}

 

    /**
     * Converts a keyed array into a keyed array with refrences for values.
     * This is used to build prepared statements, hence the private & static!
     */
    private static function ref_vals($array) {
        $refs = array();
		foreach (array_keys($array) as $key) {
			$refs[$key] = &$array[$key]; 
		}
		return $refs;
	}
}