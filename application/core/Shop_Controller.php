<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Shop_Controller extends MY_Controller {

    public $page_slug = FALSE;
    public $page_type = 1;
    public $hooks_const = array();
    public $valid_lang = FALSE;
    public $default_search = array(
        "id_cate" => 0,
        "price_range" => "",
        "keyword" => "",
        "id_manufacturer" => 0,
        "id_attribute" => array()
    );

    function _initialize() {
        ob_start();
        $this->lang->load('ta', $this->template->current_lang['iso_code']);
        $this->lang->load('global', $this->template->current_lang['iso_code']);
    }

    function _initialize_ajax() {
        $this->lang->load('ta', $this->template->current_lang['iso_code']);
        $this->lang->load('global', $this->template->current_lang['iso_code']);
    }

    function __construct() {
        parent::__construct();
        $this->template_type = _TPL_USERPAGE_; // USERPAGE
        $this->load->library('form_validation');
        $this->db->select()->from('hooks');
        $hooks = $this->db->get()->result();
        foreach ($hooks as $h) {
            $this->hooks_const[$h->slug] = $h->id;
        }
    }

    function _remap($method, $params = array()) {
        $this->valid_lang = FALSE;
        $this->method = $method;
        $lang_code = FALSE;
        if (check_domain() == TRUE) {
            if (isset($params[1])) {
                if (strpos(LANGCODE, $params[1]) !== false) {
                    $lang_code = $params[1];
                    unset($params[1]);
                    $this->valid_lang = TRUE;
                }
            }
            $slug = $params[0];
            if ($domain = get_domain_by_slug($slug)) {
                redirect('http://' . $domain);
            }
            unset($params[0]);
            $this->_shop_slug($slug, $lang_code, FALSE);
        } else {
            $slug = get_slug_by_domain($_SERVER['SERVER_NAME']);
            if (isset($params[0])) {
                if (strpos(LANGCODE, $params[0]) !== false) {
                    $lang_code = $params[0];
                    unset($params[0]);
                    $this->valid_lang = TRUE;
                }
            } else {
                $this->valid_lang = TRUE;
            }
            if ($slug) {
                $this->_shop_slug($slug, $lang_code, 'http://' . $_SERVER['SERVER_NAME']);
            } else {
                redirect('shop/user/shop_domain');
            }
        }
        if (method_exists($this, $method . $this->suffix_mobile)) {
            return call_user_func_array(array($this, $method . $this->suffix_mobile), $params);
        } else {
            if (method_exists($this, $method)) {
                return call_user_func_array(array($this, $method), $params);
            } else {
                redirect($this->template->shop_url . '/404');
//                show_404();
            }
        }

        ob_clean();
    }

    function _shop_slug($slug, $lang_code = FALSE, $domain = FALSE) {
        $this->shop = $this->users_m->get_shop_by_name($slug);
        if (count($this->shop) == 0)
            show_404();

//        $langs = $this->config->item('supported_languages');
//        $this->shop->langs = $this->users_m->get_shop_langs($this->shop->id);
        $this->shop->langs = get_shop_langs();

        $current_lang = get_current_lang($lang_code, $this->shop->langs);
        if ($current_lang['id_lang'] == "3") {
            $this->config->set_item('language', 'fr');
        } else if ($current_lang['id_lang'] == "2") {
            $this->config->set_item('language', 'vi');
        } else {
            $this->config->set_item('language', 'en');
        }
        if ($domain == FALSE) {
            $s_url = site_url($this->shop->slug);
            if ($lang_code == FALSE) {
                if (count($this->shop->langs) > 1 && $this->valid_lang) {
                    redirect(site_url($this->shop->slug . '/' . $current_lang['iso_code']));
                }
                else
                    $shop_url = site_url($this->shop->slug);
            } else {
                if (count($this->shop->langs) == 1 && $this->valid_lang)
                    show_404();
                $shop_url = site_url($this->shop->slug . '/' . $lang_code);
            }
            $shop_admin_url = site_url($this->shop->slug) . '/gi-admin/';
        } else {
            $s_url = 'http://' . $_SERVER['SERVER_NAME'];
            $shop_admin_url = 'http://' . $_SERVER['SERVER_NAME'] . '/gi-admin/';
            if ($lang_code == FALSE) {
                if ($this->method != 'shop_admin_login') {
                    if (count($this->shop->langs) > 1) {
                        redirect('http://' . $_SERVER['SERVER_NAME'] . '/' . $current_lang['iso_code']);
                    }
                }
                $shop_url = 'http://' . $_SERVER['SERVER_NAME'];
            } else {
                $shop_url = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $lang_code;
            }
        }
        $themes_up = $this->users_m->get_themes_up($this->shop->id, $current_lang['id_lang']);
        $settings = $this->settings->load_shop_settings();
        $this->shop->use_mobile = $settings['use_mobile'];
        if ($this->suffix_mobile != '') {
            if ($this->shop->use_mobile != '1')
                $this->suffix_mobile = '';
        }
        foreach ($themes_up as $key => $value) {
            $this->shop->{$key} = $value;
        }
        ($this->shop->pstyle_id > 0) OR $this->shop->pstyle_id = 0;

        if ($this->shop->ver == "2") {
            $this->load->library('v2/template');
        } else if (($this->shop->ver == "1") || ($this->shop->ver == "1.1")) {
            $this->load->library('v1/template');
        } else if ($this->shop->ver == "1.2") {
            $this->load->library('v1_2/template');
        } else {
            defined('_THEME_SKIN_') or define('_THEME_SKIN_', _THEME_UP_ . '/skins/' . $this->shop->skin_id);
            defined('_THEME_UP_SKIN_') or define('_THEME_UP_SKIN_', _THEME_UP_ . '/skins/' . $this->shop->skin_id);
            defined('_THEME_UP_LAYOUT_') or define('_THEME_UP_LAYOUT_', _THEME_UP_ . '/layouts/' . $this->shop->layout_id);
            defined('_SHOP_RES_PATH_') or define('_SHOP_RES_PATH_', site_url('assets/shop_resources') . '/' . $this->shop->id);
            defined('_SHOP_RES_FOLDER_') or define('_SHOP_RES_FOLDER_', './assets/shop_resources/' . $this->shop->id);
            $this->load->library('template', array(
                'theme_up_id' => $this->shop->themes_up_id
            ));
            $this->template->add_css('up');
        }

        $this->template->shop_settings = $settings;

        $this->template->set_js_var('system_lang', SYSLANG);
        $this->template->set_js_var('client_ip', get_real_IP_address());
        $this->template->shop_profile = $this->db->select("es_shop_profile.company_name,es_shop_profile.phone,es_shop_profile.address")
                        ->from("es_shop_profile")
                        ->where(array('shop_id' => $this->shop->id))
                        ->get()->row_array();
        $this->template->s_url = $s_url;
        $this->template->shop_url = $shop_url;
        $this->template->lang_id = $current_lang['id_lang'];
        $this->template->current_lang = $current_lang;
        $this->template->shop_admin_url = $shop_admin_url;
        $this->template->shop = $this->shop;
        $this->template->shop_id = $this->shop->id;
        $this->template->default_search = $this->default_search;

        if ($this->is_ajax()) {
            $this->_initialize_ajax();
        } else {
            $this->_initialize();
        }

        if ($this->shop->active != 1) {
            redirect(site_url('siteoff.html'));
        }

        if ($this->shop->ver == "0") {
            if (isset($this->template->shop->layout_id)) {
                $this->template->read_layout_config();
            }
        }

        if ($this->user) {
            if ($this->user->id == $this->shop->user_id)
                $this->template->set_hook('TOOLBAR', 'toolbar_live');
        }

        if ((int) $this->template->shop_settings['site_off'] == 1) {
            $replacement = "";
            if (substr($_SERVER['PATH_INFO'], -1) == '/') {
                $s_admin_url = substr($_SERVER['PATH_INFO'], 0, -1) . $replacement;
            } else {
                $s_admin_url = $_SERVER['PATH_INFO'];
            }
            if (check_domain() == FALSE && $s_admin_url != '/gi-admin') {
                echo $this->template->shop_settings['site_off_message'];
                die;
            } else if (check_domain() == TRUE && $s_admin_url != '/' . $this->shop->slug . '/gi-admin') {
                echo $this->template->shop_settings['site_off_message'];
                die;
            } else {
                return false;
            }
        } else {
            return false;
        };
    }

}
