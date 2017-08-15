<?php


if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class MM_Table extends WP_List_Table {
    
    function __construct(){
        global $status, $page;
        
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'location',     //singular name of the listed records
            'plural'    => 'locations',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    function column_default($item, $column_name){
        switch($column_name){
            case 'WorldID':
            case 'Zone_Name':
            case 'CntyID':
            case 'Cnty_Name':
            case 'Adm1ID':
            case 'Adm1_Name':
            case 'Adm2ID':
            case 'Adm2_Name':
            case 'Adm3ID':
            case 'Adm3_Name':
            case 'Adm4ID':
            case 'Adm4_Name':
            case 'World':
            case 'Region':
            case 'Field':
            case 'Notes':
                return $item[$column_name];
            case 'Center':
                return !empty( $item['Cen_x'] ) ? '<a href="https://www.google.com/maps/@'.$item['Cen_y'].','.$item['Cen_x'].',10z" target="_blank">' . $item['Cen_x'] . ', ' . $item['Cen_y'] . '</a>' : '';
            case 'geometry':
                return !empty( $item[$column_name] ) ? 'Yes' : 'No';
            case 'OBJECTID_1':
            case 'OBJECTID':
                return '<a href="https://services1.arcgis.com/DnZ5orhsUGGdUZ3h/ArcGIS/rest/services/OmegaZones082016/FeatureServer/query?layerDefs=%7B%220%22%3A+%22OBJECTID_1+%3D+%27'.$item[$column_name].'%27%22%7D&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&outSR=&returnGeometry=true&maxAllowableOffset=&geometryPrecision=&returnIdsOnly=false&returnCountOnly=false&returnDistinctValues=false&returnZ=false&returnM=false&sqlFormat=none&f=pjson&token=">'.$item[$column_name].'</a>';
            case 'Population':
                return !empty( $item[$column_name] ) ? number_format_i18n($item[$column_name]) : '';
            default:
                return print_r( $item,true ); //Show the whole array for troubleshooting purposes
        }
    }
    
    
    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&location=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&location=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['Zone_name'],
            /*$2%s*/ $item['WorldID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['WorldID']                //The value of the checkbox should be the record's id
        );
    }
    
    
    function get_columns(){
        $columns = array(
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            'WorldID'       => 'WorldID',
            'Zone_Name'     => 'Zone Name',
            'CntyID'        => 'CntyID',
            'Cnty_Name'     => 'Cnty_Name',
//            'Adm1ID'        => 'Adm1ID',
//            'Adm1_Name'     => 'Adm1_Name',
//            'Adm2ID'        => 'Adm2ID',
//            'Adm2_Name'     => 'Adm2_Name',
//            'Adm3ID'        => 'Adm3ID',
//            'Adm3_Name'     => 'Adm3_Name',
//            'Adm4ID'        => 'Adm4ID',
//            'Adm4_Name'     => 'Adm4_Name',
//            'World'         => 'World',
            'Population'    => 'Population',
            'Center'         => 'Center',
//            'Cen_y'         => 'Cen_y',
            'Region'        => 'Region',
            'Field'         => 'Field',
            'geometry'      => 'geometry',
            'OBJECTID_1'    => 'OBJECTID_1',
//            'OBJECTID'      => 'OBJECTID',
//            'Notes'         => 'Notes',
        );
        return $columns;
    }
    
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'WorldID'     => array('WorldID',false),     //true means it's already sorted
            'Zone_Name'    => array('Zone_Name',false),
            'Population'  => array('Population',false),
            'CntyID'  => array('CntyID',false),
            'Cnty_Name'  => array('Cnty_Name',false),
            'geometry'  => array('geometry',false),
            'OBJECTID_1'  => array('OBJECTID_1',false),
            'Region'  => array('Region',false),
            'Field'  => array('Field',false),
        );
        return $sortable_columns;
    }
    
    
    function get_bulk_actions() {
        $actions = array(
            'flag'    => 'Flag'
        );
        return $actions;
    }
    
    
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'flag'===$this->current_action() ) { //TODO: make an export to flag
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }
    
    function prepare_items( $search = NULL ) {
        global $wpdb; //This is used only if making any database queries
        
        $columns = $this->get_columns(); // prepare columns
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable); // construct column headers to wp_table
        
        $this->process_bulk_action(); // construct bulk actions
        
        $total_items = $wpdb->get_var("SELECT count(*) FROM $wpdb->mm"); // get total items
        $current_page = $this->get_pagenum();// get current page
        $per_page = 20; // get items per page
        $page_start = ($current_page-1)*$per_page; // calculate starting item id
        
        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'WorldID'; //If no sort, default to title
        $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
    
        if( empty($search) ) {
    
            if( $_GET['cnty'] > 0 ) {
                $where = $query . ' where cat_id=' . $_GET['cat-filter'];
            }
            
            $query = "SELECT * 
                    FROM $wpdb->mm
                    ORDER BY $orderby $order
                    LIMIT $page_start, $per_page";
            
            
    
            $data = $wpdb->get_results( $query, ARRAY_A );
            
        } else {
            // Trim Search Term
            $search = trim( $search );
    
            /* Notice how you can search multiple columns for your search term easily, and return one data set */
            $data = $wpdb->get_results(
                $wpdb->prepare( "
                    SELECT * 
                    FROM  $wpdb->mm 
                    WHERE `WorldID` LIKE '%%%s%%' 
                      OR `Zone_Name` LIKE '%%%s%%'
                    ",
                    $search,
                    $search
                ),
                ARRAY_A
            );
    
    
            $total_items = count($data);
            $per_page = $total_items;
        }
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
    function extra_tablenav( $which ) {
        global $wpdb, $testiURL, $tablename, $tablet;
        $move_on_url = '&cat-filter=';
        if ( $which == "top" ){
            ?>
            <div class="alignleft actions bulkactions">
                <?php
                $cats = $wpdb->get_results('select * from '.$tablename.' order by title asc', ARRAY_A);
                if( $cats ){
                    ?>
                    <select name="cat-filter" class="ewc-filter-cat">
                        <option value="">Filter by Category</option>
                        <?php
                        foreach( $cats as $cat ){
                            $selected = '';
                            if( $_GET['cat-filter'] == $cat['id'] ){
                                $selected = ' selected = "selected"';
                            }
                            $has_testis = false;
                            $chk_testis = $wpdb->get_row("select id from ".$tablet." where banner_id=".$cat['id'], ARRAY_A);
                            if( $chk_testis['id'] > 0 ){
                                ?>
                                <option value="<?php echo $move_on_url . $cat['id']; ?>" <?php echo $selected; ?>><?php echo $cat['title']; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        if ( $which == "bottom" ){
            //The code that goes after the table is there
            
        }
    }
    
    
}