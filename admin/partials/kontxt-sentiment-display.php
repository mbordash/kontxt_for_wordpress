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

    <h2>Sentiment Analytics</h2>

    <div id="sentiment" class="inside">

        <div id="sentiment-results-box" class="wrap">

            <div id="sentiment-results-success" class="hidden">

                <div class="wrap">

                    <div id="poststuff">

                        <div class="postbox">

                            <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">

                                <div id="kontxt-input-text">

                                    Date Range: <input type="text" style="" name="date_range" id="date_range" value="" placeholder="YYYY-MM-DD - YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01]) - [0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">

                                    <input id="kontxt-experiment-input-button" class="button-primary" type="submit" value="Get " />

                                </div>

                            </form>


                            <div class="inside">

                                <div id="sentiment_results_chart"></div>

                            </div>

                            <div class="inside">

                                <div id="sentiment_results_table"></div>

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
            kontxtAnalyze('sentiment');
        });
    </script>
</div>