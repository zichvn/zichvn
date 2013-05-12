<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Permissions controller
 *
 * @author		PyroCMS Dev Team
 * @package 	PyroCMS\Core\Modules\Permissions\Controllers
 */
class Home extends Home_Controller
{

	/**
	 * Constructor method.
	 *
	 * As well as everything in the Admin_Controller::__construct(),
	 * this additionally loads the models and language strings for
	 * permission and group.
	 */
	public function __construct()
	{
	    parent::__construct();
	}

	/**
	 * The main index page in the administration.
	 *
	 * Shows a list of the groups.
	 */
	public function index()
	{
            echo 'ihihi';
	}
}