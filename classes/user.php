<?php
// If it's going to need the database, then it's
// probably smart to require it before we start.
require_once('database.php');
require_once('password.php');

class User extends Password
{
    protected static $table_name="members";
    protected static $db_fields = array('memberID', 'username', 'password', 'firstname', 'lastname', 'email', 'isadmin','projectfolder');

    public $memberID;
    public $username;
    public $password;
    public $email;
    public $firstname;
    public $lastname;
    public $isadmin;
		public $projectfolder;

    public function __construct()
    {
        parent::__construct();
    }

    public function full_name()
    {
        if (isset($this->first_name) && isset($this->last_name)) {
            return $this->first_name . " " . $this->last_name;
        } else {
            return "";
        }
    }

		public static function getUserFolder() {
			if (isset($_SESSION['user']['folder']))
				return $_SESSION['user']['folder'];
		}
// ODO: add isAdmin method

    // Common Database Methods
    public static function find_user_all()
    {
        return self::find_user_by_sql("SELECT * FROM ".self::$table_name);
    }

    public static function find_user_by_id($id=0)
    {
        $result_array = self::find_user_by_sql("SELECT * FROM ".self::$table_name." WHERE memberID=? LIMIT 1", [$id]);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function getUserID($username)
    {
        $res = self::find_user_by_sql("SELECT memberID FROM ".self::$table_name." WHERE username=?", [$username]);
        $res = array_shift($res);

        return !empty($res) ? ($res->memberID) : false;
    }

    public static function find_user_by_sql($sql="", $params = [])
    {
        global $database;
        $result_set = $database->getAllRows($sql, $params);
        $object_array = array();
        foreach ($result_set as $row) {
            $object_array[] = self::instantiate($row);
        }
        // print_r(($object_array));

    return ($object_array);
    }

    public static function count_all()
    {
        global $database;
        $sql = "SELECT * FROM ".self::$table_name;
        $res = $database->count($sql);

        return $res;
    }

    private static function instantiate($record)
    {
        // Could check that $record exists and is an array
    $object = new self;

        // More dynamic, short-form approach:
        foreach ($record as $attribute=>$value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    private function has_attribute($attribute)
    {
        // We don't care about the value, we just want to know if the key exists
      // Will return true or false
      return array_key_exists($attribute, $this->attributes());
    }

    public function attributes()
    {
        // return an array of attribute names and their values
      $attributes = array();
        foreach (self::$db_fields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    protected function sanitized_attributes()
    {
        global $database;
        $clean_attributes = array();
      // sanitize the values before submitting
      // Note: does not alter the actual value of each attribute
      foreach ($this->attributes() as $key => $value) {
          $clean_attributes[$key] = ($value);
      }
        return $clean_attributes;
    }

    public function save()
    {
        // A new record won't have an id yet.
      return isset($this->memberID) ? $this->update() : $this->create();
    }

    public function create()
    {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - INSERT INTO table (key, key) VALUES ('value', 'value')
        // - single-quotes around all values
        // - escape all values to prevent SQL injection
				$this->projectfolder = FILEREPOSITORY.'/'.$this->username;
        $hashedpassword = $this->password_hash($this->password, PASSWORD_BCRYPT);
        $query = 'INSERT INTO members (firstname,lastname,username,password,email,active,projectfolder) VALUES (:firstname, :lastname, :username, :password, :email, :active, :projectfolder)';
        $params = array(
            ':firstname' => $this->firstname,
            ':lastname' => $this->lastname,
            ':username' => $this->username,
            ':password' => $hashedpassword,
            ':email' => $this->email,
						':projectfolder' => $this->projectfolder,
            ':active' => "Yes"
        );

        if ($database->insertRow($query, $params)) {
            // $this->memberID = $database->lastInsertId();
            if (!file_exists(FILEREPOSITORY."/".$this->username)) {
              mkdir(FILEREPOSITORY."/".$this->username, 0777, true);
              chmod(FILEREPOSITORY."/".$this->username, 0777);
            }
            return true;
        } else {
            return false;
        }
    }

    public function update()
    {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - UPDATE table SET key='value', key='value' WHERE condition
        // - single-quotes around all values
        // - escape all values to prevent SQL injection
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE ".self::$table_name." SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE memberID=?";

        return ($database->updateRow($sql, [$this->memberID])) ? true : false;
    }

    public function delete()
    {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - DELETE FROM table WHERE condition LIMIT 1
        // - escape all values to prevent SQL injection
        // - use LIMIT 1
      $sql = "DELETE FROM ".self::$table_name;
        $sql .= " WHERE memberID= ?";
        $sql .= " LIMIT 1";

        return ($database->updateRow($sql, [$this->memberID])) ? true : false;
        // NB: After deleting, the instance of User still
        // exists, even though the database entry does not.
        // This can be useful, as in:
        //   echo $user->first_name . " was deleted";
        // but, for example, we can't call $user->update()
        // after calling $user->delete().
    }

    private function get_user_hash($username)
    {
        try {
            $query = 'SELECT password, username, memberID, isadmin,projectfolder FROM members WHERE username = :username AND active="Yes" ';
            $params = (array('username' => $username));

            return self::find_user_by_sql($query, $params);
        } catch (PDOException $e) {
            echo '<p class="bg-danger">'.$e->getMessage().'</p>';
        }
    }

    public function login($username, $password)
    {
        $row = array_shift($this->get_user_hash($username));

        if (!$row) {
            return false;
        }

        if ($this->password_verify($password, $row->password) == 1) {
            $_SESSION['user']['loggedin'] = true;
            $_SESSION['user']['username'] = $row->username;
            $_SESSION['user']['memberID'] = $row->memberID;
						$_SESSION['user']['folder'] = $row->projectfolder;
                // $_SESSION['fullname'] = $row->full_name();
            return true;
        }
    }

    public function adminLogin($username, $password)
    {
        $row = array_shift($this->get_user_hash($username));

        if (!$row) {
            return false;
        }

        if ($this->password_verify($password, $row->password) == 1 && isset($row->isadmin)) {
            $_SESSION['admin']['loggedin'] = true;
            $_SESSION['admin']['username'] = $row->username;
            $_SESSION['admin']['memberID'] = $row->memberID;
            $_SESSION['admin']['isadmin'] = $row->isadmin;
                // $_SESSION['fullname'] = $row->full_name();
                return true;
        }
    }
    public function adminLogout()
    {
        unset($_SESSION['admin']);
        // $_SESSION['admin']['loggedin'] = false;
    }

    public function logout()
    {
        unset($_SESSION['user']);
    }

    public function is_logged_in()
    {
        if (isset($_SESSION['user']['loggedin']) && $_SESSION['user']['loggedin'] == true) {
            return true;
        }
    }

    public function is_admin_logged_in()
    {
        if (isset($_SESSION['admin']['loggedin']) && $_SESSION['admin']['loggedin'] == true) {
            return true;
        }
    }
}

$user = new User();
// print_r (($user->getUserID("wuru")));
// //
// // ( $user->login("wuru", "password"));
// // // print_r($user->attributes());
// // // print_r(User::find_user_all());
// $us = new User();//(User::find_user_by_id(2));
// $us->firstname = 'firstname';
// $us->lastname = 'lastname';
// $us->username = 'username';
// $us->password = "password";
// $us->email = 'email@email.com';
// // $us->save();
// echo $us->memberID."\n";
// $us->memberID = 12;
// $us->delete();
// print_r(User::find_user_all());
// // //echo User::count_all()."\n";
