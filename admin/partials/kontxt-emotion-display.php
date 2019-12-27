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

        <div id="kontxt-results-box" class="wrap">

            <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">
                <input id="dimension" name="dimension" type="hidden" value="emotion" />

                <div id="kontxt-input-text">

                    Date From: <input type="text" style="" name="date_from" id="date_from" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                    Date To: <input type="text" style="" name="date_to" id="date_to" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />

                    <input id="kontxt-events-date" class="button-primary" type="submit" value="Get " />

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