<?php
/**
 * Created by PhpStorm.
 * User: michaelbordash
 * Date: 4/21/17
 * Time: 11:04 PM
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="notice notice-info is-dismissible inline">
    <p>
        This is a <strong>PREVIEW (alpha)</strong> release of KONTXT Semantic Engine for Wordpress.  We are actively improving this plugin -- we thank you for trying it out!
        If you have any questions, comments, suggestions about our service, please <a target="_blank" href="https://kontxt.com/more-information/">contact us</a>.
		<?php
		printf(
		// translators: Leave always a hint for translators to understand the placeholders.
			esc_attr__( '', 'WpAdminStyle' ),
			'<code>.notice-info</code>',
			'<code>.is-dismissible</code>',
			'<code>.inline</code>'
		);
		?>
    </p>
</div>

<div class="wrap">

    <div id="kontxt-settings-navigation">

        <h2 class="nav-tab-wrapper current">
            <a href="javascript:;" class="nav-tab nav-tab-active">Configuration</a>
            <a href="javascript:;" class="nav-tab">Event log</a>
            <a href="javascript:;" class="nav-tab">Experiment</a>
        </h2>

        <div class="inside">

            <div id="poststuff">

                <div class="postbox">

                    <div class="inside">

                        <h3>Opt-in for KONTXT Semantic Analytics</h3>

                        <p>In order for KONTXT to work properly, you must opt-in using the form below. If you do not wish KONTXT to perform our services, please deactivate and delete this plugin. All site data is deleted within 7 days of the last recorded activity automatically.</p>

                        <p>KONTXT services log requests for deep semantic analytics. In order to enable our analytical functions and predictive insights, KONTXT will encrypt, log and process anonymous site activity.
                            This is only done to deliver and improve the machine learning functions that power our deep analytics and future product recommendations. The data we use <strong>does not contain any personally identifiable information</strong>, and is not shared, sold, or made public. Should you have any GDPR requests, please <a href="https://kontxt.com/more-information/">contact us</a>. </p>


                        <form action="options.php" method="post">

                            <?php

                                settings_fields( $this->plugin_name );
                                do_settings_sections( $this->plugin_name );
                                submit_button();

                            ?>

                        </form>

                        <p>KONTXT provides semantic and journey analysis for Wordpress core, WooCommerce, Contact Form 7, and Gravity Forms.  If you use a plugin not listed here that could benefit from KONTXT machine learning, please <a href="https://kontxt.com/more-information/">contact us</a>.</p>

                        <p>KONTXT&trade; is a service provided by &copy;RealNetworks, Inc. For more information on our terms of use and data usage, please visit https://www.realnetworks.com.</p>

                     </div>
                </div>
            </div>
        </div>

        <div class="inside hidden">

            <div id="poststuff">

                    <div class="inside">

                        <div id="latestActivity_results_table"></div>

                    </div>

            </div>

            <script>
                jQuery(function($) {
                    kontxtAnalyze('latestActivity');
                });
            </script>
        </div>

        <div class="inside hidden">

            <div id="spinner-analyze" class="spinner is-inactive" style="float: right;"></div>

            <div class="inside">

                <div id="kontxt-results-box" class="wrap">

                    <h4>Enter some content and let KONTXT analyze it for you</h4>
                    <p>Experiment with product descriptions, email marketing content and blog article content to understand how your audience might react to your tone.</p>

                    <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">

                        <div id="kontxt-input-text">

                            <textarea id="kontxt-input-text-field" name="kontxt-input-text-field" cols="80" rows="1" class="large-text">How do I change a lightbulb?</textarea>

                            <input id="kontxt-experiment-input-button" class="button-primary" type="submit" value="Analyze" />

                        </div>

                    </form>

                    <div id="kontxt-results-success" class="hidden">

                        <div class="wrap">

                            <div id="poststuff">

                                <div id="post-body" class="metabox-holder columns-2">

                                    <!-- main content -->
                                    <div id="post-body-content">

                                        <div class="meta-box-sortables ui-sortable">


                                            <div class="postbox">

                                                <h2><span><?php esc_attr_e( 'Emotion', 'WpAdminStyle' ); ?></span></h2>

                                                <div class="inside">
                                                    <div id="emotion_chart"></div>
                                                </div>
                                                <!-- .inside -->

                                            </div>
                                            <!-- .postbox -->

                                            <div class="postbox">

                                                <h2><span><?php esc_attr_e( 'Semantic Intents', 'WpAdminStyle' ); ?></span></h2>

                                                <div class="inside">
                                                    <div id="intents_chart">
                                                        <table id="kontxt_intents"></table>
                                                    </div>
                                                </div>
                                                <!-- .inside -->

                                            </div>
                                            <!-- .postbox -->

                                            <div class="postbox">

                                                <h2><span><?php esc_attr_e('Keywords', 'WpAdminStyle' ); ?></span></h2>

                                                <div class="inside">
                                                    <div id="keywords_chart">
                                                        <table id="kontxt_keywords"></table>
                                                    </div>
                                                </div>
                                                <!-- .inside -->

                                            </div>
                                            <!-- .postbox -->

                                        </div>
                                        <!-- .meta-box-sortables .ui-sortable -->

                                    </div>
                                    <!-- post-body-content -->

                                    <!-- sidebar -->
                                    <div id="postbox-container-1" class="postbox-container">

                                        <div class="meta-box-sortables">

                                            <div class="postbox">

                                                <h2><span><?php esc_attr_e( 'Sentiment', 'WpAdminStyle' ); ?></span></h2>

                                                <div class="inside">
                                                    <div id="overall_tone"></div>
                                                    <div id="sentiment_chart"></div>
                                                </div>
                                                <!-- .inside -->

                                            </div>
                                            <!-- .postbox -->


                                        </div>
                                        <!-- .meta-box-sortables -->

                                    </div>
                                    <!-- #postbox-container-1 .postbox-container -->

                                </div>
                                <!-- #post-body .metabox-holder .columns-2 -->

                                <br class="clear">
                            </div>
                            <!-- #poststuff -->

                        </div> <!-- .wrap -->

                    </div>

                </div>

            </div>

            <div id="kontxt-analyze-results-status" class="wrap"></div>

        </div>

    </div>

</div>