<?php

/**
 * Plugin Name: Movement Mapping (Private)
 * Plugin URI: https://github.com/ChasmSolutions/movement_mapping
 * Description: Movement Mapping API for serving country based mapping to disciple tools.
 * Version: 1.0
 * Author: Chasm.Solutions & Kingdom.Training
 * Author URI: https://github.com/ChasmSolutions
 */
if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

/**
 * Singleton class for setting up the plugin.
 *
 * @since  1.0
 * @access public
 */
class Movement_Mapping {
    
    public $mm_table;
    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0
     */
    public $token;
    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0
     */
    public $version;
    
    /**
     * Plugin directory path.
     *
     * @since  1.0
     * @access public
     * @var    string
     */
    public $dir_path = '';
    
    /**
     * Plugin directory URI.
     *
     * @since  1.0
     * @access public
     * @var    string
     */
    public $dir_uri = '';
    
    /**
     * Plugin image directory URI.
     *
     * @since  1.0
     * @access public
     * @var    string
     */
    public $img_uri = '';
    
    /**
     * Movement_Mapping The single instance of Movement_Mapping.
     * @var     object
     * @access  private
     * @since     0.1
     */
    private static $_instance = null;
    
    /**
     * Main Movement_Mapping Instance
     *
     * Ensures only one instance of Movement_Mapping is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Movement_Mapping instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()
    
    /**
     * Constructor method.
     *
     * @since  1.0
     * @access private
     * @return void
     */
    private function __construct() {
        global $wpdb;
        
        $this->setup_actions();
    
        $wpdb->mm = $wpdb->prefix . 'mm';
    
        // Main plugin directory path and URI.
        $this->dir_path     = trailingslashit( plugin_dir_path( __FILE__ ) );
        $this->dir_uri      = trailingslashit( plugin_dir_url( __FILE__ ) );
    
        // Plugin directory paths.
        $this->includes     = trailingslashit( $this->dir_path . 'includes' );
    
        // Admin and settings variables
        $this->token             = 'movement_mapping';
        $this->version             = '1.0';
        
        // Load admin files.
        if ( is_admin() ) {
            require_once( 'includes/admin-menu.php' );
            $this->menu = MM_Admin_Menu::instance();
    
        
        
        } // if admin
        require_once( 'includes/mm-template.php' );
    }
    
    
    
    /**
     * Magic method to output a string if trying to use the object as a string.
     *
     * @since  1.0
     * @access public
     * @return string
     */
    public function __toString() {
        return 'movement_mapping';
    }
    
    /**
     * Magic method to keep the object from being cloned.
     *
     * @since  1.0
     * @access public
     * @return void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'movement_mapping' ), '1.0' );
    }
    
    /**
     * Magic method to keep the object from being unserialized.
     *
     * @since  1.0
     * @access public
     * @return void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'movement_mapping' ), '1.0' );
    }
    
    /**
     * Magic method to prevent a fatal error when calling a method that doesn't exist.
     *
     * @since  1.0
     * @access public
     * @return null
     */
    public function __call( $method = '', $args = array() ) {
        _doing_it_wrong( "movement_mapping::{$method}", esc_html__( 'Method does not exist.', 'movement_mapping' ), '1.0' );
        unset( $method, $args );
        return null;
    }
    
    /**
     * Sets up main plugin actions and filters.
     *
     * @since  1.0
     * @access public
     * @return void
     */
    private function setup_actions() {
        
        // Internationalize the text strings used.
        add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );
        
        // Register activation hook.
        register_activation_hook( __FILE__, array( $this, 'activation' ) );
    }
    
    /**
     * Loads the translation files.
     *
     * @since  1.0
     * @access public
     * @return void
     */
    public function i18n() {
        load_plugin_textdomain( 'movement_mapping', false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ). 'languages' );
    }
    
    /**
     * Method that runs only when the plugin is activated.
     *
     * @since  1.0
     * @access public
     * @return void
     */
    public function activation() {
        
    }
}

/**
 * Gets the instance of the `locations` class.  This function is useful for quickly grabbing data
 * used throughout the plugin.
 *
 * @since  1.0
 * @access public
 * @return object
 */
function movement_mapping () {
    return Movement_Mapping::instance();
}

// Let's roll!
add_action( 'plugins_loaded', 'movement_mapping' );