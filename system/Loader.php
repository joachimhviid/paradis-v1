<?php 

namespace Milkshake\Core; 

class Loader {
	
	private static $locations = [
		"system", 
		"model", 
		"controller", 
	];
	
	private static function autoload($dir) {
		
		foreach(glob($dir."/*") as $item) {
			
			$isPHP = strtolower(substr($item, -4)) === ".php";

			if (is_file($item) && $isPHP) {
				
				require_once $item;
				
			} elseif (is_dir($item)) {
				
				Loader::autoload($item);
				
			}
			
		}
		
	}
	
	public static function init() {
		
		foreach (Loader::$locations as $dir) {
		
			Loader::autoload($dir);
			
		}
		
		require_once 'routes.php';
		require_once 'config.php';
		
	}

}

?>