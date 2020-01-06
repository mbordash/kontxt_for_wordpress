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

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <div id="poststuff">

        <div class="postbox">

            <div class="inside">

                <h3>Opt-in for KONTXT Semantic Analytics</h3>

                <p>In order for KONTXT to work properly, you must opt-in using the form below. If you do not wish KONTXT to perform our services, please deactivate and delete this plugin. All site data is deleted within 7 days of the last recorded activity automatically.</p>

                <p>KONTXT services log requests for deep semantic analytics. In order to enable our analytical functions and predictive insights, KONTXT will encrypt, log and process anonymous site activity.
                    This is only done to deliver and improve the machine learning functions that power our deep analytics and future product recommendations. The data we use <strong>does not contain any personally identifiable information</strong>, and is not shared, sold, or made public. Should you have any GDPR requests, please <a href="https://kontxt.com/more-information/">contact us</a>. </p>

            </div>
        </div>
    </div>




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


    <p>KONTXT&trade; is a service provided by &copy;RealNetworks, Inc. For more information on our terms of use and data usage, please visit https://www.realnetworks.com.</p>


</div>