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
function mm_get_country_stats () {
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
 * Get the usa database statistics
 * @return array
 */
function mm_get_usa_stats () {
    global $wpdb;
    $table = $wpdb->mm_usa;
    
    $count['total_rows'] = $wpdb->get_var( "SELECT count(*) as count FROM $wpdb->mm_usa;" );
    $count['total_admin0'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm_usa WHERE WorldID LIKE '___';" );
    $count['total_admin1'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm_usa WHERE WorldID LIKE '___-___';" );
    $count['total_admin2'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm_usa WHERE WorldID LIKE '___-___-___';" );
    $count['total_admin3'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm_usa WHERE WorldID LIKE '___-___-___-___';" );
    
    $stats = array(
        'Total Rows' => number_format( $count['total_rows'] ),
        'Total Countries' => number_format( $count['total_admin0'] ),
        'Total States' => number_format( $count['total_admin1'] ),
        'Total Counties' => number_format( $count['total_admin2'] ),
        'Total Tracts' => number_format( $count['total_admin3'] ),
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
    
    $oz_record = json_decode( file_get_contents( "https://services1.arcgis.com/DnZ5orhsUGGdUZ3h/ArcGIS/rest/services/OmegaZones082016/FeatureServer/0/query?outFields=*&returnGeometry=true&resultRecordCount=1&f=pgeojson&where=WorldID='".$worldID."'" ) );
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
            'geometry' => json_encode( $oz_record->features[0]->geometry->coordinates, JSON_PRESERVE_ZERO_FRACTION | JSON_NUMERIC_CHECK ),
            'Notes' => $oz_record->features[0]->properties->Notes,
            'Last_Sync' => date( "Y-m-d H:i:s", time() ),
            'Sync_Source' => '4KArcGIS',
            'Source_Key' => $oz_record->features[0]->properties->OBJECTID_1,
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
            '%d',
            '%f',
            '%f',
            '%f',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            
        )
    );
    
    return $result;
}

/**
 * @param $statefp
 * @return bool|string
 */
function mm_convert_usa_state_code ( $statefp ) {
    
    switch ($statefp) {
        case '02': return 'USA-AKA';
        break;
        case '01': return 'USA-ALA';
        break;
        case '04': return 'USA-ARI';
        break;
        case '05': return 'USA-ARK';
        break;
        case '06': return 'USA-CAL';
        break;
        case '08': return 'USA-COL';
        break;
        case '09': return 'USA-CON';
        break;
        case '11': return 'USA-DCO';
        break;
        case '10': return 'USA-DEL';
        break;
        case '12': return 'USA-FLO';
        break;
        case '13': return 'USA-GEO';
        break;
        case '15': return 'USA-HIA';
        break;
        case '16': return 'USA-IDA';
        break;
        case '17': return 'USA-ILL';
        break;
        case '18': return 'USA-IND';
        break;
        case '19': return 'USA-IOW';
        break;
        case '20': return 'USA-KAN';
        break;
        case '21': return 'USA-KEN';
        break;
        case '22': return 'USA-LOU';
        break;
        case '23': return 'USA-MAI';
        break;
        case '24': return 'USA-MAR';
        break;
        case '25': return 'USA-MAS';
        break;
        case '26': return 'USA-MIC';
        break;
        case '28': return 'USA-MII';
        break;
        case '27': return 'USA-MIN';
        break;
        case '29': return 'USA-MIS';
        break;
        case '30': return 'USA-MON';
        break;
        case '37': return 'USA-NCA';
        break;
        case '38': return 'USA-NDA';
        break;
        case '31': return 'USA-NEB';
        break;
        case '32': return 'USA-NEV';
        break;
        case '33': return 'USA-NHA';
        break;
        case '34': return 'USA-NJE';
        break;
        case '35': return 'USA-NME';
        break;
        case '36': return 'USA-NYO';
        break;
        case '39': return 'USA-OHI';
        break;
        case '40': return 'USA-OKL';
        break;
        case '41': return 'USA-ORE';
        break;
        case '42': return 'USA-PEN';
        break;
        case '44': return 'USA-RHO';
        break;
        case '45': return 'USA-SCA';
        break;
        case '46': return 'USA-SDA';
        break;
        case '47': return 'USA-TEN';
        break;
        case '48': return 'USA-TEX';
        break;
        case '49': return 'USA-UTA';
        break;
        case '50': return 'USA-VER';
        break;
        case '51': return 'USA-VIR';
        break;
        case '53': return 'USA-WAS';
        break;
        case '55': return 'USA-WIS';
        break;
        case '54': return 'USA-WVI';
        break;
        case '56': return 'USA-WYO';
        break;
        default: return false;
        break;
    }
}

function mm_convert_usa_state_name ( $statefp ) {
    switch ( $statefp ) {
        case '02': return 'Alaska';
        break;
        case '01': return 'Alabama';
        break;
        case '04': return 'Arizona';
        break;
        case '05': return 'Arkansas';
        break;
        case '06': return 'California';
        break;
        case '08': return 'Colorado';
        break;
        case '09': return 'Connecticut';
        break;
        case '11': return 'District of Columbia';
        break;
        case '10': return 'Delaware';
        break;
        case '12': return 'Florida';
        break;
        case '13': return 'Georgia';
        break;
        case '15': return 'Hawaii and Island Territories';
        break;
        case '16': return 'Idaho';
        break;
        case '17': return 'Illinois';
        break;
        case '18': return 'Indiana';
        break;
        case '19': return 'Iowa';
        break;
        case '20': return 'Kansas';
        break;
        case '21': return 'Kentucky';
        break;
        case '22': return 'Louisiana';
        break;
        case '23': return 'Maine';
        break;
        case '24': return 'Maryland';
        break;
        case '25': return 'Massachusetts';
        break;
        case '26': return 'Michigan';
        break;
        case '28': return 'Mississippi';
        break;
        case '27': return 'Minnesota';
        break;
        case '29': return 'Missouri';
        break;
        case '30': return 'Montana';
        break;
        case '37': return 'North Carolina';
        break;
        case '38': return 'North Dakota';
        break;
        case '31': return 'Nebraska';
        break;
        case '32': return 'Nevada';
        break;
        case '33': return 'New Hampshire';
        break;
        case '34': return 'New Jersey';
        break;
        case '35': return 'New Mexico';
        break;
        case '36': return 'New York';
        break;
        case '39': return 'Ohio';
        break;
        case '40': return 'Oklahoma';
        break;
        case '41': return 'Oregon';
        break;
        case '42': return 'Pennsylvania';
        break;
        case '44': return 'Rhode Island';
        break;
        case '45': return 'South Carolina';
        break;
        case '46': return 'South Dakota';
        break;
        case '47': return 'Tennessee';
        break;
        case '48': return 'Texas';
        break;
        case '49': return 'Utah';
        break;
        case '50': return 'Vermont';
        break;
        case '51': return 'Virginia';
        break;
        case '53': return 'Washington';
        break;
        case '55': return 'Wisconsin';
        break;
        case '54': return 'West Virginia';
        break;
        case '56': return 'Wyoming';
        break;
        default: return false;
        break;
    }
}

/**
 * @param $geometry
 * @param $single   bool    This is whether it is a single polygon or multiple polygons
 */
function mm_find_center ( $geometry ) {
    $rings = json_decode( $geometry );
    
    /* set values */
    $high_lng_e = -9999999; //will hold max val
    $high_lat_n = -9999999; //will hold max val
    $low_lng_w = 9999999; //will hold max val
    $low_lat_s = 9999999; //will hold max val
    
    /* filter for high and lows*/
    foreach ($rings as $polygon) {
        foreach ( $polygon as $v ) {
            if ( (float) $v[0] > $high_lng_e ) {
                $high_lng_e = (float) $v[0];
            }
            if ( (float) $v[0] < $low_lng_w ) {
                $low_lng_w = (float) $v[0];
            }
            if ( (float) $v[1] > $high_lat_n ) {
                $high_lat_n = (float) $v[1];
            }
            if ( (float) $v[1] < $low_lat_s ) {
                $low_lat_s = (float) $v[1];
            }
        }
    }
    
    // calculate centers
    $lng_size = $high_lng_e - $low_lng_w;
    $half_lng_difference = $lng_size / 2;
    $center_lng = $high_lng_e - $half_lng_difference;
    //    print ' | lng size: '.$lng_size ;
    
    $lat_size = $high_lat_n - $low_lat_s;
    $half_lat_difference = $lat_size / 2;
    $center_lat = $high_lat_n - $half_lat_difference;
    //    print ' | lat size: '.$lat_size ;
    
    return array("Cen_y" => $center_lat, "Cen_x" => $center_lng );
}
