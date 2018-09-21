<?php

/**
 * Plugin_Name
 *
 * @package   Plugin_Name
 * @author    {{author_name}} <{{author_email}}>
 * @copyright {{author_copyright}}
 * @license   {{author_license}}
 * @link      {{author_url}}
 */

/**
 * Plugin Name Initializer
 */
class Pn_Initialize {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * List of class to initialize.
	 *
	 * @var object
	 */
	public $classes = null;

	/**
	 * The Constructor that load the entry classes
	 *
	 * @since {{plugin_version}}
	 */
	public function __construct() {
		$this->classes   = array();
		$this->classes[] = 'Pn_PostTypes';
		$this->classes[] = 'Pn_CMB';
		$this->classes[] = 'Pn_Cron';
		$this->classes[] = 'Pn_FakePage';
		$this->classes[] = 'Pn_Template';
		$this->classes[] = 'Pn_Widgets';
		$this->classes[] = 'Pn_Rest';
		$this->classes[] = 'Pn_Transient';

        if ( $this->is_request( 'cli' ) ) {
			$this->classes[] = 'Pn_Cli';
		}

		if ( $this->is_request( 'ajax' ) ) {
			$this->classes[] = 'Pn_Ajax';
			if ( $this->is_request( 'admin' ) ) {
				$this->classes[] = 'Pn_Ajax_Admin';
			}
		}

		if ( $this->is_request( 'admin' ) ) {
			$this->classes[] = 'Pn_Pointers';
			$this->classes[] = 'Pn_ContextualHelp';
			$this->classes[] = 'Pn_Admin_ActDeact';
			$this->classes[] = 'Pn_Admin_Notices';
			$this->classes[] = 'Pn_Admin_Settings_Page';
			$this->classes[] = 'Pn_Admin_Enqueue';
			$this->classes[] = 'Pn_Admin_ImpExp';
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->classes[] = 'Pn_Enqueue';
			$this->classes[] = 'Pn_Extras';
		}

		$this->classes = apply_filters( 'pn_class_instances', $this->classes );

		return $this->load_classes();
	}

	/**
	 * What type of request is this?
	 *
	 * @since {{plugin_version}}
	 *
	 * @param  string $type admin, ajax, cron, cli or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_user_logged_in() && is_admin();
			case 'ajax':
				return ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! defined( 'REST_REQUEST' );
			case 'cli':
				return defined( 'WP_CLI' ) && WP_CLI;
		}
	}

	private function load_classes() {
		foreach ( $this->classes as &$class ) {
			$class = apply_filters( strtolower( $class ) . '_instance', $class );
			$temp  = new $class;
			$temp->initialize();
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since {{plugin_version}}
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			try {
				self::$instance = new self;
			} catch ( Exception $err ) {
				do_action( 'plugin_name_initialize_failed', $err );
				if ( WP_DEBUG ) {
					throw $err->getMessage();
				}
			}
		}

		return self::$instance;
	}

}