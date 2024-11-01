<?php if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class UM_ForumWP
 */
class UM_ForumWP {


	/**
	 * @var
	 */
	private static $instance;


	/**
	 * @return UM_ForumWP
	 */
	static public function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * UM_ForumWP constructor.
	 */
	function __construct() {
		add_filter( 'plugins_loaded', array( &$this, 'init' ) );

		add_filter( 'um_call_object_ForumWP', array( &$this, 'get_this' ) );
		add_filter( 'um_settings_default_values', array( &$this, 'default_settings' ), 10, 1 );
	}


	/**
	 * @return $this
	 */
	function get_this() {
		return $this;
	}


	/**
	 * @param $defaults
	 *
	 * @return array
	 */
	function default_settings( $defaults ) {
		$defaults = array_merge( $defaults, $this->setup()->settings_defaults );
		return $defaults;
	}


	/**
	 * Init
	 */
	function init() {
		$this->profile();
		$this->permissions();
		$this->integrations();

		if ( is_admin() ) {
			$this->admin();
		}
	}


	/**
	 * @return um_ext\um_forumwp\core\ForumWP_Setup()
	 */
	function setup() {
		if ( empty( UM()->classes['um_forumwp_setup'] ) ) {
			UM()->classes['um_forumwp_setup'] = new um_ext\um_forumwp\core\ForumWP_Setup();
		}
		return UM()->classes['um_forumwp_setup'];
	}


	/**
	 * @return um_ext\um_forumwp\core\ForumWP_Profile()
	 */
	function profile() {
		if ( empty( UM()->classes['um_forumwp_profile'] ) ) {
			UM()->classes['um_forumwp_profile'] = new um_ext\um_forumwp\core\ForumWP_Profile();
		}
		return UM()->classes['um_forumwp_profile'];
	}


	/**
	 * @return um_ext\um_forumwp\core\Integrations()
	 */
	function integrations() {
		if ( empty( UM()->classes['um_forumwp_integrations'] ) ) {
			UM()->classes['um_forumwp_integrations'] = new um_ext\um_forumwp\core\Integrations();
		}
		return UM()->classes['um_forumwp_integrations'];
	}


	/**
	 * @return um_ext\um_forumwp\core\ForumWP_Admin()
	 */
	function admin() {
		if ( empty( UM()->classes['um_forumwp_admin'] ) ) {
			UM()->classes['um_forumwp_admin'] = new um_ext\um_forumwp\core\ForumWP_Admin();
		}
		return UM()->classes['um_forumwp_admin'];
	}


	/**
	 * @return um_ext\um_forumwp\core\ForumWP_Permissions()
	 */
	function permissions() {
		if ( empty( UM()->classes['um_forumwp_permissions'] ) ) {
			UM()->classes['um_forumwp_permissions'] = new um_ext\um_forumwp\core\ForumWP_Permissions();
		}
		return UM()->classes['um_forumwp_permissions'];
	}
}

//create class var
add_action( 'plugins_loaded', 'um_init_forumwp', -10, 1 );
function um_init_forumwp() {
	if ( function_exists( 'UM' ) ) {
		UM()->set_class( 'ForumWP', true );
	}
}