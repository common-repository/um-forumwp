<?php
namespace um_ext\um_forumwp\core;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Class ForumWP_Setup
 *
 * @package um_ext\um_forumwp\core
 */
class ForumWP_Setup {


	/**
	 * @var array
	 */
	var $settings_defaults;


	/**
	 * ForumWP_Setup constructor.
	 */
	function __construct() {
		//settings defaults
		$this->settings_defaults = array(
			'profile_tab_forumwp'           => 1,
			'profile_tab_forumwp_privacy'   => 0,
		);

		$notification_types_templates = array(
			'fmwp_mention'      => __( '<strong>{member}</strong> just mentioned you <a href="{post_url}" target="_blank">here</a>.', 'um-forumwp' ),
			'fmwp_new_reply'    => __( '<strong>{member}</strong> has <strong><a href="{post_url}" target="_blank">replied</a></strong> to a topic or forum on which you are subscribed.', 'um-forumwp' ),
			'fmwp_new_topic'    => __( '<strong>{member}</strong> has <strong>created a new <a href="{post_url}" target="_blank">topic</a></strong> in a forum on which you are subscribed.', 'um-forumwp' ),
		);

		foreach ( $notification_types_templates as $k => $template ) {
			$this->settings_defaults[ 'log_' . $k ] = 1;
			$this->settings_defaults[ 'log_' . $k . '_template' ] = $template;
		}
	}


	/**
	 *
	 */
	function set_default_settings() {
		$options = get_option( 'um_options', array() );

		foreach ( $this->settings_defaults as $key => $value ) {
			//set new options to default
			if ( ! isset( $options[ $key ] ) ) {
				$options[ $key ] = $value;
			}

		}

		update_option( 'um_options', $options );
	}


	/**
	 *
	 */
	function run_setup() {
		$this->set_default_settings();
	}
}