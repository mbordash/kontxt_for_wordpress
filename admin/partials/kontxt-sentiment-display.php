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

        <div id="kontxt-results-box" class="wrap">

            <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">
                <input id="dimension" name="dimension" type="hidden" value="sentiment" />

                <div id="kontxt-input-text">

                    Date From: <input type="text" style="" name="date_from" id="date_from" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                    Date To: <input type="text" style="" name="date_to" id="date_to" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />

                    <input id="kontxt-events-date" class="button-primary" type="submit" value="Get " />

                    <select id="filter" name="filter" style="vertical-align: top;">
                        <option selected value="">All intents</option>
                        <option value="BuyNow">Buy Now</option>
                        <option value="ResearchCompare">Research & Compare</option>
                        <option value="SolveProblem">Solve a Problem</option>
                        <option value="Discovery">Discovery</option>
                        <option value="Assistance">Shop Assistance</option>
                    </select>

                    <input id="kontxt-intent-filter" class="button-primary" type="submit" value="Filter " />

                    <select id="overlay" name="overlay" style="vertical-align: top;">
                        <option selected value="intents">By all intents</option>
                    </select>

                    <input id="kontxt-intent-overlay" class="button-primary" type="submit" value="Overlay " />

                </div>

            </form>

        </div>

        <div id="sentiment-results-box" class="wrap">

            <div id="sentiment-results-success" class="hidden">

                <div class="wrap">

                    <div id="poststuff">

                        <div class="postbox">

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