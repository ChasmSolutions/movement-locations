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

class MM_Admin_Tab_Stats
{
    
    /**
     * Page content for the tab
     */
    public function page_contents()
    {
        
        $html = '';
        
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        $html .= $this->stats_box().'';
        
        
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */
        
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
        
        return $html;
        
    }
    
    public function stats_box () {
        $html = '';
        $html .= '<div class="wrap">';
    
        $html .= '<table class="widefat striped">
                    <thead><th>Global (_mm table)</th></thead>
                    <tbody><tr><td>';
        
        $country_stats = mm_get_country_stats();
        foreach ( $country_stats as $key => $value ) {
            $html .= $key . ' : ' . $value . '<br>';
        }
        
        $html .= '</td></tr></tbody></table><br>';
    
        $html .= '<table class="widefat striped">
                    <thead><th>USA (_mm_usa table)</th></thead>
                    <tbody><tr><td>';
    
        $country_stats = mm_get_usa_stats();
        foreach ( $country_stats as $key => $value ) {
            $html .= $key . ' : ' . $value . '<br>';
        }
    
        $html .= '</td></tr></tbody></table></div>';
        
        echo $html;
    }
    
}
