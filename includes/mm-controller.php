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
    public static function get_country_admin_1 ( $WorldID ) {
        
        $geojson =
            [
                'type' => 'FeatureCollection',
                'features'  => [
                    [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'Polygon',
                            'coordinates' => [
                                [
                                    [100.0, 0.0],
                                    [101.0, 0.0],
                                    [101.0, 1.0],
                                    [100.0, 1.0],
                                    [100.0, 0.0]
                                ]
                            ]
                        ],
                        'properties' => [
                            'WorldID' => (string) $WorldID,
                            'Zone_Name' => '0',
                            'CntyID' => '0',
                            'Cnty_Name' => '0',
                            'Adm1ID' => '0',
                            'Adm1_Name' => '0',
                            'Adm2ID' => '0',
                            'Adm2_Name' => '0',
                            'Adm3ID' => '0',
                            'Adm3_Name' => '0',
                            'Adm4ID' => '0',
                            'Adm4_Name' => '0',
                            'World' => '0',
                            'Population' => '0',
                            'Shape_Leng' => '0',
                            'Cen_x' => '0',
                            'Cen_y' => '0',
                            'Region' => '0',
                            'Field' => '0',
                            'geometry' => '0',
                            'OBJECTID_1' => '0',
                            'OBJECTID' => '0',
                            'Notes' => '0',
                            'Last_Sync' => '0',
                            'Sync_Source' => '0'
                        ]
                    ],
                    [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'Polygon',
                            'coordinates' => [
                                [
                                    [100.0, 0.0],
                                    [101.0, 0.0],
                                    [101.0, 1.0],
                                    [100.0, 1.0],
                                    [100.0, 0.0]
                                ]
                            ]
                        ],
                        'properties' => [
                            'WorldID' => (string) $WorldID,
                            'Zone_Name' => '0',
                            'CntyID' => '0',
                            'Cnty_Name' => '0',
                            'Adm1ID' => '0',
                            'Adm1_Name' => '0',
                            'Adm2ID' => '0',
                            'Adm2_Name' => '0',
                            'Adm3ID' => '0',
                            'Adm3_Name' => '0',
                            'Adm4ID' => '0',
                            'Adm4_Name' => '0',
                            'World' => '0',
                            'Population' => '0',
                            'Shape_Leng' => '0',
                            'Cen_x' => '0',
                            'Cen_y' => '0',
                            'Region' => '0',
                            'Field' => '0',
                            'geometry' => '0',
                            'OBJECTID_1' => '0',
                            'OBJECTID' => '0',
                            'Notes' => '0',
                            'Last_Sync' => '0',
                            'Sync_Source' => '0'
                        ]
                    ]
                    
                ]
                
            ];
        
        return [
            'status' => 'OK',
            'geojson' => $geojson,
        ];
    }
    
}
