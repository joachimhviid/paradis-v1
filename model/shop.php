<?php 

namespace Milkshake\Model;

use Milkshake\Core\Model;

class ShopModel extends Model {

    private $user = [];
    private $ownership = [];

	public function setUser($data) {

		$this->user = $data;
		if(isset($data['ownership'])) {
			$ownership = json_decode($data['ownership'], true);
			$this->ownership = (is_array($ownership)) ? $ownership: [];
		}

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
				return (ctype_digit($value)) ? true : false;
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

	public function get($id = false) {

		if($this->validate('int', $id)) {
            $sql = "SELECT id, name, address, email, phone, selection, created, updated FROM pi_locations WHERE id = ? AND deleted = 0 ORDER BY name ASC";
		    $result = $this->query($sql, [$id]);
        } else {
            $sql = "SELECT id, name, address, email, phone, created, updated FROM pi_locations WHERE deleted = 0 ORDER BY name ASC";
		    $result = $this->query($sql);
        }

		if (!$result) {
			return $this->format(false, 'Hovsa, noget gik galt under indlæsningen af butikker'); 
		}
		
        $arr = [];

		if($this->validate('int', $id)) {
			
			$arr = $result[0];
			$arr['selection'] = json_decode($arr['selection'], true);
			if(!is_array($arr['selection'])) {
				$arr['selection'] = array();
			}

        } else {
			
			if($this->user['admin'] !== 1) {
				foreach ($result as $key => $value) {
					if(in_array($value['id'], $this->ownership)) {
						$arr[] = $value;
					}
				}
			} else {
				$arr = $result;
			}	

        }

		return $this->format(true, $arr);

    }

	public function create() {

        if($this->user['admin'] !== 1) {
            return $this->format(false);
        }

		if (!$this->validate('text', $_POST['name']) || !$this->validate('text', $_POST['address']) || !$this->validate('email', $_POST['email'])) { 
			return $this->format(false, 'Indtast gyldigt navn, adresse og email');
        }

        $phone = (strlen($_POST['phone']) >= 8) ? $_POST['phone'] : '';

		$sql = "INSERT INTO pi_locations (name, address, email, phone, created, updated) VALUES (?, ?, ?, ?, NOW(), NOW())";
		$result = $this->query($sql, [$_POST['name'], $_POST['address'], $_POST['email'], $phone]);

		if ($result === false) {
			return $this->format(false, 'Hovsa, noget gik galt under oprettelsen af butikken'); 
		}

		return $this->format(true);

    }

	public function update($id) {

        if (!$this->validate('int', $id)) { 
			return $this->format(false, 'Ugyldigt butiks-ID angivet');
        }

        if($this->user['admin'] !== 1 && !in_array($id, $this->ownership)) {
            return $this->format(false, 'Du har ikke adgang til at redigere denne butik');
        }

		if (!$this->validate('text', $_POST['name']) || !$this->validate('text', $_POST['address']) || !$this->validate('email', $_POST['email'])) { 
			return $this->format(false, 'Indtast gyldigt navn, adresse og email');
        }

        $phone = (strlen($_POST['phone']) >= 8) ? $_POST['phone'] : '';

        $sql = "UPDATE pi_locations SET name = ?, address = ?, email = ?, phone = ?, updated = NOW() WHERE id = ?";
		$result = $this->query($sql, [$_POST['name'], $_POST['address'], $_POST['email'], $phone, $id]);

		if ($result === false) {
			return $this->format(false, 'Hovsa, noget gik galt under opdateringen af butikken'); 
		}

		return $this->format(true);

	}
	
	public function updateShopSelection($id) {
		
        if (!$this->validate('int', $id)) { 
			return $this->format(false, 'Ugyldigt butiks-ID angivet');
        }

        if($this->user['admin'] !== 1 && !in_array($id, $this->ownership)) {
            return $this->format(false, 'Du har ikke adgang til at redigere denne butik');
        }

		if (isset($_POST['selection'])) {
			$selection = $_POST['selection'];
		} else { 
			$selection = [];
		}

        $sql = "UPDATE pi_locations SET selection = ?, updated = NOW() WHERE id = ?";
		$result = $this->query($sql, [json_encode($selection, JSON_FORCE_OBJECT), $id]);

		if ($result === false) {
			return $this->format(false, 'Hovsa, noget gik galt under opdateringen af butikkens sortiment'); 
		}

		return $this->format(true);

	}

	public function delete() {

        if (!$this->validate('int', $_POST['id'])) { 
			return $this->format(false, 'Ugyldigt butiks-ID angivet');
        }

        $sql = "UPDATE pi_locations SET deleted = 1, updated = NOW() WHERE id = ?";
		$result = $this->query($sql, [$_POST['id']]);

		if ($result === false) {
			return $this->format(false, 'Hovsa, noget gik galt under opdateringen af butikken'); 
		}

		return $this->format(true);

    }

	public function createSelection($allowed = ['image/png']) {

		if (!$this->validate('int', $_POST['category']) || !$this->validate('text', $_POST['name']) || !$this->validate('text', $_POST['teaser'])) { 
			return $this->format(false, 'Vælg gyldig kategori, navn og beskrivelse');
        }

        $file = $_FILES['image'];

        $tmpName = $file['tmp_name'];
        $name = $file['name'];
        $sizeMB = number_format($file['size'] / 1048576, 2);
        $error = $file['error'];

        if ($sizeMB > 10) {
            return $this->format(false, 'Billedet må max fylde 10 MB');
        }

        if($error !== UPLOAD_ERR_OK){
            return $this->format(false, 'Billedet kunne ikke uploades');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpName);

        if(!in_array($mime, $allowed)){
            return $this->format(false, 'Billedet skal være af filtypen PNG');
        }

        $pictureName = time(). rand(0,999999999) . $name;
        $pictureName = htmlspecialchars($pictureName, ENT_QUOTES, 'UTF-8');

        $path = 'uploads/'.$pictureName;

        move_uploaded_file($tmpName, 'assets/uploads/'.$pictureName);

		$sql = "INSERT INTO pi_selection (name, category, description, image, created, updated) VALUES (?, ?, ?, ?, NOW(), NOW())";
		$result = $this->query($sql, [$_POST['name'], $_POST['category'], $_POST['teaser'], $path]);

		if ($result === false) {
			return $this->format(false, 'Hovsa, noget gik galt under oprettelsen af varianten'); 
		}

		return $this->format(true);

	}
	
	public function deleteSelection() {

        if (!$this->validate('int', $_POST['id'])) { 
			return $this->format(false, 'Ugyldigt butiks-ID angivet');
        }

        $sql = "UPDATE pi_selection SET deleted = 1, updated = NOW() WHERE id = ?";
		$result = $this->query($sql, [$_POST['id']]);

		if ($result === false) {
			return $this->format(false, 'Hovsa, noget gik galt under fjernelsen af produktet'); 
		}

		return $this->format(true);

    }

	public function getSelection() {

        $sql = "SELECT id, name, category, description, image, created, updated FROM pi_selection WHERE deleted = 0 ORDER BY category ASC, name ASC";
		$result = $this->query($sql);
		
		if ($result === false) {
			return $this->format(false, 'Hovsa, noget gik galt under indlæsningen af sortimentet'); 
        }

		return $this->format(true, $result);

	}

}

?>