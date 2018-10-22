<?php
/*
Plugin Name: Boilerplate Plugin
Description: Boilerplate plugin
Version: 0.1
Author: Michael AuCoin
License: none
Text Domain: starter
*/


// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Base_Plugin class
 *
 * @class Boilerplate_Plugin The class that holds the entire Base_Plugin plugin
 */
final class Boilerplate_Plugin {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '0.1.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the Boilerplate_Plugin class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {

        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    /**
     * Initializes the Boilerplate_Plugin() class
     *
     * Checks for an existing Boilerplate_Plugin() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Boilerplate_Plugin();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'BOILERPLATE_VERSION', $this->version );
        define( 'BOILERPLATE_FILE', __FILE__ );
        define( 'BOILERPLATE_PATH', dirname( BOILERPLATE_FILE ) );
        define( 'BOILERPLATE_INCLUDES', BOILERPLATE_PATH . '/includes' );
        define( 'BOILERPLATE_URL', plugins_url( '', BOILERPLATE_FILE ) );
        define( 'BOILERPLATE_ASSETS', BOILERPLATE_URL . '/assets' );
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

        $installed = get_option( 'boilerplate_installed' );

        if ( ! $installed ) {
            update_option( 'boilerplate_installed', time() );
        }

        update_option( 'boilerplate_version', BOILERPLATE_VERSION );
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {

        require_once BOILERPLATE_INCLUDES . '/class-assets.php';

        if ( $this->is_request( 'admin' ) ) {
            require_once BOILERPLATE_INCLUDES . '/class-admin.php';
        }

        if ( $this->is_request( 'frontend' ) ) {
            require_once BOILERPLATE_INCLUDES . '/class-frontend.php';
        }

        if ( $this->is_request( 'ajax' ) ) {
            // require_once BOILERPLATE_INCLUDES . '/class-ajax.php';
        }

        if ( $this->is_request( 'rest' ) ) {
            require_once BOILERPLATE_INCLUDES . '/class-rest-api.php';
        }
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {

        add_action( 'init', array( $this, 'init_classes' ) );

    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {

        if ( $this->is_request( 'admin' ) ) {
            $this->container['admin'] = new App\Admin();
        }

        if ( $this->is_request( 'frontend' ) ) {
            $this->container['frontend'] = new App\Frontend();
        }

        if ( $this->is_request( 'ajax' ) ) {
            // $this->container['ajax'] =  new App\Ajax();
        }

        if ( $this->is_request( 'rest' ) ) {
            $this->container['rest'] = new App\REST_API();
        }

        $this->container['assets'] = new App\Assets();
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();

            case 'ajax' :
                return defined( 'DOING_AJAX' );

            case 'rest' :
                return defined( 'REST_REQUEST' );

            case 'cron' :
                return defined( 'DOING_CRON' );

            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

} // Base_Plugin

$boilerplate = Base_Plugin::init();
