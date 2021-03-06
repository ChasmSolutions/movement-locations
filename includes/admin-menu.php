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
        add_menu_page( __( 'Movement Mapping', 'movement_mapping' ), __( 'Movement Mapping', 'movement_mapping' ), 'manage_options', 'movement_mapping', [ $this, 'page_content'], 'dashicons-admin-site', '6' );
        add_submenu_page( 'movement_mapping', __( 'Search & Sync', 'movement_mapping' ), __( 'Search & Sync', 'movement_mapping' ), 'manage_options', 'movement_locations', [ $this, 'mm_table_page' ] );
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
        } else {$tab = 'convert';}

        $tab_link_pre = '<a href="admin.php?page=movement_mapping&tab=';
        $tab_link_post = '" class="nav-tab ';

        $html = '<div class="wrap">
            <h2>Movement Mapping Settings</h2>
            <h2 class="nav-tab-wrapper">';
        
        $html .= $tab_link_pre . 'convert' . $tab_link_post;
        if ($tab == 'convert' ) {$html .= 'nav-tab-active';}
        $html .= '">Convert</a>';
        
        $html .= $tab_link_pre . 'stats' . $tab_link_post;
        if ($tab == 'stats' ) {$html .= 'nav-tab-active';}
        $html .= '">Stats</a>';

//        $html .= $tab_link_pre . 'settings' . $tab_link_post;
//        if ($tab == 'settings' || !isset( $tab )) {$html .= 'nav-tab-active';}
//        $html .= '">Settings</a>';

        $html .= '</h2>';

        echo $html; // Echo tabs

        $html = '';
        // End Tab Bar

        /**
         * Begin Page Content
         */
        switch ($tab) {

            case "convert":
                require_once( 'admin-tab-import.php' );
                $class_object = new MM_Admin_Tab_Import();
                $html .= '' . $class_object->page_contents();
                break;
            case "stats":
                require_once( 'admin-tab-stats.php' );
                $class_object = new MM_Admin_Tab_Stats();
                $html .= '' . $class_object->page_contents();
                break;
            case "settings":
                require_once( 'admin-tab-settings.php' );
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
        if( isset( $_GET['s'] ) ){
            trim( $_GET['s'] );
            $ListTable->prepare_items( $_GET['s'] );
        } else {
            $ListTable->prepare_items();
        }
        
        
        ?>
        <div class="wrap">
            
            <div id="icon-users" class="icon32"><br/></div>
            <h2>Movement Mapping Table</h2>
            
            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="movement-mapping" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php $ListTable->search_box( 'Search Table', 'movement-mapping' ); ?>
                <?php $ListTable->display() ?>
                
            </form>
        
        </div>
        <?php
    }
}
