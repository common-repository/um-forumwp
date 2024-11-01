<?php
namespace um_ext\um_forumwp\core;


if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class ForumWP_Admin
 *
 * @package um_ext\um_forumwp\core
 */
class ForumWP_Admin {


	/**
	 * ForumWP_Admin constructor.
	 */
	function __construct() {
		add_filter( 'um_admin_role_metaboxes', array( &$this, 'add_role_metabox' ), 10, 1 );
		add_filter( 'um_is_ultimatememeber_admin_screen', array( &$this, 'is_um_screen' ), 10, 1 );

		add_action( 'add_meta_boxes',  array( &$this, 'add_forum_access_metabox' ), 10, 1 );
		add_action( 'um_admin_custom_restrict_content_metaboxes',  array( &$this, 'save_forum_access_metabox' ), 10, 2 );

		add_filter( 'um_profile_completeness_roles_metabox_fields', array( &$this, 'role_completeness_fields' ), 10, 2 );
	}


	/**
	 * Adds a ForumWP profile completeness role settings
	 *
	 * @param $fields
	 * @param $role
	 *
	 * @return array
	 */
	function role_completeness_fields( $fields, $role ) {
		$fields[] = [
			'id'            => '_um_profilec_prevent_forumwp',
			'type'          => 'select',
			'label'         => __( 'Require profile to be complete to create new ForumWP topics/replies?', 'um-forumwp' ),
			'tooltip'       => __( 'Prevent user from adding participating in forum If their profile completion is below the completion threshold set up above?', 'um-forumwp' ),
			'value'         => ! empty( $role['_um_profilec_prevent_forumwp'] ) ? $role['_um_profilec_prevent_forumwp'] : 0,
			'conditional'   => [ '_um_profilec', '=', '1' ],
			'options'       => [
				0   => __( 'No', 'um-forumwp' ),
				1   => __( 'Yes', 'um-forumwp' ),
			],
		];

		return $fields;
	}


	/**
	 * Creates options in Role page
	 *
	 * @param array $roles_metaboxes
	 *
	 * @return array
	 */
	function add_role_metabox( $roles_metaboxes ) {
		$roles_metaboxes[] = array(
			'id'        => "um-admin-form-forumwp{" . um_forumwp_path . "}",
			'title'     => __( 'ForumWP', 'um-forumwp' ),
			'callback'  => array( UM()->metabox(), 'load_metabox_role' ),
			'screen'    => 'um_role_meta',
			'context'   => 'normal',
			'priority'  => 'default'
		);

		return $roles_metaboxes;
	}


	/**
	 * Extends UM admin pages for enqueue scripts
	 *
	 * @param $is_um
	 *
	 * @return bool
	 */
	function is_um_screen( $is_um ) {
		global $current_screen;
		if ( strstr( $current_screen->id, 'fmwp_forum' ) ) {
			$is_um = true;
		}

		return $is_um;
	}


	/**
	 * Creates UM Permissions metabox for Forum CPT
	 *
	 * @param $action
	 */
	function add_forum_access_metabox( $action ) {
		add_meta_box(
			"um-admin-custom-access/forumwp{" . um_forumwp_path . "}",
			__( 'UM Permissions', 'um-forumwp' ),
			array( UM()->metabox(), 'load_metabox_custom' ),
			'fmwp_forum',
			'side',
			'low'
		);
	}



	/**
	 * Save postmeta on Forum CPT
	 *
	 * @param bool $post_id
	 * @param bool|\WP_Post $post
	 */
	function save_forum_access_metabox( $post_id = false, $post = false ) {

		if ( empty( $post->post_type ) || $post->post_type != 'fmwp_forum' ) {
			return;
		}

		$um_fmwp_can_topic = ! empty( $_POST['_um_forumwp_can_topic'] ) ? $_POST['_um_forumwp_can_topic'] : array();
		$um_fmwp_can_reply = ! empty( $_POST['_um_forumwp_can_reply'] ) ? $_POST['_um_forumwp_can_reply'] : array();

		update_post_meta( $post_id, '_um_forumwp_can_topic', $um_fmwp_can_topic );
		update_post_meta( $post_id, '_um_forumwp_can_reply', $um_fmwp_can_reply );
	}
}