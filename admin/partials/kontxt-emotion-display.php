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

    <h2><?php echo __( 'Emotion Analytics', 'kontxt' ); ?></h2>

    <div id="emotion" class="inside">

        <div id="kontxt-results-box" class="wrap">

            <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">
                <input id="dimension" name="dimension" type="hidden" value="emotion" />

                <div id="kontxt-input-text">

	                <label for="date_from"><?php echo __( 'Date From', 'kontxt' ); ?></label> <input type="text" style="" name="date_from" id="date_from" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                    <label for="date_to"><?php echo __( 'Date To', 'kontxt' ); ?></label> <input type="text" style="" name="date_to" id="date_to" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />

                    <input id="kontxt-events-date" class="button-primary" type="submit" value="<?php echo __( 'Get', 'kontxt' ); ?> " />

                </div>

            </form>

        </div>


        <div id="emotion-results-box" class="wrap">

            <div id="emotion-results-success">

                <div class="wrap">

                    <div id="poststuff">

                        <div class="postbox">

                            <div class="inside">

                                <div id="emotion_results_chart"></div>

                            </div>
                        </div>


                        <div class="postbox">

                            <div class="inside">

                                <?php
                                    echo wp_kses(__('

                                        <h3>About</h3>

                                        <p>Our machine learning engine analyzes all in-bound customer communication (including search input, product reviews, contact forms) to determine the general emotional elements of all interactions each day.
                                         hese insights provide a signal for how your customers feel about your brand and its products over time. Generally, you\'ll want to isolate the negative emotions and study
                                        why your brand is causing this reaction through the analytics we provide.

                                        While "joy" is a positive indicator that predicts higher transaction rates, the negative emotions tend to predict higher attrition rates
                                        and cause more damage to your brand than what can be countered by positive emotion.</p>

                                     ', 'kontxt'), $allowed_html );
                                ?>
                                
                            </div>
                        </div>

                        <div class="postbox">
                            <div class="inside">

                                <div id="emotion_results_table"></div>

                            </div>

                        </div>

                    </div>
                        <!-- .postbox -->
                </div>
            </div>
        </div>

    </div>

    <div id="kontxt-analyze-results-status" class="wrap"></div>

    <script>
        jQuery(function($) {
            "use strict";
            kontxtAnalyze('emotion');
        });
    </script>

</div>