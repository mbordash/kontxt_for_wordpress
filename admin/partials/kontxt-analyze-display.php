<?php
/**
 * Created by PhpStorm.
 * User: michaelbordash
 * Date: 4/21/17
 * Time: 11:04 PM
 */

$allowed_html = get_option( 'kontxt_allowable_html' );

include_once 'kontxt-banner.php';

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <div id="spinner-analyze" class="spinner is-inactive" style="float: right;"></div>

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <div id="activity-results-success" class="inside">

        <div id="activity-results-box" class="wrap">

            <div class="wrap">

                <div id="poststuff">

                    <div class="postbox">

                        <div class="inside">

                            <?php
                            echo wp_kses(__('
                                <h3>KONTXTscore&trade;</h3>
    
                                Your KONTXTscore is the overall analysis of your visitor sentiment.  Our proprietary algorithm considers all visitor text interaction with your site, including views, search, reviews, and other inbound
                                text-based communication.
                                ', 'kontxt'), $allowed_html );
                            ?>

                            <div style="text-align: center">
                                <div style="display: inline-block">

                                    <div id="dashboard_results_chart"></div>

                                    Deep analytics:
                                        <a href="<?php echo admin_url( 'admin.php?page=kontxt_sentiment' ); ?>"><?php echo __('Sentiment', 'kontxt' ); ?></a> |
                                        <a href="<?php echo admin_url( 'admin.php?page=kontxt_emotion' ); ?>"><?php echo __('Emotion', 'kontxt' ); ?></a> |
                                        <a href="<?php echo admin_url( 'admin.php?page=kontxt_intents' ); ?>"><?php echo __('Semantic Intents', 'kontxt' ); ?></a> |
                                        <a href="<?php echo admin_url( 'admin.php?page=kontxt_keywords' ); ?>"><?php echo __('Keywords', 'kontxt' ); ?></a> |
                                        <a href="<?php echo admin_url( 'admin.php?page=kontxt_journey' ); ?>"><?php echo __('Journey', 'kontxt' ); ?></a> |
                                        <a href="<?php echo admin_url( 'admin.php?page=kontxt_settings' ); ?>"><?php echo __('Experiment', 'kontxt' ); ?></a>

                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- .postbox -->
                </div>

                <div id="poststuff">

                    <div class="postbox">

                        <div class="inside">
                            <?php
                                echo wp_kses(__('
                                    <h3>About KONTXT&trade;</h3>
        
                                    <p>KONTXT helps you understand your relationship with your customers in real-time by analyzing how they interact with your
                                        site through both text and journey analytics.  These insights will help you craft better blog articles, product descriptions,
                                        customer care interaction, and keywords used for SEO and external search advertising.</p>
                                ', 'kontxt'), $allowed_html );
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        jQuery(function($) {
            "use strict";
            kontxtAnalyze('dashboard');
        });
    </script>

    <div id="kontxt-analyze-results-status" class="wrap"></div>

</div>