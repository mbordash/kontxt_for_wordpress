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

    <h2><?php echo __('Keywords Extracted', 'kontxt'); ?></h2>

    <div id="keywords" class="inside">

        <div id="keywords-results-box" class="wrap">

            <div id="keywords-results-success">

                <div class="wrap">

                    <div id="poststuff">

                        <div class="postbox">

                            <div class="inside">

                                <div id="keywords_results_table"></div>

                            </div>
                        </div>

                        <div class="postbox">

                            <div class="inside">

                                <?php

                                    echo wp_kses(__('
    
                                        <h3>About</h3>
        
                                        <p>Our machine learning engine analyzes all in-bound customer communication (including search input, product reviews, contact forms) to extract and aggregate every meaningful keyword found within each interaction.
        
                                            Use these keywords to optimize your SEO strategy by embedding them in your meta data, blog content, and product descriptions.
        
                                            These keywords should also be used to optimize your advertising campaigns on external search platforms.
                                    ', 'kontxt'), $allowed_html );

                                ?>

                                </p>
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
            "use strict";
            kontxtAnalyze('keywords');
        });
    </script>

</div>