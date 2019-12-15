<?php 

namespace Milkshake\Model;

use Milkshake\Core\Model;

session_start();

class UserModel extends Model {
	
	public function token($length = 256) {
	
		$token = '';
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#&%@.+';
		$charsLen = strlen($chars); 
		
		for ($i = 0; $i < $length; $i++) { 
			$token .= $chars[rand(0, $charsLen - 1)]; 
		} 
	
		return $token;
	
	}
	
	public function format($status, $value = false) {
		
		$data = [];
		$data['status'] = $status;
		
		if($value) {
			$type = ($status) ? 'data' : 'error';
			$data[$type] = $value;
		}
		
		return $data;
	
	}
	
	public function validate($type, $value, $minLength = 3) {
		
		$minLength = (int)$minLength;
		
		switch ($type) {
			
			case "text":
				return (strlen($value) >= $minLength) ? true : false;
				break;
				
			case "int":
				return (is_int($value)) ? true : false;
				break;
				
			case "float":
				return (is_float($value)) ? true : false;
				break;
				
			case "email":
				return (filter_var($value, FILTER_VALIDATE_EMAIL)) ? true : false;
				break;
				
			case "bool":
				return ($value === true || $value === false) ? true : false;
				break;
				
			default:
				die('Something went wrong (User.Validate.Type Error)');
				
		}
	
	}
	
	public function setKey($netkey, $cookie = false) {
		
		if($cookie === true) {
			
			$timespan = time() + 5 * 24 * 60 * 60; // 5 days cookie duration
			setcookie('netkey', $netkey, $timespan, '/');
			
		} else {
			
			$_SESSION['netkey'] = $netkey;
			
		}
		
		return true;
	
	}
	
	public function auth($fetch = false) {
		
		if (!isset($_COOKIE['netkey']) && !isset($_SESSION['netkey'])) { 
			return $this->format(false, 'Du ser ikke ud til at være logget ind længere');
		}
		
		$key = (isset($_COOKIE['netkey'])) ? $_COOKIE['netkey'] : $_SESSION['netkey'];
		$cookie = (isset($_COOKIE['netkey'])) ? true : false;
		
		$sql = "SELECT user, updated FROM pi_sessions WHERE netkey = ?";
		$result = $this->query($sql, [$key]);
		
		if (count($result) !== 1) { 
			return $this->format(false, 'Du ser ikke ud til at være logget ind');
		}
		
		$user = $result[0]['user'];
		$newkey = $this->token();
		
		$sql = "UPDATE pi_sessions SET netkey = ?, updated = NOW() WHERE netkey = ?";
		if (($result = $this->query($sql, [$newkey, $key])) === false) {
			return $this->format(false, 'Hovsa, der skete en fejl (User.Auth.UpdateKey Error)');
		}
		
		/* Set netkey in browser */
		$setKey = $this->setKey($newkey, $cookie);
		
		if ($fetch === true) {
			
			$sql = "SELECT id, email, name, ownership, admin FROM pi_users WHERE id = ? AND deleted = 0";
			if (($result = $this->query($sql, [$user])) === false) {
				return $this->format(false, 'Hovsa, der skete en fejl (User.Auth.Fetch Error)');
			}
			
			return $this->format(true, $result);
			
		} else {
			
			return $this->format(true);
			
		}
		
	}
	
	public function login() {
		
		$email = $_POST["email"];
		$pass = $_POST["pass"];
		$cookie = (isset($_POST["cookie"]));

		/* Validate input */
		if (!$this->validate('email', $email) || !$this->validate('text', $pass)) { 
			return $this->format(false);
		}
		
		/* Get user data */
		$sql = "SELECT id, pass FROM pi_users WHERE email = ? AND deleted = 0";
		$result = $this->query($sql, [$email]);
		
		if (count($result) !== 1) { 
			return $this->format(false, 'En bruger med den angivne email kunne ikke findes');
		}
		
		$stored_id = $result[0]['id'];
		$stored_pass = $result[0]['pass'];
		
		/* Verify password */
		if (!password_verify($pass, $stored_pass)) { 
			return $this->format(false, 'Forkert kodeord');
		}
		
		/* Generate netkey */
		$netkey = $this->token();
		
		/* Insert into sessions */
		$sql = "INSERT INTO pi_sessions (netkey, user, created, updated) VALUES (?, ?, NOW(), NOW())";
		$result = $this->query($sql, [$netkey, $stored_id]);
		
		/* Set netkey in browser */
		$setKey = $this->setKey($netkey, $cookie);
		
		return $this->format(true);
		
	}
	
	public function logout() {
		
		if(isset($_COOKIE['netkey'])) {
			setcookie('netkey', FALSE, -1, '/');
		}
		session_destroy();
		
		return true; 
		
	}
	
	public function update($user) {
        
        $sqlPart = "UPDATE pi_users SET";
        $paramPart = [];

        if ($this->validate('text', $_POST['name'])) {
            $sqlPart .= " name = ?,";
            $paramPart[] = $_POST['name'];
            $success = $this->format(true, $_POST['name']);
        } else {
            $success = $this->format(true);
        }

        if ($this->validate('text', $_POST['old']) && $this->validate('text', $_POST['new']) && $this->validate('text', $_POST['repeat'])) {

            /* If new passwords do not match */
            if ($_POST['new'] !== $_POST['repeat']) { 
                return $this->format(false, 'De nye kodeord er ikke ens'); 
            }

            /* Get user data */
            $sql = "SELECT id, pass FROM pi_users WHERE id = ? AND deleted = 0";
            $result = $this->query($sql, [$user['id']]);
            if (count($result) !== 1) { return $this->format(false, 'Dine oplysninger kunne ikke hentes. Prøv igen senere'); }

            /* Verify password */
            $stored_pass = $result[0]['pass'];
            if (!password_verify($_POST['old'], $stored_pass)) { 
                return $this->format(false, 'Det indtastede kodeord er forkert');
            }
            $pass_crypt = password_hash($_POST['new'], PASSWORD_DEFAULT);

            $sqlPart .= " pass = ?,";
            $paramPart[] = $pass_crypt;
        }

        $paramPart[] = $user['id'];
        $sqlPart .= " updated = NOW() WHERE id = ?";
		
		/* Update user */
		$result = $this->query($sqlPart, $paramPart);
		
		if ($result === false) {
			return $this->format(false, 'Hovsa, noget gik galt under opdateringen af din konto');
		}
		
		return $success;
		
	}
	
	public function create($email, $name, $admin = 0) {
		
		/* Validate input */
		if (!$this->validate('email', $email) || !$this->validate('text', $name) || !$this->validate('int', $admin)) { 
			return false; 
		}
		
		/* Generate temporary password */
		$pass = $this->token(16);
		$pass_crypt = password_hash($pass, PASSWORD_DEFAULT);
		
		/* Check for existing users with same email */
		$sql = "SELECT id FROM pi_users WHERE email = ? AND deleted = 0";
		$result = $this->query($sql, [$email]);
		
		if (!empty($result)) { 
			die('A user with this email already exists'); 
		}
		
		/* Insert new user */
		$sql = "INSERT INTO pi_users (email, pass, name, created, admin) VALUES (?, ?, ?, NOW(), ?)";
		$result = $this->query($sql, [$email, $pass_crypt, $name, $admin]);
		
		if ($result === false) {
			die('Something went wrong (DB Error)'); 
		}
		
		return $pass;
		
	}
	
}

?>