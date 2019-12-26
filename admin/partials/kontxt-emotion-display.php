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

    <h2>Emotion Analytics</h2>

        <div id="emotion" class="inside">

            <div id="emotion-results-box" class="wrap">

                <div id="emotion-results-success">

                    <div class="wrap">

                        <div id="poststuff">

                            <div class="postbox">

                                <div class="inside">

                                    <div id="emotion_results_chart"></div>

                                </div>

                                <div class="inside">

                                    <div id="emotion_results_table"></div>

                                </div>

                            </div>
                            <!-- .postbox -->
                        </div>
                    </div>
                </div>
        </div>

    </div>

    <div id="spinner-analyze" class="spinner is-inactive" style="float:none; width:100%; height: auto; padding:10px 0 10px 50px; background-position: center center;"></div>

    <div id="kontxt-analyze-results-status" class="wrap"></div>

    <script>
        jQuery(function($) {
            kontxtAnalyze('emotion');
        });
    </script>

</div>