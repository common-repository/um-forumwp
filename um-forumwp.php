<?php
/**
 * Plugin Name: Ultimate Member - ForumWP
 * Plugin URI: https://ultimatemember.com/extensions/forumwp/
 * Description: Integrates Ultimate Member with ForumWP
 * Version: 2.1.6
 * Author: Ultimate Member
 * Author URI: http://ultimatemember.com/
 * Text Domain: um-forumwp
 * Domain Path: /languages
 * Requires at least: 5.5
 * Requires PHP: 7.0
 * UM version: 2.7.0
 * ForumWP version: 2.0
 * Requires Plugins: ultimate-member, forumwp
 *
 * @package UM_ForumWP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugin_data = get_plugin_data( __FILE__ );

define( 'um_forumwp_url', plugin_dir_url( __FILE__ ) );
define( 'um_forumwp_path', plugin_dir_path( __FILE__ ) );
define( 'um_forumwp_plugin', plugin_basename( __FILE__ ) );
define( 'um_forumwp_extension', $plugin_data['Name'] );
define( 'um_forumwp_version', $plugin_data['Version'] );
define( 'um_forumwp_textdomain', 'um-forumwp' );

define( 'um_forumwp_requires', '2.7.0' );

function um_forumwp_plugins_loaded() {
	$locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
	load_textdomain( um_forumwp_textdomain, WP_LANG_DIR . '/plugins/' .um_forumwp_textdomain . '-' . $locale . '.mo');
	load_plugin_textdomain( um_forumwp_textdomain, false, dirname( plugin_basename(  __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'um_forumwp_plugins_loaded', 0 );


add_action( 'plugins_loaded', 'um_forumwp_check_dependencies', -20 );

if ( ! function_exists( 'um_forumwp_check_dependencies' ) ) {
	function um_forumwp_check_dependencies() {
		if ( ! defined( 'um_path' ) || ! file_exists( um_path  . 'includes/class-dependencies.php' ) ) {
			//UM is not installed
			function um_forumwp_dependencies() {
				// translators: %s is the ForumWP extension name.
				echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-forumwp' ), um_forumwp_extension ) . '</p></div>';
			}

			add_action( 'admin_notices', 'um_forumwp_dependencies' );
		} else {

			if ( ! function_exists( 'UM' ) ) {
				require_once um_path . 'includes/class-dependencies.php';
				$is_um_active = um\is_um_active();
			} else {
				$is_um_active = UM()->dependencies()->ultimatemember_active_check();
			}

			if ( ! $is_um_active ) {
				//UM is not active
				function um_forumwp_dependencies() {
					// translators: %s is the ForumWP extension name.
					echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-forumwp' ), um_forumwp_extension ) . '</p></div>';
				}

				add_action( 'admin_notices', 'um_forumwp_dependencies' );

			} elseif ( true !== UM()->dependencies()->compare_versions( um_forumwp_requires, um_forumwp_version, 'forumwp', um_forumwp_extension ) ) {
				//UM old version is active
				function um_forumwp_dependencies() {
					echo '<div class="error"><p>' . UM()->dependencies()->compare_versions( um_forumwp_requires, um_forumwp_version, 'forumwp', um_forumwp_extension ) . '</p></div>';
				}

				add_action( 'admin_notices', 'um_forumwp_dependencies' );

			} elseif ( ! UM()->dependencies()->forumwp_active_check() ) {
				//UM is not active
				function um_forumwp_dependencies() {
					// translators: %s is the ForumWP extension name.
					echo '<div class="error"><p>' . sprintf( __( 'Sorry. You must activate the <strong>ForumWP</strong> plugin to use the %s.', 'um-forumwp' ), um_forumwp_extension ) . '</p></div>';
				}

				add_action( 'admin_notices', 'um_forumwp_dependencies' );
			} else {
				require_once um_forumwp_path . 'includes/core/um-forumwp-init.php';
			}
		}
	}
}


if ( ! function_exists( 'um_forumwp_activation_hook' ) ) {
	function um_forumwp_activation_hook() {
		//first install
		$version = get_option( 'um_forumwp_version' );
		if ( ! $version ) {
			update_option( 'um_forumwp_last_version_upgrade', um_forumwp_version );
		}

		if ( $version != um_forumwp_version ) {
			update_option( 'um_forumwp_version', um_forumwp_version );
		}

		//run setup
		if ( ! class_exists( 'um_ext\um_forumwp\core\ForumWP_Setup' ) ) {
			require_once um_forumwp_path . 'includes/core/class-forumwp-setup.php';
		}

		$fmwp_setup = new um_ext\um_forumwp\core\ForumWP_Setup();
		$fmwp_setup->run_setup();
	}
}
register_activation_hook( um_forumwp_plugin, 'um_forumwp_activation_hook' );
