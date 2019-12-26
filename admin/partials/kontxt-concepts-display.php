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

    <h2>Concepts</h2>

    <div id="concepts" class="inside">

        <div id="concepts-results-box" class="wrap">

            <div id="concepts-results-success">

                <div class="wrap">

                    <div id="poststuff">

                        <div class="postbox">

                            <h2 id="concepts_results_title"></h2>

                            <div class="inside">

                                <div id="concepts_results_chart"></div>

                            </div>

                            <div class="inside">

                                <div id="concepts_results_table"></div>

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
            kontxtAnalyze('concepts');
        });
    </script>

</div>