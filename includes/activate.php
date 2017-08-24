<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1
 * @package    Disciple_Tools
 * @subpackage Disciple_Tools/includes/admin
 * @author
 */


class MM_Activate {
    
    
    /**
     * Activities to run during installation.
     *
     * Long Description.
     *
     * @since 0.1
     */
    public static function activate( $network_wide ) {
        global $wpdb;
        
        /**
         * Activate database creation for Disciple Tools Activity logs
         *
         * @since 0.1
         */
        if ( is_multisite() && $network_wide ) {
            // Get all blogs in the network and activate plugin on each one
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                self::create_tables( movement_mapping()->version );
                restore_current_blog();
            }
        } else {
            self::create_tables( movement_mapping()->version );
        }
    }
    
    /**
     * Creating tables whenever a new blog is created
     *
     * @param $blog_id
     * @param $user_id
     * @param $domain
     * @param $path
     * @param $site_id
     * @param $meta
     */
    public static function on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        
        if ( is_plugin_active_for_network( 'movement-mapping/movement-mapping.php' ) ) {
            switch_to_blog( $blog_id );
            self::create_tables( movement_mapping()->version );
            restore_current_blog();
        }
    }
    
    public static function on_delete_blog( $tables ) {
        global $wpdb;
        $tables[] = $wpdb->prefix . 'mm';
        return $tables;
    }
    
    /**
     * Creates the tables for the activity and report logs.
     *
     * @access protected
     */
    protected static function create_tables( $version ) {
        global $wpdb;
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        /* Activity Log */
        $table_name = $wpdb->prefix . 'mm';
        if( $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ) {
            $sql1 = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
					  `WorldID` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
                      `Zone_Name` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
                      `CntyID` varchar(3) CHARACTER SET utf8mb4 NOT NULL,
                      `Cnty_Name` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
                      `Adm1ID` varchar(7) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Adm1_Name` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Adm2ID` varchar(11) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Adm2_Name` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Adm3ID` varchar(15) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Adm3_Name` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Adm4ID` varchar(19) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Adm4_Name` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `World` varchar(1) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Population` float DEFAULT NULL,
                      `Shape_Leng` float DEFAULT NULL,
                      `Cen_x` float DEFAULT NULL,
                      `Cen_y` float DEFAULT NULL,
                      `Region` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Field` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `geometry` longtext CHARACTER SET utf8mb4,
                      `Notes` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Last_Sync` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                      `Sync_Source` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
                      `Source_Key` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
                      PRIMARY KEY (`WorldID`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";
            
            dbDelta( $sql1 );
            
            update_option( 'mm_db_version', $version );
        }
        
        
        
    }
    
}
