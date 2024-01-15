<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//$route['default_controller'] = "welcome";
$route['default_controller'] = "home";
$route['404_override'] = '';

//route for customer payment gateway redirection
$route['paymentgatewayredirect/(:any)'] = 'customer/payment_redirection/$1';

//route after redirection from payment gateway (return url)
$route['paymentsuccess/(:any)'] = 'customer/payment_success/$1';

//route for short url sent to customer
$route['customerotpform/(:any)'] = 'customer/customerotpform/$1';

$route['customerdetails/(:any)'] = 'customer/customerpaymentformdetails/$1';

$route['ghdverified/(:any)'] = 'customer/ghdverificationresponse/$1';

$route['generate_quote/(:any)'] = 'generate_quotes_abc/generate_quotes_home_abc/$1';
$route['SendFTPRequest'] = 'GadgetInsurance/SendFTPRequest';
$route['getCoi'] = 'GadgetInsurance/getCoi';


/* End of file routes.php */
/* Location: ./application/config/routes.php */
