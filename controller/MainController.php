<?php 

namespace Milkshake\Controller;

use Milkshake\Core\Controller;

class MainController extends Controller {

	public function index() {

		return $this->render('main.index');
		
    }
    
	public function configuratorShops() {

        $shop = $this->model('ShopModel');
        $admin['admin'] = 1;
        $shop->setUser($admin);

        $get = $shop->get();
        if ($get['status'] !== true) { 
            $data['getShopError'] = $get['error']; 
        } else {
            $data['shops'] = $get['data'];
        }

		return $this->render('main.findbutik', $data);
		
    }
    
	public function configuratorIcecream($params) {

        $shop = $this->model('ShopModel');
        $admin['admin'] = 1;
        $shop->setUser($admin);

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

		return $this->render('main.isconfig', $data);
		
	}
	
}

?>