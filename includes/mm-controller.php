<?php
/**
 * Contains create, update and delete functions for locations, wrapping access to
 * the database
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


class MM_Controller {
    
    /**
     * Returns the records according to country and level
     *
     * @param  $address
     * @return array
     */
    public static function get_country_by_level ( $cnty_id, $level ) {
        global $wpdb;
        $where = '';
        
        switch ($level) {
            case '0':
                $where = "WHERE `WorldID` LIKE '___' AND `CntyID` = '$cnty_id'";
                break;
            case '1':
                $where = "WHERE `WorldID` LIKE '___-___' AND `CntyID` = '$cnty_id'";
                break;
            case '2':
                $where = "WHERE `WorldID` LIKE '___-___-___' AND `CntyID` = '$cnty_id'";
                break;
            case '3':
                $where = "WHERE `WorldID` LIKE '___-___-___-___' AND `CntyID` = '$cnty_id'";
                break;
            case '4':
                $where = "WHERE `WorldID` LIKE '___-___-___-___-___' AND `CntyID` = '$cnty_id'";
                break;
            default:
                break;
        }
        
        // query $cnty_id and filter for admin1
        $data = $wpdb->get_results( "SELECT * FROM $wpdb->mm $where", ARRAY_A );
    
        $geojson = [
            'type' => 'FeatureCollection',
            'features'  => []
        ];
        
        //prepare returns in foreach loop for geojson
        foreach ( $data as $record ) {
            $geojson['features'][] =
                [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' =>
                            json_decode( $record['geometry'] )
                        
                    ],
                    'properties' => [
                        'WorldID' => $record['WorldID'],
                        'Zone_Name' => $record['Zone_Name'],
                        'CntyID' => $record['CntyID'],
                        'Cnty_Name' => $record['Cnty_Name'],
                        'Adm1ID' => $record['Adm1ID'],
                        'Adm1_Name' => $record['Adm1_Name'],
                        'Adm2ID' => $record['Adm2ID'],
                        'Adm2_Name' => $record['Adm2_Name'],
                        'Adm3ID' => $record['Adm3ID'],
                        'Adm3_Name' => $record['Adm3_Name'],
                        'Adm4ID' => $record['Adm4ID'],
                        'Adm4_Name' => $record['Adm4_Name'],
                        'World' => $record['World'],
                        'Population' => (int) $record['Population'],
                        'Shape_Leng' => (float) $record['Shape_Leng'],
                        'Cen_x' => (float) $record['Cen_x'],
                        'Cen_y' => (float) $record['Cen_y'],
                        'Region' => $record['Region'],
                        'Field' => $record['Field'],
                        'Notes' => $record['Notes'],
                        'Last_Sync' => $record['Last_Sync'],
                        'Sync_Source' => $record['Sync_Source'],
                        'Source_Key' => $record['Source_Key']
                    ]
                ];
        }
        
        return [
            'status' => 'OK',
            'geojson' => $geojson,
        ];
    }
    
    public static function get_summary( $cnty_id ) {
        global $wpdb;
        $count = [];
        
        // Total number of admin1
        $count['admin1'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE CntyID = '$cnty_id' AND WorldID LIKE '___-___'" );
    
        // Total number of admin2
        $count['admin2'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE CntyID = '$cnty_id' AND WorldID LIKE '___-___-___'" );
    
        // Total number of admin3
        $count['admin3'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE CntyID = '$cnty_id' AND WorldID LIKE '___-___-___-___'" );
    
        // Total number of admin4
        $count['admin4'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->mm WHERE CntyID = '$cnty_id' AND WorldID LIKE '___-___-___-___-___'" );
        
        return [
            "adm1_count" => $count['admin1'],
            "adm2_count" => $count['admin2'],
            "adm3_count" => $count['admin3'],
            "adm4_count" => $count['admin4'],
        ];
    }
    
    /**
     * Get USA state counties and tracts
     * @param $state_id
     * @param $level    string      "county" or "tract"
     *
     * @return array
     */
    public static function get_usa_state( $state_id, $level ) {
        global $wpdb;
        $cnty_id = substr( $state_id, 0,3 );
        $where = '';
    
        switch ($level) {
            case 'county':
                $where = "WHERE `WorldID` LIKE '$state_id-___' AND `CntyID` = '$cnty_id'";
                break;
            case 'tract':
                $where = "WHERE `WorldID` LIKE '$state_id-___-%' AND `CntyID` = '$cnty_id'";
                break;
            case 'state':
                $where = "WHERE `WorldID` = '$state_id'";
                break;
            default:
                break;
        }
    
        // query $cnty_id and filter for admin1
        $data = $wpdb->get_results( "SELECT * FROM $wpdb->mm $where", ARRAY_A );
        if(empty( $data )) {
            return [
                'status' => false,
                'message' => 'Failed on the sql query. No results found.'
            ];
        }
    
        $geojson = [
            'type' => 'FeatureCollection',
            'features'  => []
        ];
    
        //prepare returns in foreach loop for geojson
        foreach ( $data as $record ) {
            $geojson['features'][] =
                [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' =>
                            json_decode( $record['geometry'] )
                
                    ],
                    'properties' => [
                        'WorldID' => $record['WorldID'],
                        'Zone_Name' => $record['Zone_Name'],
                        'CntyID' => $record['CntyID'],
                        'Cnty_Name' => $record['Cnty_Name'],
                        'Adm1ID' => $record['Adm1ID'],
                        'Adm1_Name' => $record['Adm1_Name'],
                        'Adm2ID' => $record['Adm2ID'],
                        'Adm2_Name' => $record['Adm2_Name'],
                        'Adm3ID' => $record['Adm3ID'],
                        'Adm3_Name' => $record['Adm3_Name'],
                        'Adm4ID' => $record['Adm4ID'],
                        'Adm4_Name' => $record['Adm4_Name'],
                        'World' => $record['World'],
                        'Population' => (int) $record['Population'],
                        'Shape_Leng' => (float) $record['Shape_Leng'],
                        'Cen_x' => (float) $record['Cen_x'],
                        'Cen_y' => (float) $record['Cen_y'],
                        'Region' => $record['Region'],
                        'Field' => $record['Field'],
                        'Notes' => $record['Notes'],
                        'Last_Sync' => $record['Last_Sync'],
                        'Sync_Source' => $record['Sync_Source'],
                        'Source_Key' => $record['Source_Key']
                    ]
                ];
        }
    
        return [
            'status' => 'OK',
            'geojson' => $geojson,
        ];
        
        
    }
    
}
