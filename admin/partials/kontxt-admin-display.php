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

    <p>KONTXT services log requests for deep semantic analytics. In order to enable our analytical functions and predictive insights, KONTXT will encrypt, log and process anonymous behaviors from your content and cmmerce customers.
        This is only done to deliver and improve the machine learning functions that power our deep analytics and future product recommendations.
        If you do not wish KONTXT to perform our services, please deactivate and delete this plugin. All site data is deleted within 7 days of the last recorded activity.</p>

    <p>The data we use is encrypted, does not contain any personally identifiable information, and is not shared, sold, or made public. Should you have any GDPR requests, please <a href="https://kontxt.com/more-information/">contact us</a>. </p>

    <p>KONTXT &copy; is a service provided by RealNetworks. For more information on our terms of use and data usage, please visit https://www.realnetworks.com.</p>

    <form action="options.php" method="post">

        <?php

            settings_fields( $this->plugin_name );
            do_settings_sections( $this->plugin_name );
            submit_button();

        ?>

    </form>

    <p>KONTXT provides semantic and journey analysis for the following core functions and plugins:</p>

    <ul>
        <li> &middot; Wordpress: view events: home, search, categories, articles, pages, comments</li>
        <li> &middot; Woo Commerce view events: shop home, search, categories, products, account, reviews</li>
        <li> &middot; Woo Commerce actions: product added to cart, transaction completed</li>
        <li> &middot; Contact Form 7: comment sent</li>
    </ul>

    <p>If you use a plugin not listed here that could benefit from KONTXT machine learning, please <a href="https://kontxt.com/more-information/">contact us</a>.</p>


    <p>&copy;2019 RealNetworks, Inc.</p>


</div>