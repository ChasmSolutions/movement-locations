<?php

/**
 * MM_OZ_Sync
 *
 * @class MM_OZ_Sync
 * @version	1.0
 * @since 1.0
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class MM_OZ_Sync {
    
    /**
     * MM_OZ_Sync The single instance of MM_OZ_Sync.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;
    
    /**
     * Main MM_OZ_Sync Instance
     *
     * Ensures only one instance of MM_OZ_Sync is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return MM_OZ_Sync instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    } // End instance()
    
    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
        
    } // End __construct()
    
    public function sync_by_objectid_1 ( $OBJECTID ) {
        
        
    }
    
}
