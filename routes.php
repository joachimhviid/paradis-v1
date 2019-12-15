<?php 

use Milkshake\Core\Router;

/** 
*
* Milkshake Router
* 
* Use the following format to create regular routes:
* Router::set(Method, Path, Controller.Method);
*
* For multiple methods per route, seperate methods with comma.
* 
* Use the following format to create redirect routes:
* Router::set('PATH', From, To);
*
**/

Router::set('GET', '/', 'MainController.index');
Router::set('PATH', '/minparadis', '/minparadis/butikker');
Router::set('GET', '/minparadis/butikker', 'MainController.configuratorShops');
Router::set('GET', '/minparadis/konfigurator/{id}', 'MainController.configuratorIcecream');

Router::set('GET,POST', '/admin/login', 'UserController.login');
Router::set('GET', '/admin/logout', 'UserController.logout');

Router::set('GET', '/admin', 'PanelController.index');
Router::set('GET,POST', '/admin/account/edit', 'PanelController.accountEdit');
Router::set('GET,POST', '/admin/locations', 'PanelController.locations');
Router::set('GET,POST', '/admin/locations/{id}', 'PanelController.location');
Router::set('GET,POST', '/admin/selection', 'PanelController.selection');
Router::set('GET,POST', '/admin/help', 'PanelController.help');

?>