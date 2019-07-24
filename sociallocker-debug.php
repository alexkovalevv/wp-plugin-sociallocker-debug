<?php
	/**
	 * Plugin Name:[Bizpanda Addon] Отчеты и отладка
	 * Plugin URI: http://byoneress.com
	 * Description: Модификация предназначена для формаирования отчетов и отладки плагинов Onepress.
	 * Author: Webcraftic <alex.kovalevv@gmail.com>
	 * Version: 1.0.3
	 * Author URI: http://byoneress.com
	 */

	define('BZDA_POPUPS_ADN_DEBUG_PLUGIN_URL', plugins_url(null, __FILE__));
	define('BZDA_POPUPS_ADN_DEBUG_PLUGIN_DIR', dirname(__FILE__));

	function onp_bzda_adn_init()
	{
		if( defined('OPTINPANDA_PLUGIN_ACTIVE') || defined('SOCIALLOCKER_PLUGIN_ACTIVE') ) {
			global $bizpanda;

			if( is_admin() ) {
				require_once BZDA_POPUPS_ADN_DEBUG_PLUGIN_DIR . '/admin/pages/debug-settings.php';
			}
		}
	}

	add_action('bizpanda_init', 'onp_bzda_adn_init', 20);


