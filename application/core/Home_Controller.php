<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// Website home portal
class Home_Controller extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->template_type = _TPL_HOME_;
        // Check the frontend hasnt been disabled by an admin
//        if ((Settings::get('frontend_enabled') == 0) && (empty($this->user) OR $this->user->group != 'admin')) {
//            $error = Settings::get('unavailable_message');
//            echo $error;
//            die;
//        }
    }

}
