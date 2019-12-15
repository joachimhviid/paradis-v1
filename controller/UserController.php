<?php 

namespace Milkshake\Controller;

use Milkshake\Core\Controller;

class UserController extends Controller {

	public function login() {
		
		/* Get model */
		$model = $this->model('UserModel');
		
		if (isset($_POST['login-submit'])) {
			
			/* Perform login */
			$login = $model->login();
				
			if ($login['status'] !== true) {
				$data['loginError'] = (isset($login['error'])) ? $login['error'] : 'Fejl';
			} else {
				header("Location: /admin");
			}

			return $this->render('user.login', $data);
				
		} else {
			
			/* Display login page */
			return $this->render('user.login');
			
		}
		
	}
	
	// public function create() {
		
	// 	$model = $this->model('UserModel');
		
	// 	$data = $model->create('sven@swinther.com', 'Sven Bachmann', 1);
		
	// 	return $data;
		
	// }
	
	public function logout() {
		
		$model = $this->model('UserModel');
		
		$data = $model->logout();
		
		header("Location: /admin/login");
		
	}
	
}

?>