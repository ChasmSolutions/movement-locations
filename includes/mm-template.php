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
    
    $count['total_rows'] = $wpdb->get_var( "SELECT count(*) as count FROM $wpdb->mm;" );
    $count['total_admin0'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___';" );
    $count['total_admin1'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___';" );
    $count['total_admin2'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___';" );
    $count['total_admin3'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___-___';" );
    $count['total_admin4'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___-___-___';" );
    
    $stats = array(
        'Total Rows' => number_format( $count['total_rows'] ),
        'Total Admin 0 (countries)' => number_format( $count['total_admin0'] ),
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
    
    $countries = $wpdb->get_results( "SELECT CntyID, Cnty_Name FROM $wpdb->mm WHERE WorldID LIKE '___'" );
    foreach ( $countries as $country ) {
        
        $CntyID = $country->CntyID;
        $Cnty_Name = $country->Cnty_Name;
        
        $tree[ $Cnty_Name ] = array(
            'admin1' => $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___' AND CntyID = '$CntyID'" ),
            'admin2' => $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___' AND CntyID = '$CntyID'" ),
            'admin3' => $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___-___' AND CntyID = '$CntyID'" ),
            'admin4' => $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE WorldID LIKE '___-___-___-___-___' AND CntyID = '$CntyID'" ),
        );
    }
    
    return $tree;
}

function mm_sync_by_oz_objectid ( $worldID ) {
    global $wpdb;
    
    $worldID = "'" . trim( $worldID ) . "'";
    $oz_record = json_decode( file_get_contents( 'https://services1.arcgis.com/DnZ5orhsUGGdUZ3h/ArcGIS/rest/services/OmegaZones082016/FeatureServer/0/query?outFields=*&returnGeometry=true&resultRecordCount=1&f=pgeojson&where=WorldID='.$worldID ) );
    if(empty( $oz_record->features )) {
        return 'no records found';
    }
    
    $result = $wpdb->replace(
        $wpdb->mm,
        array(
            'WorldID' => $oz_record->features[0]->properties->WorldID,
            'Zone_Name' => $oz_record->features[0]->properties->Zone_Name,
            'CntyID' => $oz_record->features[0]->properties->CntyID,
            'Cnty_Name' => $oz_record->features[0]->properties->Cnty_Name,
            'Adm1ID' => $oz_record->features[0]->properties->Adm1ID,
            'Adm1_Name' => $oz_record->features[0]->properties->Adm1_Name,
            'Adm2ID' => $oz_record->features[0]->properties->Adm2ID,
            'Adm2_Name' => $oz_record->features[0]->properties->Adm2_Name,
            'Adm3ID' => $oz_record->features[0]->properties->Adm3ID,
            'Adm3_Name' => $oz_record->features[0]->properties->Adm3_Name,
            'Adm4ID' => $oz_record->features[0]->properties->Adm4ID,
            'Adm4_Name' => $oz_record->features[0]->properties->Adm4_Name,
            'World' => $oz_record->features[0]->properties->World,
            'Population' => $oz_record->features[0]->properties->Population,
            'Shape_Leng' => $oz_record->features[0]->properties->Shape_Leng,
            'Cen_x' => $oz_record->features[0]->properties->Cen_x,
            'Cen_y' => $oz_record->features[0]->properties->Cen_y,
            'Region' => $oz_record->features[0]->properties->Region,
            'Field' => $oz_record->features[0]->properties->Field,
            'geometry' => json_encode( $oz_record->features[0]->geometry->rings[0] ),
            'OBJECTID_1' => $oz_record->features[0]->properties->OBJECTID_1,
            'OBJECTID' => $oz_record->features[0]->properties->OBJECTID,
            'Notes' => $oz_record->features[0]->properties->Notes,
            'Last_Sync' => date( "Y-m-d H:i:s" ),
            'Sync_Source' => '4KArcGIS',
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%f',
            '%f',
            '%f',
            '%f',
            '%s',
            '%s',
            '%s',
            '%d',
            '%d',
            '%s',
            '%s',
        )
    );
    
    return $result;
}


function mm_match_usa_state_code ( $statefp ) {
    
}
