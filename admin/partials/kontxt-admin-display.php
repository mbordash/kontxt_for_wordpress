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

    <p>By default, all KONTXT services DO NOT log requests for deep analytics. In order to enable our Analytics functions, KONTXT will need to log your
        Wordpress customer and administrative behaviors.  This is only done to deliver and improve the machine learning functions that power our deep analytics.</p>

    <p>The logged data is encrypted, does not contain any personally identifiable information, and is not shared or made public.</p>

    <p>KONTXT is a service provided by RealNetworks. For more information on our terms of use and data usage, please visit https://www.realnetworks.com.</p>

    <form action="options.php" method="post">

        <?php

            settings_fields( $this->plugin_name );
            do_settings_sections( $this->plugin_name );
            submit_button();

        ?>

    </form>

    <p>&copy;2019 RealNetworks, Inc.</p>


</div>