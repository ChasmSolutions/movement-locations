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
            case 'Population':
            case 'Cen_x':
            case 'Cen_y':
            case 'Region':
            case 'Field':
            case 'OBJECTID_1':
            case 'OBJECTID':
            case 'Notes':
                return $item->$column_name;
            case 'geometry':
                return true;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
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
            /*$2%s*/ $item->WorldID                //The value of the checkbox should be the record's id
        );
    }
    
    
    function get_columns(){
        $columns = array(
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            'WorldID'       => 'WorldID',
            'Zone_Name'     => 'Zone Name',
            'CntyID'        => 'CntyID',
            'Cnty_Name'     => 'Cnty_Name',
            'Adm1ID'        => 'Adm1ID',
            'Adm1_Name'     => 'Adm1_Name',
            'Adm2ID'        => 'Adm2ID',
            'Adm2_Name'     => 'Adm2_Name',
            'Adm3ID'        => 'Adm3ID',
            'Adm3_Name'     => 'Adm3_Name',
            'Adm4ID'        => 'Adm4ID',
            'Adm4_Name'     => 'Adm4_Name',
            'World'         => 'World',
            'Population'    => 'Population',
            'Cen_x'         => 'Cen_x',
            'Cen_y'         => 'Cen_y',
            'Region'        => 'Region',
            'Field'         => 'Field',
            'geometry'      => 'geometry',
            'OBJECTID_1'    => 'OBJECTID_1',
            'OBJECTID'      => 'OBJECTID',
            'Notes'         => 'Notes',
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
    
    
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries
        
        $per_page = 20;
        
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        $this->process_bulk_action();
        
        
        $data = $wpdb->get_results("SELECT * FROM $wpdb->mm LIMIT 1000 ;");;
        
        
        function ml_usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'WorldID'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        
//        usort($data, 'ml_usort_reorder');
        
        $current_page = $this->get_pagenum();
        
        $total_items = count($data);
        
//        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
    
}