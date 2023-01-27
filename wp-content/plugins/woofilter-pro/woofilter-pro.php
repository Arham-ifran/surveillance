<?php
/**
 * Plugin Name: Woo Product Filter PRO
 * Description: Product Filter by WooBeWoo PRO. Best plugins from Woobewoo!
 * Plugin URI: https://woobewoo.com/product/woocommerce-filter/
 * Author: WooBeWoo
 * Author URI: https://woobewoo.com/
 * Version: 2.0.4
 * WC requires at least: 3.4.0
 * WC tested up to: 6.0.0
 **/
define('WPF_FREE_REQUIRES', '2.0.4');
// we use it as fallback for a cases when we cant parse install.xml fale
define('WPF_PRO_MODULES',
	serialize(
		array(
			array (
				'code' =>  'license',
				'active' =>  '1',
				'type_id' =>  '6',
				'label' =>  'license',
			),
			array (
				'code' =>  'access',
				'active' =>  '1',
				'type_id' =>  '6',
				'label' =>  'access',
			),
			array (
				'code' =>  'woofilterpro',
				'active' =>  '1',
				'type_id' =>  '6',
				'label' =>  'woofilterpro',
			)
		)
	)
);
if (!defined('WPF_SITE_URL')) {
	define('WPF_SITE_URL', get_bloginfo('wpurl') . '/');
}

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wpUpdater.php');

register_activation_hook(__FILE__, 'woofilterProActivateCallback');
register_deactivation_hook(__FILE__, array('ModInstallerWpf', 'deactivate'));
register_uninstall_hook(__FILE__, array('ModInstallerWpf', 'uninstall'));

add_filter('pre_set_site_transient_update_plugins', 'checkForPluginUpdatewoofilterPro');
add_filter('plugins_api', 'myPluginApiCallwoofilterPro', 10, 3);
if (!function_exists('getProPlugCodeWpf')) {
	function getProPlugCodeWpf() {
		return 'woofilter_pro';
	}
}
if (!function_exists('getProPlugDirWpf')) {
	function getProPlugDirWpf() {
		return basename(dirname(__FILE__));
	}
}
if (!function_exists('getProPlugFileWpf')) {
	function getProPlugFileWpf() {
		return basename(__FILE__);
	}
}
if (!function_exists('getProPlugFullPathWpf')) {
	function getProPlugFullPathWpf() {
		return __FILE__;
	}
}
if (!defined('S_YOUR_SECRET_HASH_' . getProPlugCodeWpf())) {
	define('S_YOUR_SECRET_HASH_' . getProPlugCodeWpf(), 'ng93#g3j9g#R#E)@KDPWKOK)Fkvvk#f30f#KF');
}

if (!function_exists('checkForPluginUpdatewoofilterPro')) {
	function checkForPluginUpdatewoofilterPro( $checkedData ) {
		if (class_exists('WpUpdaterWpf')) {
			return WpUpdaterWpf::getInstance( getProPlugDirWpf(), getProPlugFileWpf(), getProPlugCodeWpf(), getProPlugFullPathWpf() )->checkForPluginUpdate($checkedData);
		}
		return $checkedData;
	}
}
if (!function_exists('myPluginApiCallwoofilterPro')) {
	function myPluginApiCallwoofilterPro( $def, $action, $args ) {
		if (class_exists('WpUpdaterWpf')) {
			return WpUpdaterWpf::getInstance( getProPlugDirWpf(), getProPlugFileWpf(), getProPlugCodeWpf(), getProPlugFullPathWpf() )->myPluginApiCall($def, $action, $args);
		}
		return $def;
	}
}
/**
 * Check if there are base (free) version installed
 */
if (!function_exists('woofilterProActivateCallback')) {
	function woofilterProActivateCallback() {
		if (class_exists('FrameWpf')) {
			$arguments = func_get_args();
			call_user_func_array(array('ModInstallerWpf', 'check'), $arguments);
		}
	}
}
add_action('admin_notices', 'woofilterProInstallBaseMsg');
if (!function_exists('woofilterProInstallBaseMsg')) {
	function woofilterProInstallBaseMsg() {
		if ( !get_option('wpf_full_installed') || !class_exists('FrameWpf') ) {
			$plugName = 'Product Filter by WooBeWoo';
			$plugWpUrl = 'https://wordpress.org/plugins/woo-product-filter/';
			echo '<div class="notice error is-dismissible"><p><strong>
				Please install Free (Base) version of ' . esc_html($plugName) . ' plugin, you can get it <a target="_blank" href="' . esc_url($plugWpUrl) . '">here</a> or use Wordpress plugins search functionality,
				activate it, then deactivate and activate again PRO version of ' . esc_html($plugName) . '.
				In this way you will have full and upgraded PRO version of ' . esc_html($plugName) . '.</strong></p></div>';
		} else if (version_compare(WPF_VERSION, WPF_FREE_REQUIRES, '<')) {
			$plugName = 'Product Filter by WooBeWoo';
			$plugWpUrl = 'https://wordpress.org/plugins/woo-product-filter/';
			echo '<div class="notice error is-dismissible"><p><strong>
				Please install latest Free (Base) version of ' . esc_html($plugName) . ' plugin (requires at least ' . esc_html(WPF_FREE_REQUIRES) . '), you can get it <a target="_blank" href="' . esc_url($plugWpUrl) . '">here</a> or use Wordpress plugins search functionality,
				activate it, then deactivate and activate again PRO version of ' . esc_html($plugName) . '.
				In this way you will have full and upgraded PRO version of ' . esc_html($plugName) . '.</strong></p></div>';
		}
	}
}
