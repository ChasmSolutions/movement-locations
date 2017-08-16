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
     * Returns the tract geoid from an address
     *
     * @param  $address
     * @return array
     */
    public static function get_country_admin_1 ( $CntyID ) {
        global $wpdb;
        
        // query $CntyID and filter for admin1
        $data = $wpdb->get_results(
            $wpdb->prepare( "
                    SELECT * 
                    FROM $wpdb->mm
                    WHERE (`WorldID` LIKE '___'
                      OR `WorldID` LIKE '___-___')
                      AND `CntyID` = '%s'
                    ",
                $CntyID
            ),
            ARRAY_A
        );
    
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
                            json_decode($record['geometry'])
                        
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
        
//        $geojson =
//            [
//                'type' => 'FeatureCollection',
//                'features'  => [
//                    [
//                        'type' => 'Feature',
//                        'geometry' => [
//                            'type' => 'Polygon',
//                            'coordinates' => [
//                                [
//                                    [100.0, 0.0],
//                                    [101.0, 0.0],
//                                    [101.0, 1.0],
//                                    [100.0, 1.0],
//                                    [100.0, 0.0]
//                                ]
//                            ]
//                        ],
//                        'properties' => [
//                            'WorldID' => (string) $CntyID,
//                            'Zone_Name' => '0',
//                            'CntyID' => '0',
//                            'Cnty_Name' => '0',
//                            'Adm1ID' => '0',
//                            'Adm1_Name' => '0',
//                            'Adm2ID' => '0',
//                            'Adm2_Name' => '0',
//                            'Adm3ID' => '0',
//                            'Adm3_Name' => '0',
//                            'Adm4ID' => '0',
//                            'Adm4_Name' => '0',
//                            'World' => '0',
//                            'Population' => '0',
//                            'Shape_Leng' => '0',
//                            'Cen_x' => '0',
//                            'Cen_y' => '0',
//                            'Region' => '0',
//                            'Field' => '0',
//                            'geometry' => '0',
//                            'OBJECTID_1' => '0',
//                            'OBJECTID' => '0',
//                            'Notes' => '0',
//                            'Last_Sync' => '0',
//                            'Sync_Source' => '0'
//                        ]
//                    ],
//                    [
//                        'type' => 'Feature',
//                        'geometry' => [
//                            'type' => 'Polygon',
//                            'coordinates' => [
//                                [
//                                    [100.0, 0.0],
//                                    [101.0, 0.0],
//                                    [101.0, 1.0],
//                                    [100.0, 1.0],
//                                    [100.0, 0.0]
//                                ]
//                            ]
//                        ],
//                        'properties' => [
//                            'WorldID' => (string) $CntyID,
//                            'Zone_Name' => '0',
//                            'CntyID' => '0',
//                            'Cnty_Name' => '0',
//                            'Adm1ID' => '0',
//                            'Adm1_Name' => '0',
//                            'Adm2ID' => '0',
//                            'Adm2_Name' => '0',
//                            'Adm3ID' => '0',
//                            'Adm3_Name' => '0',
//                            'Adm4ID' => '0',
//                            'Adm4_Name' => '0',
//                            'World' => '0',
//                            'Population' => '0',
//                            'Shape_Leng' => '0',
//                            'Cen_x' => '0',
//                            'Cen_y' => '0',
//                            'Region' => '0',
//                            'Field' => '0',
//                            'geometry' => '0',
//                            'OBJECTID_1' => '0',
//                            'OBJECTID' => '0',
//                            'Notes' => '0',
//                            'Last_Sync' => '0',
//                            'Sync_Source' => '0'
//                        ]
//                    ]
//
//                ]
//
//            ];
        
        return [
            'status' => 'OK',
            'geojson' => $geojson,
        ];
    }
    
}
