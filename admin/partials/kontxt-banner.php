<?php
/**
 * Created by PhpStorm.
 * User: michaelbordash
 * Date: 4/21/17
 * Time: 11:04 PM
 */

$optin = get_option( $this->option_name . '_optin' );

if( $optin === 'no' || $optin === false || !$optin || !isset($optin) || $optin === '' ) {
	?>

    <!-- This file should primarily consist of HTML with a little bit of PHP. -->

    <div class="notice notice-info is-dismissible inline">
        <p>
            In order for KONTXT to function properly, you must opt-in to our services. Please visit the <a href="<?php echo admin_url( 'admin.php?page=kontxt_settings' ); ?>">KONTXT settings page</a> to opt-in.
        </p>
    </div>

	<?php

}
?>

<img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid=1880329&conversionId=1851474&fmt=gif" />
