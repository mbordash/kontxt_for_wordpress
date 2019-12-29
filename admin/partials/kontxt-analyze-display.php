<?php
/**
 * Created by PhpStorm.
 * User: michaelbordash
 * Date: 4/21/17
 * Time: 11:04 PM
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <div id="activity-results-success" class="inside hidden">

        <div id="activity-results-box" class="wrap">

            <div class="wrap">

                <div id="poststuff">

                    <div class="postbox">

                        <div class="inside">

                            <h3>KONTXTscore &copy; </h3>

                            Your KONTXTscore is the overall analysis of your visitor sentiment.  Our proprietary algorithm considers all visitor text interaction with your site, including views, search, reviews, and other inbound
                            text-based communication.

                            <div style="text-align: center">
                                <div style="display: inline-block">

                                    <div id="dashboard_results_chart"></div>

                                    Deep analytics:
                                    <a href="<?php echo admin_url( 'admin.php?page=kontxt_sentiment' ); ?>">Sentiment</a> |
                                    <a href="<?php echo admin_url( 'admin.php?page=kontxt_emotion' ); ?>">Emotion</a> |
                                    <a href="<?php echo admin_url( 'admin.php?page=kontxt_intents' ); ?>">Semantic Intents</a> |
                                    <a href="<?php echo admin_url( 'admin.php?page=kontxt_keywords' ); ?>">Keywords</a> |
                                    <a href="<?php echo admin_url( 'admin.php?page=kontxt_experiment' ); ?>">Experiment</a>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- .postbox -->
                </div>

                <div id="poststuff">

                    <div class="postbox">

                        <div class="inside">

                            <h3>Latest Site Behaviors Detected</h3>

                            <div id="latestActivity_results_table"></div>

                        </div>

                    </div>
                    <!-- .postbox -->
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(function($) {
            kontxtAnalyze('latestActivity');
            kontxtAnalyze('dashboard');
        });
    </script>

    <div id="spinner-analyze" class="spinner is-inactive" style="float:none; width:100%; height: auto; padding:10px 0 10px 50px; background-position: center center;"></div>

    <div id="kontxt-analyze-results-status" class="wrap"></div>

</div>