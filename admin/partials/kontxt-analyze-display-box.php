<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.kontxt.com
 * @since      1.0.0
 *
 * @package    Kontxt
 * @subpackage kontxt-for-wordpress/admin/partials
 */


?>


<div id="kontxt-results-box" class="wrap">

    <div id="kontxt-results-status" class="wrap">Enter some content above and then click the kontxt robot button.</div>

    <div id="kontxt-results-success" class="hidden">

        <div id="spinner" class="spinner is-inactive" style="float:none; width:100%; height: auto; padding:10px 0 10px 50px; background-position: center center;"></div>

        <div id="kontxt-results-navigation">

            <h2 class="nav-tab-wrapper current">

                <a class="nav-tab nav-tab-active" href="javascript:;"><?php esc_attr_e( 'Sentiment', 'wp_admin_style' ); ?></a>
                <a class="nav-tab" href="javascript:;"><?php esc_attr_e( 'Emotions', 'wp_admin_style' ); ?></a>
                <a class="nav-tab" href="javascript:;"><?php esc_attr_e( 'Keywords & Concepts', 'wp_admin_style' ); ?></a>
            </h2>

            <div class="inside">
                <div id="overall_tone"></div>
                <div id="sentiment_chart">
                    <svg></svg>
                </div>
            </div>

            <div class="inside hidden">
                <div id="emotion_chart">
                    <svg id="emotion_chart_svg"></svg>
                </div>
            </div>

           <div class="inside hidden">
               <div id="keywords_chart">
                   <table id="kontxt_keywords"></table>
               </div>

               <div id="concepts_chart">
                   <table id="kontxt_concepts"></table>
               </div>

           </div>

        </div>

    </div>

</div>
