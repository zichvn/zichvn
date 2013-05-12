<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Code here is run before ALL controllers
 * 
 * @package 	ZICH\Core\Controllers
 * @author      Zunio Team Leader
 * @copyright   Copyright (c) 2013, ZICH.VN
 */

define('_TPL_HOME_', 1);
define('_TPL_ADMIN_', 2);
define('_TPL_DEMO_', 3);
define('_TPL_SHOP_', 4);
define('_TPL_SHOPADMIN_', 5);
define('_TPL_TA_', 6);
define('_TPL_TOOL_', 7);

class MY_Controller extends CI_Controller
{
        var $prefix_table = '';
        var $suffix_mobile = '';
        var $current_user = false;
        
	public function __construct()
	{
		parent::__construct();
//                $this->load->library('user_agent');
//                if ($this->agent->is_mobile())
//                    $this->suffix_mobile = '_mb';
                
                $this->load->library('users/ion_auth');
		$this->load->library('form_validation');
                if ($this->ion_auth->logged_in()) {
                    $this->current_user = $this->ion_auth->user()->row();
                }
                        
//                print_r($this->current_user);die;
		// Get user data
//		$this->template->current_user = ci()->current_user = $this->current_user = $this->ion_auth->get_user();

		// Work out module, controller and method and make them accessable throught the CI instance
//		ci()->module = $this->module = $this->router->fetch_module();
//		ci()->controller = $this->controller = $this->router->fetch_class();
//		ci()->method = $this->method = $this->router->fetch_method();
//
//		// Loaded after $this->current_user is set so that data can be used everywhere
		$this->load->model('permissions/permission_m');
                $this->permissions = $this->current_user ? $this->permission_m->get_group($this->current_user->group_id) : array();
              print_r($this->permissions);die;
//		// List available module permissions for this user
//		ci()->permissions = $this->permissions = $this->current_user ? $this->permission_m->get_group($this->current_user->group_id) : array();
//
//		// load all modules (the Events library uses them all) and make their details widely available
//		ci()->enabled_modules = $this->module_m->get_all();
//
//		// now that we have a list of enabled modules
//		$this->load->library('events');
//
//		// set defaults
//		$this->template->module_details = ci()->module_details = $this->module_details = false;
//
//		// now pick our current module out of the enabled modules array
//		foreach (ci()->enabled_modules as $module)
//		{
//			if ($module['slug'] === $this->module)
//			{
//				// Set meta data for the module to be accessible system wide
//				$this->template->module_details = ci()->module_details = $this->module_details = $module;
//
//				continue;
//			}
//		}
//
//		// certain places (such as the Dashboard) we aren't running a module, provide defaults
//		if ( ! $this->module)
//		{
//			$this->module_details = array(
//				'name' => null,
//				'slug' => null,
//				'version' => null,
//				'description' => null,
//				'skip_xss' => null,
//				'is_frontend' => null,
//				'is_backend' => null,
//				'menu' => false,
//				'enabled' => 1,
//				'sections' => array(),
//				'shortcuts' => array(),
//				'is_core' => null,
//				'is_current' => null,
//				'current_version' => null,
//				'updated_on' => null
//			);
//		}
//
//		// If the module is disabled, then show a 404.
//		empty($this->module_details['enabled']) AND show_404();
//
//		if ( ! $this->module_details['skip_xss'])
//		{
//			$_POST = $this->security->xss_clean($_POST);
//		}
//
//		if ($this->module and isset($this->module_details['path']))
//		{
//			Asset::add_path('module', $this->module_details['path'].'/');
//		}
		
		
		// Enable profiler on local box
	}
}

/**
 * Returns the CodeIgniter object.
 *
 * Example: ci()->db->get('table');
 *
 * @return \CI_Controller
 */
function ci()
{
	return get_instance();
}
