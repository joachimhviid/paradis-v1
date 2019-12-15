<?php 

namespace Milkshake\Controller;

use Milkshake\Core\Controller;

class PanelController extends Controller {

	public function index() {
		
		$user = $this->model('UserModel');
		
		$auth = $user->auth(true);
		if ($auth['status'] !== true) { header("Location: /admin/login"); }
		$data['account'] = $auth['data'][0];

		return $this->render('panel.common.index', $data);
		
	}
	
	public function accountEdit() {
		
		$user = $this->model('UserModel');
		
		$auth = $user->auth(true);
		if ($auth['status'] !== true) { header("Location: /admin/login"); }
		$data['account'] = $auth['data'][0];
		
		if (isset($_POST['edit-account-submit'])) {
			
			$update = $user->update($data['account']);
			
            if ($update['status'] !== true) {
                $data['updateError'] = (isset($update['error'])) ? $update['error'] : 'Error';
			} else {
                $data['updateSuccess'] = "Kontoen blev opdateret";
                $data['account']['name'] = (isset($update['data'])) ? $update['data'] : $data['account']['name'];
            }
			
		}
			
		return $this->render('panel.account.edit', $data);
		
	}
	
	public function locations() {
		
        $user = $this->model('UserModel');
        $shop = $this->model('ShopModel');
		
		$auth = $user->auth(true);
		if ($auth['status'] !== true) { header("Location: /admin/login"); }
        $data['account'] = $auth['data'][0];
        $shop->setUser($data['account']);
		
		if (isset($_POST['create-location-submit'])) {
            
            $create = $shop->create();
			
			if ($create['status'] !== true) {
				$data['createShopError'] = (isset($create['error'])) ? $create['error'] : 'Error';
			} else {
                $data['createShopSuccess'] = "Butikken blev oprettet";
            }
			
        }

        if (isset($_POST['delete-location-submit'])) {
            
            $delete = $shop->delete();
			
			if ($delete['status'] !== true) {
				$data['deleteShopError'] = (isset($delete['error'])) ? $delete['error'] : 'Error';
			} else {
                $data['deleteShopSuccess'] = "Butikken blev slettet";
            }
			
        }
        
        $get = $shop->get();
        if ($get['status'] !== true) { 
            $data['getShopError'] = $get['error']; 
        } else {
            $data['shops'] = $get['data'];
        }
			
		return $this->render('panel.shop.locations', $data);
		
    }

	public function selection() {
		
        $user = $this->model('UserModel');
        $shop = $this->model('ShopModel');
		
		$auth = $user->auth(true);
		if ($auth['status'] !== true) { header("Location: /admin/login"); }
        $data['account'] = $auth['data'][0];
        $shop->setUser($data['account']);

        if (isset($_POST['create-selection-submit'])) {
            
            $create = $shop->createSelection();
			
			if ($create['status'] !== true) {
				$data['createSelectionError'] = (isset($create['error'])) ? $create['error'] : 'Error';
			} else {
                $data['createSelectionSuccess'] = "Sortimentet blev opdateret";
            }
			
        }

        if (isset($_POST['delete-selection-submit'])) {
            
            $delete = $shop->deleteSelection();
			
			if ($delete['status'] !== true) {
				$data['deleteSelectionError'] = (isset($delete['error'])) ? $delete['error'] : 'Error';
			} else {
                $data['deleteSelectionSuccess'] = "Produktet blev slettet";
            }
			
        }

        $get = $shop->getSelection();
        if ($get['status'] !== true) { 
            $data['getError'] = $get['error']; 
        } else {
            $data['selection'] = $get['data'];
        }
			
		return $this->render('panel.shop.selection', $data);
		
    }
    
	public function location($params) {

        $user = $this->model('UserModel');
        $shop = $this->model('ShopModel');
		
		$auth = $user->auth(true);
		if ($auth['status'] !== true) { header("Location: /admin/login"); }
        $data['account'] = $auth['data'][0];
        $shop->setUser($data['account']);

		if (isset($_POST['edit-location-submit'])) {
			
			$update = $shop->update($params['id']);
			
			if ($update['status'] !== true) {
				$data['updateShopError'] = (isset($update['error'])) ? $update['error'] : 'Error';
			} else {
                $data['updateShopSuccess'] = "Butikken blev opdateret";
            }
			
        }
        
		if (isset($_POST['save-selection-location-submit'])) {
			
			$update = $shop->updateShopSelection($params['id']);
			
			if ($update['status'] !== true) {
				$data['updateShopSelectionError'] = (isset($update['error'])) ? $update['error'] : 'Error';
			} else {
                $data['updateShopSelectionSuccess'] = "Butikkens sortiment blev opdateret";
            }
			
		}
        
        $get = $shop->get($params['id']);
        if ($get['status'] !== true) { 
            $data['getShopError'] = $get['error']; 
        } else {
            $data['shop'] = $get['data'];
        }

        $get = $shop->getSelection();
        if ($get['status'] !== true) { 
            $data['getSelectionError'] = $get['error']; 
        } else {
            $data['selection'] = $get['data'];
        }
			
		return $this->render('panel.shop.shop', $data);
		
    }
    
	public function help() {

        $user = $this->model('UserModel');
        $shop = $this->model('ShopModel');
		
		$auth = $user->auth(true);
		if ($auth['status'] !== true) { header("Location: /admin/login"); }
        $data['account'] = $auth['data'][0];
			
		return $this->render('panel.common.help', $data);
		
	}
	
}

?>