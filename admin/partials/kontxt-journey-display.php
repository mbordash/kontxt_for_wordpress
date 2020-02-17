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

    <div id="spinner-analyze" class="spinner is-inactive" style="float: right;"></div>

    <h2>Journey Analytics</h2>

    <div id="journey" class="inside">

        <div id="kontxt-results-box" class="wrap">

            <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">
                <input id="dimension" name="dimension" type="hidden" value="journey" />

                <div id="kontxt-input-text">

                    Date From: <input type="text" style="" name="date_from" id="date_from" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                    Date To: <input type="text" style="" name="date_to" id="date_to" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />

                    <input id="kontxt-events-date" class="button-primary" type="submit" value="Get " />

                </div>

            </form>

        </div>


        <div id="journey-results-box" class="wrap">

            <div id="journey-results-success">

                <div class="wrap">

                    <div id="poststuff">

                        <div class="postbox">

                            <div class="inside">

                                <div id="journey_results_chart"></div>

                            </div>

                        </div>

                        <div id="journey_node_details_box" class="postbox hidden">

                            <div class="inside">

                                <h3 id="journey_node_details_header"></h3>

                                <div id="journey_node_details_table"></div>

                            </div>

                        </div>

                        <div class="postbox">

                            <div class="inside">

                                <h3>About</h3>

                                <p>This is a preview of our new journey analytics function, so please expect some odd visuals while we work out the kinks! This
                                    has no effect on your wordpress site or store at all.  Our machine learning engine analyzes customer journeys to help discover the optimal paths between inception and transaction.
                                    Use these insights to help shape your site navigation and help your customers along the path.

                                    We've trained our journey analyzer on basic content and shop site events, however, we offer customized event discovery including article, product, and custom business logic flows for more advanced analysis and recommendations.  Please <a target="_blank" href="https://kontxt.com/more-information/">contact us</a> for details.
                                </p>


                                <h3>Generic events supported:</h3>

                                <dl>

                                    <dt><strong>site_home</strong></dt>
                                    <dd>View of the main site page or blog home page</dd>

                                    <dt><strong>blog_post</strong></dt>
                                    <dd>View of a blog post</dd>

                                    <dt><strong>site_page</strong></dt>
                                    <dd>View of any site page including custom pages, category pages, and commerce pages like cart, checkout, and account management </dd>

                                    <dt><strong>search_query*</strong></dt>
                                    <dd>Retrieval of search results</dd>

                                    <dt><strong>no_search_results</strong></dt>
                                    <dd>Signal indicating your visitor received no search results from their query</dd>

                                    <dt><strong>user_registered</strong></dt>
                                    <dd>Event indicating a new visitor registered on your site</dd>

                                    <dt><strong>comment_submitted*</strong></dt>
                                    <dd>Event indicating a visitor posted a comment</dd>

                                    <dt><strong>END SESSION</strong></dt>
                                    <dd>A calculated event that indicates your visitor ended their time on your site</dd>


                                </dl>

                                <h3>WooCommerce events supported:</h3>

                                <dl>

                                    <dt><strong>shop_page_home</strong></dt>
                                    <dd>View of your WooCommerce home page </dd>

                                    <dt><strong>shop_page_category</strong></dt>
                                    <dd>View of a shop category page </dd>

                                    <dt><strong>shop_page_product</strong></dt>
                                    <dd>View of a shop product page </dd>

                                    <dt><strong>cart_add</strong></dt>
                                    <dd>Signal indicating your visitor added a product to cart</dd>

                                    <dt><strong>order_received</strong></dt>
                                    <dd>Signal indicating your visitor completed and order</dd>

                                </dl>

                                <h3>bbPress forum events supported:</h3>

                                <dl>

                                    <dt><strong>forum_page</strong></dt>
                                    <dd>Indicating a user viewed a forum or topic page</dd>

                                    <dt><strong>forum_topic_content*</strong></dt>
                                    <dd>Indicating a user posted a topic or replied to an existing topic</dd>

                                </dl>

                                <h3>Gravity Forms and Contact Form 7 events supported:</h3>

                                <dl>

                                    <dt><strong>contact_form_submitted*</strong></dt>
                                    <dd>Indicating a visitor used the contact form to get in touch</dd>

                                </dl>

                                * indicates that a semantic assessment was performed on the words accompanying these events

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>


    <div id="kontxt-analyze-results-status" class="wrap"></div>

    <script>
        jQuery(function($) {
            kontxtJourney();
        });
    </script>

</div>