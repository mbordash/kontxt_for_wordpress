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


    <h2>Experiment</h2>

    <div class="inside">

        <div id="kontxt-results-box" class="wrap">

            <h4>Enter some content and let KONTXT analyze it for you</h4>
            <p>Experiment with product descriptions, email marketing content and blog article content to understand how your audience might react to your tone.</p>

            <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">

                <div id="kontxt-input-text">

                    <textarea id="kontxt-input-text-field" name="kontxt-input-text-field" cols="80" rows="3" class="large-text">Fourscore and seven years ago our fathers brought forth, on this continent, a new nation, conceived in liberty, and dedicated to the proposition that all men are created equal. Now we are engaged in a great civil war, testing whether that nation, or any nation so conceived, and so dedicated, can long endure. We are met on a great battle-field of that war. We have come to dedicate a portion of that field, as a final resting-place for those who here gave their lives, that that nation might live. It is altogether fitting and proper that we should do this. But, in a larger sense, we cannot dedicate, we cannot consecrate—we cannot hallow—this ground. The brave men, living and dead, who struggled here, have consecrated it far above our poor power to add or detract. The world will little note, nor long remember what we say here, but it can never forget what they did here. It is for us the living, rather, to be dedicated here to the unfinished work which they who fought here have thus far so nobly advanced. It is rather for us to be here dedicated to the great task remaining before us—that from these honored dead we take increased devotion to that cause for which they here gave the last full measure of devotion—that we here highly resolve that these dead shall not have died in vain—that this nation, under God, shall have a new birth of freedom, and that government of the people, by the people, for the people, shall not perish from the earth.</textarea>

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