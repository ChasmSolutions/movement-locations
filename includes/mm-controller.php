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
    public static function get_country_by_level ( $CntyID, $level ) {
        global $wpdb;
        $where = '';
        
        switch ($level) {
            case '0':
                $where = "WHERE `WorldID` LIKE '___' AND `CntyID` = '$CntyID'";
                break;
            case '1':
                $where = "WHERE `WorldID` LIKE '___-___' AND `CntyID` = '$CntyID'";
                break;
            case '2':
                $where = "WHERE `WorldID` LIKE '___-___-___' AND `CntyID` = '$CntyID'";
                break;
            case '3':
                $where = "WHERE `WorldID` LIKE '___-___-___-___' AND `CntyID` = '$CntyID'";
                break;
            case '4':
                $where = "WHERE `WorldID` LIKE '___-___-___-___-___' AND `CntyID` = '$CntyID'";
                break;
            default:
                break;
        }
        
        // query $CntyID and filter for admin1
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
                        'Population' => $record['Population'],
                        'Shape_Leng' => $record['Shape_Leng'],
                        'Cen_x' => $record['Cen_x'],
                        'Cen_y' => $record['Cen_y'],
                        'Region' => $record['Region'],
                        'Field' => $record['Field'],
                        'OBJECTID_1' => $record['OBJECTID_1'],
                        'Notes' => $record['Notes'],
                        'Last_Sync' => $record['Last_Sync'],
                        'Sync_Source' => $record['Sync_Source']
                    ]
                ];
        }
        
        return [
            'status' => 'OK',
            'geojson' => $geojson,
        ];
    }
    
}
