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

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <div id="activity-results-success" class="inside hidden">

        <div id="activity-results-box" class="wrap">

            <div class="wrap">

                <div id="poststuff">

                    <div class="postbox">

                        <div class="inside">

                            <div id="latestActivity_results_table"></div>

                        </div>

                    </div>
                    <!-- .postbox -->
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(function($) {
            kontxtAnalyze('latestActivity');
        });
    </script>

</div>