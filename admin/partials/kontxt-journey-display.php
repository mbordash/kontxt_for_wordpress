<?php
/**
 * Created by PhpStorm.
 * User: michaelbordash
 * Date: 4/21/17
 * Time: 11:04 PM
 */

include_once 'kontxt-banner.php';

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <h2>Journey Analytics (preview/alpha)</h2>

    <div id="journey" class="inside">

        <div id="journey-results-box" class="wrap">

            <div id="journey-results-success">

                <div class="wrap">

                    <div id="poststuff">

                        <div class="postbox">

                            <div class="inside">

                                <div id="journey_results_chart"></div>

                            </div>
                        </div>
                    </div>

                    <div id="poststuff">

                        <div class="postbox">

                            <div class="inside">

                                <h3>About</h3>

                                <p>This is a preview of our new journey analytics function, so please expect some odd visuals while we work out the kinks! This
                                    has no effect on your wordpress site or store at all.
                                    Our machine learning engine analyzes customer journeys to help discover the optimal paths between inception and transaction.
                                    Use these insights to help shape your site navigation and help your customers along the path.</p>
                            </div>
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
            kontxtJourney();
        });
    </script>

</div>