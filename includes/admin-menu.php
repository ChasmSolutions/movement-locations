<?php

/**
 * Disciple_Tools_Tabs
 *
 * @class   Disciple_Tools_Tabs
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools_Tabs
 * @author  Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class MM_Admin_Menu {

    public $path;

    /**
     * MM_Admin_Menu The single instance of MM_Admin_Menu.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main MM_Admin_Menu Instance
     *
     * Ensures only one instance of MM_Admin_Menu is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return MM_Admin_Menu instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {
        $this->path  = plugin_dir_path( __DIR__ );

        add_action( 'admin_menu', [ $this, 'load_admin_menu_item' ] );
    } // End __construct()

    /**
     * Load Admin menu into Settings
     */
    public function load_admin_menu_item () {
        add_menu_page( __('Movement Mapping', 'movement_mapping'), __('Movement Mapping', 'movement_mapping'), 'manage_options', 'movement_mapping', [ $this, 'page_content'], 'dashicons-admin-site', '6' );
        add_submenu_page( 'movement_mapping', __( 'Table', 'movement_mapping' ), __( 'Table', 'movement_mapping' ), 'manage_options', 'movement_locations', [ $this, 'mm_table_page' ] );
    }

    /**
     * Builds the tab bar
     *
     * @since 0.1
     */
    public function page_content() {

        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        /**
         * Begin Header & Tab Bar
         */
        if (isset( $_GET["tab"] )) {$tab = $_GET["tab"];
        } else {$tab = 'settings';}

        $tab_link_pre = '<a href="edit.php?post_type=locations&page=movement_locations&tab=';
        $tab_link_post = '" class="nav-tab ';

        $html = '<div class="wrap">
            <h2>Locations Settings</h2>
            <h2 class="nav-tab-wrapper">';

        $html .= $tab_link_pre . 'settings' . $tab_link_post;
        if ($tab == 'settings' || !isset( $tab )) {$html .= 'nav-tab-active';}
        $html .= '">Settings</a>';

        $html .= $tab_link_pre . 'sync' . $tab_link_post;
        if ($tab == 'sync' ) {$html .= 'nav-tab-active';}
        $html .= '">Sync</a>';
    
        $html .= $tab_link_pre . 'stats' . $tab_link_post;
        if ($tab == 'stats' ) {$html .= 'nav-tab-active';}
        $html .= '">Stats</a>';

        $html .= '</h2>';

        echo $html; // Echo tabs

        $html = '';
        // End Tab Bar

        /**
         * Begin Page Content
         */
        switch ($tab) {

            case "sync":
                require_once ( 'admin-tab-sync.php' );
                $class_object = new MM_Admin_Tab_Sync ();
                $html .= '' . $class_object->page_contents();
                break;
            case "stats":
                require_once ( 'admin-tab-stats.php' );
                $class_object = new MM_Admin_Tab_Stats();
                $html .= '' . $class_object->page_contents();
                break;
            case "settings":
                require_once ( 'admin-tab-settings.php' );
                $class_object = new MM_Admin_Tab_Settings();
                $html .= '' . $class_object->page_contents();
                break;
            default:
                break;
        }

        $html .= '</div>'; // end div class wrap

        echo $html; // Echo contents
    }
    
    /**
     * Display Table List
     */
    public function mm_table_page (){
    
        require_once( 'mm-table.php' );
        $ListTable = new MM_Table();
        //Fetch, prepare, sort, and filter our data...
        $ListTable->prepare_items();
        
        ?>
        <div class="wrap">
            
            <div id="icon-users" class="icon32"><br/></div>
            <h2>Movement Mapping Table</h2>
            
            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="movies-filter" method="get">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <!-- Now we can render the completed list table -->
                <?php $ListTable->display() ?>
            </form>
        
        </div>
        <?php
    }
}
