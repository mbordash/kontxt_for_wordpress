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

<!-- Facebook Pixel Code -->
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '926208751141145');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=926208751141145&ev=PageView&noscript=1"
    /></noscript>
<!-- End Facebook Pixel Code -->