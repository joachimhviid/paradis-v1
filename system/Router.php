<?php 

namespace Milkshake\Core;

class Router {

	private static $routes = [];
	private static $redirect = "PATH";
	
	private function methods($method) {
		
		$methods = [];
		$break = explode(",", $method);
		
		foreach($break as $val) {
			$methods[] = trim(strtoupper($val));
		}
		
		return array_filter($methods);
		
	}
	
	public static function set($method, $request, $action) {
		
		/* Validate method */
		if (!count(array_intersect((new self)->methods($method), ["GET", "POST", "DELETE", "UPDATE", Router::$redirect]))) {
			die('Something went wrong (Route.Method Error)');
		}
		
		/* Validate request route */
		if (strpos($request, '/') === false) {
			die('Something went wrong (Route.Route Error)');
		}
		
		if ($method === Router::$redirect) {
			
			/* Validate redirect target */
			if (strpos($action, '/') === false) {
				die('Something went wrong (Route.Target.Path Error)');
			}
			
		} else {
			
			/* Validate request target */
			if (strpos($action, '.') === false) {
				die('Something went wrong (Route.Target Error)');
			}
			
		}
		
		/* Save route */
		Router::$routes[$request] = $method.".".$action;
		
	}
	
	
	private function cut($action) {
		
		$parts = explode(".", $action);
		
		$result = [];
		$result['method'] = $parts[0];
		
		if ($result['method'] === Router::$redirect) {
			
			$result['target'] = $parts[1];
			
			return $result;
			
		} else {
			
			$result['controller'] = $parts[1];
			$result['function'] = $parts[2];
			
			return $result;

		}
			
	}
	
	
	private function match($request, $routes) {
		
		$matches = 0;
		$match_arr = [];
		
		foreach ($routes as $key => $value) {
			
			/* Exact match for route */
			if($request == $key) {
				
				$target = $this->cut($value);
				
				if($target['method'] === Router::$redirect) {
					
					header('Location: '.$target['target']);
					die;
					
				} else {
					
					/* Validate method */
					if (!in_array($_SERVER['REQUEST_METHOD'], $this->methods($target['method']))) {
						die('Something went wrong (Request.Method Error)');
					}
					
					return $target;
					
				}
				
			}
			
			/* Parameter matching */
			if(preg_match('({|})', $key) && substr_count($key, '/') === substr_count($request, '/')) {
				
				$matches++;
				
				$paramless = preg_replace('/{(.*?)}/', '', $key);
				
				$sim = similar_text($request, $paramless, $percentage);
				$match_arr[$key] = $percentage;
				
			}
		 
		}
		
		/* An array of likely matches exists */
		if($matches > 0) {
			
			/* Get match with highest percentage */
			$highest = array_search(max($match_arr), $match_arr);
			
			/* Target corresponding original route */
			$original = $routes[$highest];
			$target = $this->cut($original);
			
			/* Validate method */
			if (!in_array($_SERVER['REQUEST_METHOD'], $this->methods($target['method']))) {
				die('Something went wrong (Request.Method Error)');
			}
			
			$param__arr = [];
			$parts_request = array_filter(explode("/", $request));
			$parts_route = array_filter(explode("/", $highest));
			
			foreach ($parts_route as $key => $part) {
				
				if(preg_match('({|})', $part)) {
					
					preg_match('/{(.*?)}/', $part, $match);
					$id = $match[1];
					$value = $parts_request[$key];
					
					/* Save value in array with parameter name as key */
					$param_arr[$id] = $value;
					
				}
				
			}
			
			$target['data'] = $param_arr;
			
			return $target;
			
		}
		
		/* No match found for supplied route */
		die('Something went wrong (Unknown Route)');
		
	}
	
	
	public function load($request) {
		
		$request = trim(strtolower($request));
		
		$target = $this->match($request, Router::$routes);
		
		return $target;
		/*return Router::$routes;*/
		
	}
	
}

?>