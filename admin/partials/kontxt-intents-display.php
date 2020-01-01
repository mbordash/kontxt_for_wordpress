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

    <h2>Intents Analytics</h2>

    <div id="intents" class="inside">


        <div id="kontxt-results-box" class="wrap">

            <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">
                <input id="dimension" name="dimension" type="hidden" value="intents" />

                <div id="kontxt-input-text">

                    Date From: <input type="text" style="" name="date_from" id="date_from" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                    Date To: <input type="text" style="" name="date_to" id="date_to" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />

                    <input id="kontxt-events-date" class="button-primary" type="submit" value="Get " />

                </div>

            </form>

        </div>

        <div id="intenst-results-box" class="wrap">

            <div id="intents-results-success">

                <div class="wrap">

                    <div id="poststuff">

                        <div class="postbox">

                            <h2 id="intents_results_title"></h2>

                            <div class="inside">

                                <div id="intents_results_chart"></div>

                            </div>
                        </div>
                    </div>

                    <div id="poststuff">

                        <div class="postbox">

                            <div class="inside">

                                <h3>About</h3>

                                <p>Our machine learning engine analyzes all in-bound customer communication (including search input, product reviews, contact forms) to determine the semantic intent behind each interaction.
                                    These intent insights show you what your customers are trying to do from your site.   You'll want to study the results for each intent to determine whether you're
                                    missing content or product that your customer is looking for.

                                    Providing search results that better match the customer's intent yield higher transaction rates and reduce dependency on Google for searching your own site -- this keeps customers on your site and creates
                                    a stronger relationship with your brand.

                                </p>
                            </div>
                        </div>
                    </div>

                    <div id="poststuff">
                        <div class="inside">
                            <div id="intents_results_table"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="spinner-analyze" class="spinner is-inactive" style="float:none; width:100%; height: auto; padding:10px 0 10px 50px; background-position: center center;"></div>

    <div id="kontxt-analyze-results-status" class="wrap"></div>

    <script>
        jQuery(function($) {
            kontxtAnalyze('intents');
        });
    </script>

</div>