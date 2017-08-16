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
        $base = 'locations';
        register_rest_route(
            $namespace, '/' . $base . '/getcountryadmin1', [
            [
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => [ $this, 'get_country_admin_1' ],
            ],
             ]
        );
    }

    /**
     * Get admin level 1 for a country
     *
     * @param  WP_REST_Request $request
     * @access public
     * @since  0.1
     * @return string|WP_Error The contact on success
     */
    public function get_country_admin_1 ( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['CntyID'] )){
            $result = MM_Controller::get_country_admin_1( $params['CntyID'] );
            if ($result["status"] == 'OK'){
                return $result["geojson"];
            } else {
                return new WP_Error( "country_error", $result["message"], ['status' => 400] );
            }
        } else {
            return new WP_Error( "country_param_error", "Please provide a valid country (WorldID)", ['status' => 400] );
        }
    }

    

}
