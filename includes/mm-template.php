<?php
/**
 * Presenter template for theme support
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Get the country database statistics
 * @return array
 */
function ml_get_country_stats () {
    global $wpdb;
    
    $count['total_rows'] = $wpdb->get_var("SELECT count(*) as count FROM $wpdb->mm;");
    $count['total_admin0'] = $wpdb->get_var("SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___';");
    $count['total_admin1'] = $wpdb->get_var("SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___';");
    $count['total_admin2'] = $wpdb->get_var("SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___';");
    $count['total_admin3'] = $wpdb->get_var("SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___-___';");
    $count['total_admin4'] = $wpdb->get_var("SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___-___-___';");
    
    $stats = array(
        'Total Rows' => number_format( $count['total_rows'] ),
        'Total Admin 0' => number_format( $count['total_admin0'] ),
        'Total Admin 1' => number_format( $count['total_admin1'] ),
        'Total Admin 2' => number_format( $count['total_admin2'] ),
        'Total Admin 3' => number_format( $count['total_admin3'] ),
        'Total Admin 4' => number_format( $count['total_admin4'] ),
    );
    
    return $stats;
}

/**
 * Get country tree
 * @return array
 */
function ml_get_admin_tree () {
    global $wpdb;
    
    $tree = array();
    
    $countries = $wpdb->get_results("SELECT CntyID, Cnty_Name FROM $wpdb->mm WHERE WorldID LIKE '___';");
    foreach ( $countries as $country ) {
        
        $CntyID = $country->CntyID;
        $Cnty_Name = $country->Cnty_Name;
        
        $tree[ $Cnty_Name ] = array(
            'admin1' => $wpdb->get_var("SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___' AND CntyID = '$CntyID';"),
            'admin2' => $wpdb->get_var("SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___' AND CntyID = '$CntyID';"),
            'admin3' => $wpdb->get_var("SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___-___' AND CntyID = '$CntyID';"),
            'admin4' => $wpdb->get_var("SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___-___-___' AND CntyID = '$CntyID';"),
        );
    }
    
    return $tree;
}