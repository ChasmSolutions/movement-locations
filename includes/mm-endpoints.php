<?php

/**
 * MM_Endpoints
 *
 * @class   MM_Endpoints
 * @version 0.1
 * @since   0.1
 * @package MM_Endpoints
 * @author  Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class MM_Endpoints {

    private $version = 1;
    private $context = "mm";
    private $namespace;

    /**
     * MM_Endpoints The single instance of MM_Endpoints.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main MM_Endpoints Instance
     *
     * Ensures only one instance of MM_Endpoints is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return MM_Endpoints instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {
        $this->namespace = $this->context . "/v" . intval( $this->version );
        add_action( 'rest_api_init', [$this,  'add_api_routes'] );

    } // End __construct()

    public function add_api_routes () {
        $version = '1';
        $namespace = 'mm/v' . $version;
        $base = 'install';
        register_rest_route(
            $namespace, '/' . $base . '/getcountrybylevel', [
                [
                    'methods'         => WP_REST_Server::READABLE,
                    'callback'        => [ $this, 'get_country_by_level' ],
                ],
            ]
        );
    
        register_rest_route(
            $namespace, '/' . $base . '/get_summary', [
                [
                    'methods'         => WP_REST_Server::READABLE,
                    'callback'        => [ $this, 'get_summary' ],
                ],
            ]
        );
        
        register_rest_route(
            $namespace, '/' . $base . '/getstate', [
                [
                    'methods'         => WP_REST_Server::READABLE,
                    'callback'        => [ $this, 'get_usa_state' ],
                ],
            ]
        );
    }

    /**
     * Get admin level 1 for a country
     *
     * @example http://locations/wp-json/mm/v1/install/getcountrybylevel?CntyID=CHN&level=2
     *
     * @param  WP_REST_Request $request
     * @access public
     * @since  0.1
     * @return string|WP_Error The contact on success
     */
    public function get_country_by_level ( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['cnty_id'] )){
            $result = MM_Controller::get_country_by_level( $params['cnty_id'], $params['level'] );
            if ($result["status"] == 'OK'){
                return $result["geojson"];
            } else {
                return new WP_Error( "country_error", $result["message"], ['status' => 400] );
            }
        } else {
            return new WP_Error( "country_param_error", "Please provide a valid country (WorldID)", ['status' => 400] );
        }
    }
    
    /**
     * Gets a summary of the country. i.e. how many adm1, how many adm2, etc
     * @param WP_REST_Request $request
     *
     * @return array|WP_Error
     */
    public function get_summary ( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['cnty_id'] )){
            $result = MM_Controller::get_summary( $params['cnty_id'] );
            if ($result){
                return $result;
            } else {
                return new WP_Error( "country_error", $result["message"], ['status' => 400] );
            }
        } else {
            return new WP_Error( "country_param_error", "Please provide a valid country (WorldID)", ['status' => 400] );
        }
    }
    
    /**
     * Gets counties and tracts for USA states
     * @param WP_REST_Request $request
     *
     * @return array|WP_Error
     */
    public function get_usa_state ( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['state_id'] )){
            $result = MM_Controller::get_usa_state( $params['state_id'], $params['level'] );
            if ($result){
                return $result;
            } else {
                return new WP_Error( "country_error", $result["message"], ['status' => 400] );
            }
        } else {
            return new WP_Error( "country_param_error", "Please provide a valid country (WorldID)", ['status' => 400] );
        }
    }

    

}
