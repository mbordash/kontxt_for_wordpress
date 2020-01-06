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
                                </p>
                                <p>
                                    Providing search results that better match the customer's intent yield higher transaction rates and reduce dependency on Google for searching your own site -- this keeps customers on your site and creates
                                    a stronger relationship with your brand.
                                </p>

                                <p>Macro intents supported:</p>

                                <dl>

                                    <dt><strong>Discovery</strong></dt>
                                    <dd>Signals the beginning of a customer purchase journey. Questions about product concepts or general industry concepts that relate to your brand, for example "What is wifi calling?" or "What is 3d printing?"</dd>

                                    <dt><strong>Research & Compare</strong></dt>
                                    <dd>Signals an intermediate state of a customer journey where the customer is leaning in to a set of products. Questions about general product details or comparison, for example "What's the difference between a regular ipad and a pro?" or "What suit materials are good for travel?"</dd>

                                    <dt><strong>Solve My Problem</strong></dt>
                                    <dd>Signals an intermediate state of a customer journey where the customer is asking your brand to help solve a problem such as "How do I stop a leaking sink?" or "How do I prevent my sweater from shrinking?"</dd>

                                    <dt><strong>Buy Now</strong></dt>
                                    <dd>Signals a customer at the final stages of a purchase choice. Questions about specific product details, for example "Do you have this model available near me?" or "How do I extend support for my device?" </dd>

                                    <dt><strong>Customer Support</strong></dt>
                                    <dd>Signals an issue from an existing or potential customer for example "When do you open tomorrow?" or "What is your return policy" or "When does my order arrive?" </dd>

                                </dl>

                                <p>
                                    We've trained our classifier on general retail scenarios.  We offer secondary classifiers that are industry and domain specific. We can also custom train a classifier that is very specific to your brand.
                                    Our classifiers are also available for use within chat bot systems via our API. Please <a target="_blank" href="https://kontxt.com/more-information/">contact us</a> for details.
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