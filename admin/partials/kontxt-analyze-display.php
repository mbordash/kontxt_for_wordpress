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

    <div id="kontxt-results-navigation">

        <h2 class="nav-tab-wrapper current">

            <a class="nav-tab nav-tab-active" href="javascript:;"><?php esc_attr_e( 'Experiment', 'wp_admin_style' ); ?></a>
            <a class="nav-tab" href="javascript:;"><?php esc_attr_e( 'Analytics', 'wp_admin_style' ); ?></a>

        </h2>

        <div class="inside">

            <div id="kontxt-results-box" class="wrap">

                <h4>Enter some content and let KONTXT analyze it for you</h4>

                <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">

                    <div id="kontxt-input-text">

                        <textarea id="kontxt-input-text-field" name="kontxt-input-text-field" cols="80" rows="3" class="large-text">The full moon warns that you need to plan your moves carefully, especially if they could affect the people around you. You don't live in isolation (though sometimes you think you might like to) so be aware that your actions have consequences.</textarea>

                        <input id="kontxt-input-button" class="button-primary" type="submit" value="Analyze">

                    </div>

                </form>


                <div id="kontxt-results-status" class="wrap"></div>

                <div id="kontxt-results-success" class="hidden">

                    <div id="spinner" class="spinner is-inactive" style="float:none; width:100%; height: auto; padding:10px 0 10px 50px; background-position: center center;"></div>

                    <div class="wrap">

                        <div id="icon-options-general" class="icon32"></div>

                        <div id="poststuff">

                            <div id="post-body" class="metabox-holder columns-2">

                                <!-- main content -->
                                <div id="post-body-content">

                                    <div class="meta-box-sortables ui-sortable">

                                        <div class="postbox">

                                            <h2><span><?php esc_attr_e( 'Sentiment', 'WpAdminStyle' ); ?></span></h2>

                                            <div class="inside">
                                                <div id="overall_tone"></div>
                                                <div id="sentiment_chart"></div>
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

                                            <h2><span><?php esc_attr_e(
					                                    'Keywords & Concepts', 'WpAdminStyle'
				                                    ); ?></span></h2>

                                            <div class="inside">
                                                <div id="keywords_chart">
                                                    <table id="kontxt_keywords"></table>
                                                </div>
                                                <br />
                                                <div id="concepts_chart">
                                                    <table id="kontxt_concepts"></table>
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

                                            <h2><span><?php esc_attr_e( 'Emotion', 'WpAdminStyle' ); ?></span></h2>

                                            <div class="inside">
                                                <div id="emotion_chart"></div>
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

        <div class="inside hidden">
            <h3>Analytics</h3>

            <div id="kontxt-results-box" class="wrap">

                <h4>Deep analytics are enabled once you opt-in to KONTXT data collection. <a href="options-general.php?page=kontxt">Edit your settings</a>.</h4>

            </div>
        </div>
    </div>
</div>