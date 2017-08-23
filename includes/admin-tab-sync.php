<?php

/**
 * Locations_Tab_Settings
 *
 * @class   Locations_Tab_Settings
 * @version 1.0
 * @since   1.0
 * @package Locations
 * @author  Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class MM_Admin_Tab_Sync
{
    
    /**
     * Page content for the tab
     */
    public function page_contents()
    {
        if (isset( $_POST ) ) {
            if (isset( $_POST['run_script'] )) {
                $this->install_kml();
            }
        }
        $html = '';
        
        $html .= '<div class="wrap"><h2>Sync</h2>'; // Block title
        
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        $html .= '<form method="post"><button type="submit" name="run_script" value="true">Run Script</button></form>';
        
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */
        
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
        
        return $html;
        
    }
    
    public function install_kml () {
        global $wpdb;
        $table = 'wp_mm_usa_scratch';
        $ring = [];
        $error = '';
        
        $kml_object = simplexml_load_file( plugin_dir_path( __FILE__ ) . 'kml/cb_2016_us_county_500k.kml' ); // get xml from amazon
        
        foreach ($kml_object->Document->Folder->Placemark as $place) {
            
            $STATE = $place->ExtendedData->SchemaData->SimpleData[0];
            $COUNTY  = $place->ExtendedData->SchemaData->SimpleData[1];
            $NAME = $place->ExtendedData->SchemaData->SimpleData[5];
            
            if(mm_convert_usa_state_code ( $STATE )) { // tests if it is valid US state and not subterritory
    
                // Create the record array
                $WorldID = mm_convert_usa_state_code( $STATE ) . '-' . substr( strtoupper( $NAME ), 0, 2 ) . substr( $COUNTY, -1 );
                $Zone_Name = $NAME;
                $CntyID = 'USA';
                $Cnty_Name = 'United States of America';
                $Adm1ID = mm_convert_usa_state_code( $STATE );
                $Adm1_Name = mm_convert_usa_state_name( $STATE );
                $Adm2ID = mm_convert_usa_state_code( $STATE ) . '-' . substr( strtoupper( $NAME ), 0, 2 ) . substr( $COUNTY, -1 );
                $Adm2_Name = $NAME;
                $Adm3ID = '';
                $Adm3_Name = '';
                $Adm4ID = '';
                $Adm4_Name = '';
                $World = 'C';
                $Population = '';
                $Shape_Leng = '';
                $Cen_x = '';
                $Cen_y = '';
                $Region = 'North America Region';
                $Field = 'The Americas Field';
                $geometry = '';
                $OBJECTID_1 = '';
                $OBJECTID = '';
                $Notes = 'cb_2016_us_county_500k.kml';
                $Last_Sync = date( 'Y-m-d H:i:s' );
                $Sync_Source = 'US Census KML';
                
                $duplicate_check = $wpdb->get_var("SELECT WorldID FROM $table WHERE WorldID = '$WorldID'");
                if ( !is_null($duplicate_check) ) {
                    $last_digit = substr($WorldID, -1);
                    $last_digit++;
                    if($last_digit >= 10) {$last_digit = 1; }
                    $WorldID = substr($WorldID, 0, -1) . $last_digit;
                    $duplicate_check = $wpdb->get_var("SELECT WorldID FROM $table WHERE WorldID = '$WorldID'");
                    if ( !is_null($duplicate_check) ) {
                        $WorldID = mm_convert_usa_state_code( $STATE ) . '-' . substr( strtoupper( $NAME ), 0, 2 ) . substr( strtoupper( $NAME ), 4 );
                        $duplicate_check = $wpdb->get_var("SELECT WorldID FROM $table WHERE WorldID = '$WorldID'");
                        if ( !is_null($duplicate_check) ) {
                            $WorldID = mm_convert_usa_state_code( $STATE ) . '-' . substr( strtoupper( $NAME ), 0, 2 ) . substr( strtoupper( $NAME ), 5 );
                            $duplicate_check = $wpdb->get_var("SELECT WorldID FROM $table WHERE WorldID = '$WorldID'");
                            if ( !is_null($duplicate_check) ) {
                                $error = 'duplicate with ' . mm_convert_usa_state_code( $STATE ) . '-' . substr( strtoupper( $NAME ), 0, 2 ) . substr( $COUNTY, -1 ) . ' | ';
                            }
                        }
                    }
                }
    
    
                // Parse and create JSON coordinate record.
                if ( $place->Polygon ) {
                    $ring = [];
                    $polygon = [];
                    $values = explode( " ", $place->Polygon->outerBoundaryIs->LinearRing->coordinates );
                    foreach ( $values as $value ) {
                        $value = substr( $value, 0, -4 );
                        $coords = explode( ",", $value );
            
                        $polygon[] = $coords;
            
                    }
                    $ring[] = $polygon;
                }
                elseif ( $place->MultiGeometry ) {
                    $ring = [];
                    foreach ( $place->MultiGeometry->Polygon as $single_polygon ) {
                        $polygon = [];
                        $values = explode( " ", $single_polygon->outerBoundaryIs->LinearRing->coordinates );
                        foreach ( $values as $value ) {
                            $value = substr( $value, 0, -4 );
                            $coords = explode( ",", $value );
                
                            $polygon[] = $coords;
                
                        }
                        $ring[] = $polygon;
                    }
                }
    
                $geometry = json_encode( $ring, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK );
    
                $center = mm_find_center( $geometry );
                $Cen_x = $center[ 'Cen_x' ];
                $Cen_y = $center[ 'Cen_y' ];
    
                // Create SQL and insert statement
                $insert_sql = "
                REPLACE INTO $table
                (
                WorldID,
                Zone_Name,
                CntyID,
                Cnty_Name,
                Adm1ID,
                Adm1_Name,
                Adm2ID,
                Adm2_Name,
                Adm3ID,
                Adm3_Name,
                Adm4ID,
                Adm4_Name,
                World,
                Population,
                Shape_Leng,
                Cen_x,
                Cen_y,
                Region,
                Field,
                geometry,
                OBJECTID_1,
                OBJECTID,
                Notes,
                Last_Sync,
                Sync_Source
                )
                VALUES
                (
                '$WorldID',
                '$Zone_Name',
                '$CntyID',
                '$Cnty_Name',
                '$Adm1ID',
                '$Adm1_Name',
                '$Adm2ID',
                '$Adm2_Name',
                '$Adm3ID',
                '$Adm3_Name',
                '$Adm4ID',
                '$Adm4_Name',
                '$World',
                '$Population',
                '$Shape_Leng',
                '$Cen_x',
                '$Cen_y',
                '$Region',
                '$Field',
                '$geometry',
                '$OBJECTID_1',
                '$OBJECTID',
                '$Notes',
                '$Last_Sync',
                '$Sync_Source'
                )
                ";
    
                $wpdb->query( $insert_sql );
                
            }
            
        }
        
        print_r($wpdb->rows_affected . $wpdb->last_error); print $error;
        
    }
    
    public function load_button () {
        global $wpdb;
        $html = '';
    
        if ( !empty( $_POST[ 'oz_nonce' ] ) && isset( $_POST[ 'oz_nonce' ] ) && wp_verify_nonce( $_POST[ 'oz_nonce' ], 'oz_nonce_validate' ) ) {
        
            if ( !empty( $_POST[ 'sync-4k' ] ) ) {
            
                $result =  json_decode( file_get_contents( 'https://services1.arcgis.com/DnZ5orhsUGGdUZ3h/ArcGIS/rest/services/OmegaZones082016/FeatureServer/query?layerDefs={"0":"CntyID=\''.$_POST[ 'sync-4k' ].'\'"}&returnGeometry=true&f=pjson' ) );
            
                // build a parsing loop
                foreach($result->layers[0]->features as $item) {
                
                    // insert/update megazone table
                    $wpdb->update(
                        'omegazone_v1',
                        array(
                            'OBJECTID_1' => $item->attributes->OBJECTID_1,
                            'OBJECTID' => $item->attributes->OBJECTID,
                            'WorldID' => $item->attributes->WorldID,
                            'Zone_Name' => $item->attributes->Zone_Name,
                            'World' => $item->attributes->World,
                            'Adm4ID' => $item->attributes->Adm4ID,
                            'Adm3ID' => $item->attributes->Adm3ID,
                            'Adm2ID' => $item->attributes->Adm2ID,
                            'Adm1ID' => $item->attributes->Adm1ID,
                            'CntyID' => $item->attributes->CntyID,
                            'Adm4_Name' => $item->attributes->Adm4_Name,
                            'Adm3_Name' => $item->attributes->Adm3_Name,
                            'Adm2_Name' => $item->attributes->Adm2_Name,
                            'Adm1_Name' => $item->attributes->Adm1_Name,
                            'Cnty_Name' => $item->attributes->Cnty_Name,
                            'Population' => $item->attributes->Population,
                            'Shape_Leng' => $item->attributes->Shape_Leng,
                            'Cen_x' => $item->attributes->Cen_x,
                            'Cen_y' => $item->attributes->Cen_y,
                            'Region' => $item->attributes->Region,
                            'Field' => $item->attributes->Field,
                            'geometry' => json_encode( $item->geometry->rings ),
                        ),
                        array( 'WorldID' => $item->attributes->WorldID ),
                        array(
                            '%d',
                            '%d',
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
                        )
                    );
                
                    print '<br><br>Records updated: ' . $wpdb->rows_affected . ' | ' . $item->attributes->Cnty_Name;
                }
            }
        }
    
        $dir_contents =  dt_get_oz_country_list();
    
        $admin1 = '<select name="sync-4k" class="regular-text">';
        $admin1 .= '<option >- Choose</option>';
    
        foreach ( $dir_contents as $value ) {
        
            $admin1 .= '<option value="' . $value->CntyID . '" ';
            if ( isset( $_POST[ 'sync-4k' ] ) && $_POST[ 'sync-4k' ] == $value->CntyID  ) { $admin1 .= ' selected'; }
            $admin1 .= '>' . $value->Cnty_Name;
            $admin1 .= '</option>';
        }
    
        $admin1 .= '</select>';
        /* End load dropdown */
    
        $html .= '<table class="widefat ">
                    <thead><th>Sync 4K Data</th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true, false ) . $admin1 . '
                                    
                                    <button type="submit" class="button" value="submit">Sync 4k to omegazones_v1 table</button>
                                </form>
                                <br><a href="https://services1.arcgis.com/DnZ5orhsUGGdUZ3h/ArcGIS/rest/services/OmegaZones082016/FeatureServer/query">4K Query Server</a>
                            </td>
                        </tr>';
        $html .= '</tbody>
                </table>';
    
        return $html;
    }
    
}
