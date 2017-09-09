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

class MM_Admin_Tab_Import
{
    private $table;
    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
        $this->table = 'wp_mm_usa';
        
    } // End __construct()
    
    
    /**
     * Page content for the tab
     */
    public function page_contents()
    {
        global $wpdb;
        // Catch and process post submission
        if (isset( $_POST ) ) {
            if (isset( $_POST['install_usa_0'] )) {
                $this->install_usa_nation_kml( );
            }
            if (isset( $_POST['install_usa_1'] )) {
                $this->install_usa_state_kml( );
            }
            if (isset( $_POST['install_usa_2'] )) {
                $this->install_usa_county_kml( );
            }
            if ( isset( $_POST[ 'states-dropdown' ] ) ) { // check if file is correctly set
                $this->install_usa_tracts_kml( $_POST[ 'states-dropdown' ] );
            }
            if ( isset( $_POST[ 'install_gdam_2' ] ) ) { // check if file is correctly set
                $this->install_gdam_adm2_kml( );
            }
        }
        
        // Build HTML for Page
        $html = '';
        $html .= '<div class="wrap"><h2>Import</h2>'; // Block title
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        $html .= '<table class="widefat striped"><thead><th>USA Installers</th><th></th><th></th></thead><tbody>';
        
        // US Counties Installer
        $html .= '<tr><th><form method="post"><button type="submit" class="button" name="install_usa_0" value="cb_2016_us_state_500k.kml">Install USA Nation (Admin0)</button></form></th><td>This installs Admin0 from US Census.gov provided KML.</td><td></td></tr>';
        $html .= '<tr><th><form method="post"><button type="submit" class="button" name="install_usa_1" value="cb_2016_us_state_500k.kml">Install USA States (Admin1)</button></form></th><td>This installs Admin1 from US Census.gov provided KML.</td><td></td></tr>';
        $html .= '<tr><th><form method="post"><button type="submit" class="button" name="install_usa_2" value="cb_2016_us_county_500k.kml">Install USA Counties (Admin2)</button></form></th><td>This installs Admin2 from US Census.gov provided KML.</td><td></td></tr>';
        $html .= '<tr><th><form method="post">'.$this->mm_get_states_key_dropdown_not_installed().' <button type="submit" class="button" name="install_usa_3">Install USA Tracts (Admin3)</button></form></th><td>This installs Admin3 from US Census.gov provided KML.</td><td></td></tr>';
    
        $html .= '</tbody></table><br>';
    
        $html .= '<table class="widefat striped"><thead><th>GDAM Installer</th><th></th><th></th></thead><tbody>';
        $html .= '<tr><th><form method="post"><button type="submit" class="button" name="install_gdam_2" value="true" >Install GDAM Admin2</button></form></th><td>Add KML file, select admin level, submit</td><td></td></tr>';
        $html .= '</tbody></table>';
    
//        $oz_record = json_decode( file_get_contents( "https://services1.arcgis.com/DnZ5orhsUGGdUZ3h/ArcGIS/rest/services/OmegaZones082016/FeatureServer/0/query?outFields=*&returnGeometry=true&resultRecordCount=1&f=pgeojson&where=WorldID='TUN-TAT'" ), true);
//        $oz_record = json_encode($oz_record, JSON_PRESERVE_ZERO_FRACTION, 13);
////        $oz_record = json_decode($oz_record);
//        print '<pre>'; print_r($oz_record); print '</pre>';
        
        //$html .= '<table class="widefat striped"><thead><th>GDAM Installer</th><th></th><th></th></thead><tbody>';
        //$html .= '<tr><th><form method="post">'.$this->select_oz_data_dropdown().'</form></th><td>GDAM is a source for geometry for political administrative units. <a href="http://gdam.org">GDAM Website</a></td><td></td></tr>';
        //$html .= '</tbody></table>';
        
        
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
        
        return $html;
        
    }
    
    /**
     * Install USA nation from static data
     */
    public function install_usa_nation_kml () {
        // SET VARIABLES
        $kml = 'cb_2016_us_nation_5m.kml';
        global $wpdb;
        $table = $this->table;
        $Last_Sync = date( 'Y-m-d H:i:s', time() );
        
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
        Notes,
        Last_Sync,
        Sync_Source,
        Source_Key
        )
        VALUES
        (
        'USA',
        'United States of America',
        'USA',
        'United States of America',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        'C',
        '',
        '',
        '39.8603611',
        '-98.7587165',
        'North America Region',
        'The Americas Field',
        '',
        '',
        '$Last_Sync',
        'Movement Mapping Plugin',
        ''
        )
        ";
        
        $wpdb->query( $insert_sql );
        
        print_r( $wpdb->rows_affected . $wpdb->last_error );
        
    }
    
    /**
     * Install USA states from KML
     */
    public function install_usa_state_kml () {
        // SET VARIABLES
        $kml = 'cb_2016_us_state_500k.kml';
        global $wpdb;
        $table = $this->table;
        $ring = [];
        $error = '';
        
        // GET SOURCE
        $kml_object = simplexml_load_file( plugin_dir_path( __FILE__ ) . 'kml/' . $kml ); // get xml from amazon
        
        // PARSE AND INSERT SOURCE
        foreach ($kml_object->Document->Folder->Placemark as $place) {
            
            $STATE = $place->ExtendedData->SchemaData->SimpleData[0];
            $GEOID = $place->ExtendedData->SchemaData->SimpleData[3];
            
            if(mm_convert_usa_state_code( $STATE )) { // tests if it is valid US state and not subterritory
                
                // Create the record array
                $WorldID = mm_convert_usa_state_code( $STATE );
                $Zone_Name =  mm_convert_usa_state_name( $STATE );
                $CntyID = 'USA';
                $Cnty_Name = 'United States of America';
                $Adm1ID = mm_convert_usa_state_code( $STATE );
                $Adm1_Name = mm_convert_usa_state_name( $STATE );
                $Adm2ID = '';
                $Adm2_Name = '';
                $Adm3ID = '';
                $Adm3_Name = '';
                $Adm4ID = '';
                $Adm4_Name = '';
                $World = 'C';
                $Population = '';
                $Shape_Leng = '';
                $Cen_x = ''; // added later
                $Cen_y = ''; // added later
                $Region = 'North America Region';
                $Field = 'The Americas Field';
                $geometry = ''; // added later
                $Notes = $kml;
                $Last_Sync = date( 'Y-m-d H:i:s', time() );
                $Sync_Source = 'US Census KML';
                $Source_Key = $GEOID;
                
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
                Notes,
                Last_Sync,
                Sync_Source,
                Source_Key
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
                '$Notes',
                '$Last_Sync',
                '$Sync_Source',
                '$Source_Key'
                )
                ";
                
                $wpdb->query( $insert_sql );
                
            }
            
        }
        
        print_r( $wpdb->rows_affected . $wpdb->last_error );
        print $error;
        
    }
    
    /**
     * Install USA states from KML
     */
    public function install_usa_county_kml () {
        // SET VARIABLES
        // $kml value provided in the button value.
        $kml = 'cb_2016_us_county_500k.kml';
        global $wpdb;
        $table = $this->table;
        $ring = [];
        $error = '';
        
        // GET SOURCE
        $kml_object = simplexml_load_file( plugin_dir_path( __FILE__ ) . 'kml/' . $kml ); // get xml from amazon
        
        // PARSE AND INSERT SOURCE
        foreach ($kml_object->Document->Folder->Placemark as $place) {
            
            $STATE = $place->ExtendedData->SchemaData->SimpleData[0];
            $COUNTY  = $place->ExtendedData->SchemaData->SimpleData[1];
            $NAME = $place->ExtendedData->SchemaData->SimpleData[5];
            $GEOID = $place->ExtendedData->SchemaData->SimpleData[4];
            
            if(mm_convert_usa_state_code( $STATE )) { // tests if it is valid US state and not subterritory
                
                // Create the record array
                $WorldID = mm_convert_usa_state_code( $STATE ) . '-' . substr( strtoupper( $NAME ), 0, 2 ) . substr( $COUNTY, -1 );
                $Zone_Name = esc_attr($NAME);
                $CntyID = 'USA';
                $Cnty_Name = 'United States of America';
                $Adm1ID = mm_convert_usa_state_code( $STATE );
                $Adm1_Name = esc_attr(mm_convert_usa_state_name( $STATE ));
                $Adm2ID = '';
                $Adm2_Name = esc_attr($NAME);
                $Adm3ID = '';
                $Adm3_Name = '';
                $Adm4ID = '';
                $Adm4_Name = '';
                $World = 'C';
                $Population = '';
                $Shape_Leng = '';
                $Cen_x = ''; // added later
                $Cen_y = ''; // added later
                $Region = esc_attr('North America Region');
                $Field = esc_attr('The Americas Field');
                $geometry = ''; // added later
                $Notes = $kml;
                $Last_Sync = date( 'Y-m-d H:i:s', time() );
                $Sync_Source = esc_attr('US Census KML');
                $Source_Key = $GEOID;
                
                /**
                 * Cascading duplicate check
                 * Issue: The creation of the WorldID key has duplicates challenges. So this is a cascading series of three renameings to find an alternate WorldID key.
                 * It tries to increment the final number of the WorldID first, then it searches for an alternate alpha character from the name two times, and then it fails and populates the $error variable.
                 */
                $duplicate_check = $wpdb->get_var( "SELECT WorldID FROM $table WHERE WorldID = '$WorldID'" ); // check if WorldID already exists
                if ( !is_null( $duplicate_check ) ) { // if WorldID exists in the database
                    // check if previously installed
                    $duplicate_check = $wpdb->get_var( "SELECT Source_Key FROM $table WHERE Source_Key = '$GEOID' AND WorldID = '$WorldID'" ); // if WorldID already exists, check if this record is the same source_key (geoid)
                    if ( is_null( $duplicate_check ) ) { // if the worldid is taken in the previous if, but the worldid and geoid combination are not found, then we need to find a new worldid
                        $last_digit = substr( $WorldID, -1 );
                        $last_digit++;
                        if($last_digit >= 10) {$last_digit = 1; }
                        $WorldID = substr( $WorldID, 0, -1 ) . $last_digit;
                        $duplicate_check = $wpdb->get_var( "SELECT WorldID FROM $table WHERE WorldID = '$WorldID' " );
                        if ( !is_null( $duplicate_check ) ) { // if WorldID exists in the database
                            // check if previously installed
                            $duplicate_check = $wpdb->get_var( "SELECT Source_Key FROM $table WHERE Source_Key = '$GEOID' AND WorldID = '$WorldID'" ); // if WorldID already exists, check if this record is the same source_key (geoid)
                            if ( is_null( $duplicate_check ) ) { // if the worldid is taken in the previous if, but the worldid and geoid combination are not found, then we need to find a new worldid
                                $WorldID = mm_convert_usa_state_code( $STATE ) . '-' . substr( strtoupper( $NAME ), 0, 2 ) . substr( strtoupper( $NAME ), 3, 1 );
                                $duplicate_check = $wpdb->get_var( "SELECT WorldID FROM $table WHERE WorldID = '$WorldID' " );
                                if ( !is_null( $duplicate_check ) ) { // if WorldID exists in the database
                                    // check if previously installed
                                    $duplicate_check = $wpdb->get_var( "SELECT Source_Key FROM $table WHERE Source_Key = '$GEOID' AND WorldID = '$WorldID'" ); // if WorldID already exists, check if this record is the same source_key (geoid)
                                    if ( is_null( $duplicate_check ) ) { // if the worldid is taken in the previous if, but the worldid and geoid combination are not found, then we need to find a new worldid
                                        $WorldID = mm_convert_usa_state_code( $STATE ) . '-' . substr( strtoupper( $NAME ), 0, 2 ) . substr( strtoupper( $NAME ), 4, 1 );
                                        $duplicate_check = $wpdb->get_var( "SELECT WorldID FROM $table WHERE WorldID = '$WorldID' " );
                                        if ( !is_null( $duplicate_check ) ) {
                                            $error = ' Duplicate with ' . mm_convert_usa_state_code( $STATE ) . '-' . substr( strtoupper( $NAME ), 0, 2 ) . substr( $COUNTY, -1 ) . ' | ';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $Adm2ID = $WorldID;
                
                
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
                Notes,
                Last_Sync,
                Sync_Source,
                Source_Key
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
                '$Notes',
                '$Last_Sync',
                '$Sync_Source',
                '$Source_Key'
                )
                ";
                
                $wpdb->query( $insert_sql );
                
            }
            
        }
        
        print_r( $wpdb->rows_affected . $wpdb->last_error );
        print $error;
        
    }
    
    /**
     * Install USA states from KML
     */
    public function install_usa_tracts_kml ( $STATE ) {
        // SET VARIABLES
        global $wpdb;
        $table = $this->table;
        $ring = [];
        $error = '';
        
        // GET SOURCE
        $directory = $this->mm_get_usa_meta(); // get directory;
        $file = $directory->USA_states->{$STATE}->file;
    
        $kml_object = simplexml_load_file( $directory->base_url . $file ); // get xml from amazon
        
        $i = 'A01';
        
        
        // PARSE AND INSERT SOURCE
        foreach ($kml_object->Document->Folder->Placemark as $place) {
    
            // Parse KML
            $STATE = $place->ExtendedData->SchemaData->SimpleData[ 0 ];
            $COUNTY = $place->ExtendedData->SchemaData->SimpleData[ 1 ];
            $TRACT = $place->ExtendedData->SchemaData->SimpleData[ 2 ];
            $GEOID = $place->ExtendedData->SchemaData->SimpleData[ 4 ];
            $NAME = $place->ExtendedData->SchemaData->SimpleData[ 5 ];
    
            // Lookup County WorldID Code by GEOID
            $COUNTY_GEOID = $STATE . $COUNTY;
            $COUNTY_WORLDID = $wpdb->get_results( "SELECT WorldID, Zone_Name FROM $table WHERE Source_Key = '$COUNTY_GEOID'", ARRAY_A );
            
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
            } // end if coordinate build
    
            $geometry = json_encode( $ring, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK );
            
            // Find center coordinates
            $center = mm_find_center( $geometry );
            $Cen_x = $center[ 'Cen_x' ];
            $Cen_y = $center[ 'Cen_y' ];
            

            // Create the record array
            $WorldID = $COUNTY_WORLDID[0]['WorldID'] . "-" . $i;
            $Zone_Name = esc_attr("Tract " . $TRACT . " of " . $COUNTY_WORLDID[0]['Zone_Name'] . ' County');
            $CntyID = 'USA';
            $Cnty_Name = 'United States of America';
            $Adm1ID = mm_convert_usa_state_code( $STATE );
            $Adm1_Name = esc_attr(mm_convert_usa_state_name( $STATE ));
            $Adm2ID = $COUNTY_WORLDID[0]['WorldID'];
            $Adm2_Name = esc_attr($COUNTY_WORLDID[0]['Zone_Name']);
            $Adm3ID = ''; // added later
            $Adm3_Name = ''; // added later
            $Adm4ID = '';
            $Adm4_Name = '';
            $World = 'C';
            $Population = '';
            $Shape_Leng = '';
            $Region = esc_attr('North America Region');
            $Field = esc_attr('The Americas Field');
            $Notes = $file;
            $Last_Sync = date( 'Y-m-d H:i:s', time() );
            $Sync_Source = esc_attr('US Census KML');
            $Source_Key = $GEOID;


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
            Notes,
            Last_Sync,
            Sync_Source,
            Source_Key
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
            '$Notes',
            '$Last_Sync',
            '$Sync_Source',
            '$Source_Key'
            )
            ";

            $wpdb->query( $insert_sql );
            
            $i++;

        } // end if state
        
        
        print_r( $wpdb->rows_affected . $wpdb->last_error );
        print $error;
        
    }
    
    /**
     * Creates drop down for uploading state xml files
     * @return mixed
     */
    public function select_us_census_data_dropdown()
    {
        $html = '';
        $result = '';
        $result2 = '';
        
        // check if $_POST to change option
        if ( !empty( $_POST[ 'state_nonce' ] ) && isset( $_POST[ 'state_nonce' ] ) && wp_verify_nonce( $_POST[ 'state_nonce' ], 'state_nonce_validate' ) ) {
            
            if ( !isset( $_POST[ 'states-dropdown' ] ) ) { // check if file is correctly set
                return false;
            }
            
            $result = Disciple_Tools_Locations_Import::upload_census_tract_kml_to_post_type( $_POST[ 'states-dropdown' ] ); // run insert process TODO make this a javascript call with a spinner.
            $result2 = Disciple_Tools_Locations_Import::upload_us_state_tracts( $_POST[ 'states-dropdown' ] );
            
        } /* end if $_POST */
        
        $dropdown = $this->mm_get_states_key_dropdown_installed();
        
        // return form and dropdown
        
        $html .= '<table class="widefat ">
                    <thead><th>Zume Project - USA Census Data </th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'state_nonce_validate', 'state_nonce', true, false ) . $dropdown . '
                                    
                                    <button type="submit" class="button" value="submit">Upload State</button>
                                </form>
                            </td>
                        </tr>';
        
        
        if ( !empty( $result ) || !empty( $result2 ) ) {
            $html .= '<tr>
                            <td>State Counties: ' . $result . '<br>State Tracts: ' . $result2 . '</td>
                      </tr>';
        }
        
        $html .= '</tbody>
                </table>';
        
        return $html;
    }
    
    /**
     * Creates a dropdown of the states with the state key as the value.
     * @usage USA locations
     *
     * @return string
     */
    function mm_get_states_key_dropdown_installed () {
        
        $dir_contents = $this->mm_get_usa_meta(); // get directory & build dropdown
        
        $dropdown = '<select name="states-dropdown">';
        
        foreach ($dir_contents->USA_states as $value) {
            $disabled = '';
            
            $dropdown .= '<option value="' . $value->key . '" ';
            if (!get_option( '_installed_us_county_'.$value->key )) {$dropdown .= ' disabled';
                $disabled = ' (Not Installed)';}
            elseif (isset( $_POST['states-dropdown'] ) && $_POST['states-dropdown'] == $value->key) {$dropdown .= ' selected';}
            $dropdown .= '>' . $value->name . $disabled;
            $dropdown .= '</option>';
        }
        $dropdown .= '</select>';
        
        return $dropdown;
    }
    
    /**
     * Creates a dropdown of the states with the state key as the value.
     * @usage USA locations
     *
     * @return string
     */
    function mm_get_states_key_dropdown_not_installed () {
        
        $dir_contents = $this->mm_get_usa_meta();
        
        $dropdown = '<select name="states-dropdown">';
        
        foreach ($dir_contents->USA_states as $value) {
            $disabled = '';
            
            $dropdown .= '<option value="' . $value->key . '" ';
            if (get_option( '_installed_us_county_'.$value->key )) {$dropdown .= ' disabled';
                $disabled = ' (Installed)';}
            elseif (isset( $_POST['states-dropdown'] ) && $_POST['states-dropdown'] == $value->key) {$dropdown .= ' selected';}
            $dropdown .= '>' . $value->name . $disabled;
            $dropdown .= '</option>';
        }
        $dropdown .= '</select>';
        
        return $dropdown;
    }
    
    /**
     * Get the master json file with USA states and counties names, ids, and file locations.
     * @usage USA locations
     *
     * @return array|mixed|object
     */
    function mm_get_usa_meta() {
        return json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/usa-meta.json' ) );
    }
    
    
    public function load_button () { // todo hardwired development function.
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
    
        $dir_contents =  $this->mm_get_oz_country_list();
    
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
    
    
    /**
     * Creates drop down meta box for loading Omega Zone files
     * @return mixed
     */
    public function select_oz_data_dropdown()
    {
        
        /*********************************/
        /* POST */
        /*********************************/
        if ( !empty( $_POST[ 'oz_nonce' ] ) && isset( $_POST[ 'oz_nonce' ] ) && wp_verify_nonce( $_POST[ 'oz_nonce' ], 'oz_nonce_validate' ) ) {
            
            if( !empty( $_POST[ 'load-oz-admin1' ] ) ) {
                
                // insert records
                $import = new Disciple_Tools_Locations_Import();
                $import->insert_location_oz( $_POST[ 'load-oz-admin1' ], 'admin1' );
                
                // Update option.
                $option = get_option( '_mm_oz_installed' );
                $option['Adm1ID'][] = $_POST[ 'load-oz-admin1' ];
                update_option( '_mm_oz_installed', $option );
                
            }
            
            if( !empty( $_POST[ 'load-oz-admin2' ] ) ) {
                
                // insert records
                $import = new Disciple_Tools_Locations_Import();
                $import->insert_location_oz( $_POST[ 'load-oz-admin2' ], 'admin2' );
                
                // Update option.
                $option = get_option( '_mm_oz_installed' );
                $option['Adm2ID'][] = $_POST[ 'load-oz-admin2' ];
                update_option( '_mm_oz_installed', $option );
                
            }
            
            if( !empty( $_POST[ 'load-oz-admin3' ] ) ) {
                
                // insert records
                $import = new Disciple_Tools_Locations_Import();
                $import->insert_location_oz( $_POST[ 'load-oz-admin3' ], 'admin3' );
                
                // Update option.
                $option = get_option( '_mm_oz_installed' );
                $option['Adm3ID'][] = $_POST[ 'load-oz-admin3' ];
                update_option( '_mm_oz_installed', $option );
            }
            
            if( !empty( $_POST[ 'load-oz-admin4' ] ) ) {
                
                // insert records
                $import = new Disciple_Tools_Locations_Import();
                $import->insert_location_oz( $_POST[ 'load-oz-admin4' ], 'admin4' );
                
                // Update option.
                $option = get_option( '_mm_oz_installed' );
                $option['Adm4ID'][] = $_POST[ 'load-oz-admin4' ];
                update_option( '_mm_oz_installed', $option );
            }
        }
        /* End POST */
        
        /*********************************/
        /* Load or Create Options        */
        /*********************************/
        if( get_option( '_mm_oz_installed' ) ) {
            $currently_installed = get_option( '_mm_oz_installed' );
        } else {
            $currently_installed = [
                'Adm1ID' => [ ],
                'Adm2ID' => [ ],
                'Adm3ID' => [ ],
                'Adm4ID' => [ ],
            ];
            add_option( '_mm_oz_installed', $currently_installed, '', false );
        }
        
        
        /*********************************/
        /* Begin Admin 1 Create Dropdown */
        /*********************************/
        
        $dir_contents =  $this->mm_get_oz_country_list();
        
        $admin1 = '<select name="load-oz-admin1" class="regular-text">';
        $admin1 .= '<option >- Choose</option>';
        
        foreach ( $dir_contents as $value ) {
            $test = array_search( $value->CntyID , $currently_installed[ 'Adm1ID' ] );
            if ( !($test) && !($test === 0)) {
                $admin1 .= '<option value="' . $value->CntyID . '" ';
                $admin1 .= '>' . $value->Cnty_Name;
                $admin1 .= '</option>';
            }
        }
        
        $admin1 .= '</select>';
        /* End load dropdown */
        
        /*********************************/
        /* Begin Admin 2 Create Dropdown */
        /*********************************/
        $admin2 = '<select name="load-oz-admin2" class="regular-text">';
        
        if(!empty( $currently_installed['Adm1ID'] ) && isset( $currently_installed['Adm1ID'] )) {
            
            $admin2 .= '<option>- Choose</option>';
            
            foreach ( $currently_installed['Adm1ID'] as $value ) {
                $test = array_search( $value , $currently_installed[ 'Adm2ID' ] );
                if ( !($test) && !($test === 0)) {
                    $admin2 .= '<option value="' . $value . '" ';
                    $admin2 .= '>' . dt_locations_match_country_to_key( $value );
                    $admin2 .= '</option>';
                }
            }
        } else {
            $admin2 .= '<option selected>- Unavailable</option>';
        }
        
        $admin2 .= '</select>';
        /* End Admin 1 Create Dropdown */
        
        /*********************************/
        /* Begin Admin 3 Create Dropdown */
        /*********************************/
        $admin3 = '<select name="load-oz-admin3" class="regular-text">';
        
        if(!empty( $currently_installed['Adm2ID'] ) && isset( $currently_installed['Adm2ID'] )) {
            
            $admin3 .= '<option>- Choose</option>';
            
            foreach ( $currently_installed['Adm2ID'] as $value ) {
                $test = array_search( $value , $currently_installed[ 'Adm3ID' ] );
                if ( !($test) && !($test === 0)) {
                    $admin3 .= '<option value="' . $value . '" ';
                    $admin3 .= '>' . dt_locations_match_country_to_key( $value );
                    $admin3 .= '</option>';
                }
            }
        } else {
            $admin3 .= '<option selected>- Unavailable</option>';
        }
        
        $admin3 .= '</select>';
        /* End Admin 2 Create Dropdown */
        
        /*********************************/
        /* Begin Admin 4 Create Dropdown */
        /*********************************/
        $admin4 = '<select name="load-oz-admin4" class="regular-text">';
        
        if(!empty( $currently_installed['Adm3ID'] ) && isset( $currently_installed['Adm3ID'] )) {
            
            $admin4 .= '<option>- Choose</option>';
            
            foreach ( $currently_installed['Adm3ID'] as $value ) {
                $test = array_search( $value , $currently_installed[ 'Adm4ID' ] );
                if ( !($test) && !($test === 0)) {
                    $admin4 .= '<option value="' . $value . '" ';
                    $admin4 .= '>' . dt_locations_match_country_to_key( $value );
                    $admin4 .= '</option>';
                } else {
                    $admin4 .= '<option value="' . $value . '" disabled';
                    $admin4 .= '>' . dt_locations_match_country_to_key( $value ) . ' (Installed)';
                    $admin4 .= '</option>';
                }
            }
        } else {
            $admin4 .= '<option selected>- Unavailable</option>';
        }
        
        $admin4 .= '</select>';
        /* End Admin 3 Create Dropdown */
        
        /*********************************/
        /* Build form box                */
        /*********************************/
        $html = '';
        $html .= '<table class="widefat ">
                    <thead><th>Import Omega Zones/2414/Zume International</th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true, false ) . $admin1 . '
                                    
                                    <button type="submit" class="button" value="submit">Load Admin Level 1</button>
                                </form>
                            </td>
                        </tr>';
        $html .=        '<tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true, false ) . $admin2 . '
                                    
                                    <button type="submit" class="button" value="submit">Load Admin Level 2</button>
                                </form>
                            </td>
                        </tr>';
        $html .=        '<tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true, false ) . $admin3 . '
                                    
                                    <button type="submit" class="button" value="submit">Load Admin Level 3</button>
                                </form>
                            </td>
                        </tr>';
        $html .=        '<tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true, false ) . $admin4 . '
                                    
                                    <button type="submit" class="button" value="submit">Load Admin Level 4</button>
                                </form>
                            </td>
                        </tr>';
        
        $html .= '</tbody>
                </table>';
        /* End Build Form Box */
        
        return $html;
    }
    
    /**
     * Get the master list of countries for omega zones including country abbreviation, country name, and zone.
     * @return array|mixed|object
     */
    public function mm_get_oz_country_list( $admin = 'cnty' ) {
        
        switch ( $admin ) {
            case 'cnty':
                $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_cnty.json' ) );
                return $result->RECORDS;
                break;
            case 'admin1':
                $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_admin1.json' ) );
                return $result->RECORDS;
                break;
            case 'admin2':
                $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_admin2.json' ) );
                return $result->RECORDS;
                break;
            case 'admin3':
                $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_admin3.json' ) );
                return $result->RECORDS;
                break;
            case 'admin4':
                $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_admin4.json' ) );
                return $result->RECORDS;
                break;
            default:
                break;
        }
        
        return false;
    }
    
    /**
     * Import Omega Zone locations
     * @param $admin
     */
    public function insert_location_oz( $cntyID, $admin ) {
        
        $list = $this->mm_get_oz_country_list( $admin );
        $parent_postID = '';
        
        // Install single top level country record for Admin1 only
        if ( $admin == 'admin1') {
            
            $country_list = $this->mm_get_oz_country_list( 'cnty' );
            $country_name = '';
            $country_id = '';
            
            foreach ( $country_list as $value ) {
                if ( $value->CntyID == $cntyID) {
                    $country_name = $value->Zone_Name;
                    $country_id = $value->WorldID;
                    break;
                }
            }
            
            if ( !empty( $country_name ) || !empty( $country_id )) {
                $post = [
                    "post_title" => $country_name . ' ( ' . $country_id . ' )',
                    'post_type' => 'locations',
                    "post_content" => '',
                    "post_excerpt" => '',
                    "post_name" => $country_id,
                    "post_status" => "publish",
                    "post_author" => get_current_user_id(),
                ];
                
                $parent_postID = wp_insert_post( $post );
            }
            
        }
        
        // Loop the admin level list
        foreach ($list as $item ) {
            
            if ( $item->CntyID == $cntyID ) {
                
                $content = '';
                
                foreach ( $item as $key => $value ) {
                    $content .= $key . ': ' . $value . '<br>';
                }
                
                $post = [
                    "post_title" => $item->Zone_Name . ' ( ' . $item->WorldID . ' )',
                    'post_type' => 'locations',
                    "post_content" => $content,
                    "post_excerpt" => '',
                    "post_parent" => $parent_postID,
                    "post_name" => $item->WorldID,
                    "post_status" => "publish",
                    "post_author" => get_current_user_id(),
                ];
                
                $new_post_id = wp_insert_post( $post );
                
            }
        }
    }
    
    public function install_gdam_adm2_kml() {
        // SET VARIABLES
        $kml = 'TUN_Adm2.kml';
        global $wpdb;
        $table = $wpdb->mm;
        $ring = [];
        $error = '';
    
        // GET SOURCE
        $kml_object = simplexml_load_file( plugin_dir_path( __FILE__ ) . 'kml/' . $kml ); // get xml from amazon
    
        // PARSE AND INSERT SOURCE
        foreach ($kml_object->Document->Placemark as $place) {
        
            $name = $place->name;
            
            
            // Create the record array
            $WorldID = $place->ExtendedData->Data->value;
            $Zone_Name =  $name;
            $CntyID = 'TUN';
            $Cnty_Name = 'Tunisia';
    
            $Adm1ID = substr( $WorldID, 0, 7 );
            $Adm1_Name = $wpdb->get_var( "SELECT Zone_Name FROM $table WHERE WorldID = '$Adm1ID'" );
            
            $Adm2ID = $WorldID;
            $Adm2_Name = $Zone_Name;
            $Adm3ID = '';
            $Adm3_Name = '';
            $Adm4ID = '';
            $Adm4_Name = '';
            $World = 'C';
            $Population = '';
            $Shape_Leng = '';
            $Cen_x = ''; // added later
            $Cen_y = ''; // added later
            $Region = 'North Africa Region';
            $Field = 'Europe, Middle East and North Africa Field';
            $geometry = ''; // added later
            $Notes = $kml;
            $Last_Sync = date( 'Y-m-d H:i:s', time() );
            $Sync_Source = 'GDAM KML';
            $Source_Key = '';
        
            // Parse and create JSON coordinate record.
            if ( $place->Polygon ) {
                $ring = [];
                $polygon = [];
                $values = explode( ",0", $place->Polygon->outerBoundaryIs->LinearRing->coordinates );
                
                $count = count( $values );
                unset( $values[$count - 1] ); // remove last empty element. the explode generates an empty array element at the end.
                
                foreach ( $values as $value ) {
                    $value = trim( $value );
                    $coords = explode( ",", $value );
                    !empty( $coords ) ? $polygon[] = $coords : false;

                }
                $ring[] = $polygon;
            }
            elseif ( $place->MultiGeometry ) {
                $ring = [];
                foreach ( $place->MultiGeometry->Polygon as $single_polygon ) {
                    $polygon = [];
                    $values = explode( ",0", $single_polygon->outerBoundaryIs->LinearRing->coordinates );
    
                    $count = count( $values );
                    unset( $values[$count - 1] ); // remove last empty element. the explode generates an empty array element at the end.
                    
                    foreach ( $values as $value ) {
                        $value = trim( $value );
                        $coords = explode( ",", $value );
                        !empty( $coords ) ? $polygon[] = $coords : false;

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
            Notes,
            Last_Sync,
            Sync_Source,
            Source_Key
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
            '$Notes',
            '$Last_Sync',
            '$Sync_Source',
            '$Source_Key'
            )
            ";
        
            $wpdb->query( $insert_sql );
//            print '<pre>';
//            print_r($insert_sql);
//            print '</pre>';
        }
        
        print_r( $wpdb->rows_affected );
        print $error;
    }
}
