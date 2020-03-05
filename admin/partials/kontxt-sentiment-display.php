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

    <h2><?php echo __('Sentiment Analytics', 'kontxt'); ?></h2>

    <div id="sentiment" class="inside">

        <div id="kontxt-results-box" class="wrap">

            <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">
                <input id="dimension" name="dimension" type="hidden" value="sentiment" />

                <div id="kontxt-input-text">

                    <label for="date_from"><?php echo __('Date From', 'kontxt'); ?></label> <input type="text" style="" name="date_from" id="date_from" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                    <label for="date_to"><?php echo __('Date To', 'kontxt'); ?></label> <input type="text" style="" name="date_to" id="date_to" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />

                    <input id="kontxt-events-date" class="button-primary" type="submit" value="Get " />

                    <label for="filter"><?php echo __('Filter', 'kontxt' ); ?></label> <select id="filter" name="filter" style="vertical-align: top;">
                        <option selected value="">All intents</option>
                        <option value="BuyNow">Buy Now</option>
                        <option value="ResearchCompare">Research & Compare</option>
                        <option value="SolveMyProblem">Solve My Problem</option>
                        <option value="Discovery">Discovery</option>
                        <option value="CustomerSupport">Customer Support</option>
                    </select>

                    <input id="kontxt-intent-filter" class="button-primary" type="submit" value="Filter " />

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
                        </div>

                        <div class="postbox">

                            <div class="inside">

                                <?php
                                    echo wp_kses(__('
                                        <h3>About</h3>
        
                                        <p>Our machine learning engine analyzes all in-bound customer communication (including search input, product reviews, contact forms) to determine the general sentiment of all interactions each day.
                                            These insights provide a signal for how your customers feel about your brand and its products over time. A positive sentiment score
                                            tends to yield higher attraction and transaction rates. Fixing your content and service problems early on should improve the effectiveness of your customer\'s journey.</p>
                                    ', 'kontxt'), $allowed_html );
                                ?>
                            </div>
                        </div>
                    </div>

                    <div id="postbox">

                            <div class="inside">

                                <div id="sentiment_results_table"></div>

                            </div>

                        <!-- .postbox -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="kontxt-analyze-results-status" class="wrap"></div>

    <script>
        jQuery(function($) {
            "use strict";
            kontxtAnalyze('sentiment');
        });
    </script>
</div>