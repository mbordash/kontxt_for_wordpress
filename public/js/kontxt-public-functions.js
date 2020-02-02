jQuery(function($) {

    // check for events (viewProductID, pageID, searchText, reviewTextPresent )
    // loop through API calls to cognitive

    let recsType;

    let data = {
        'action'    : 'kontxt_send_event',
        'apikey'    : kontxtAjaxObject.apikey,
        'eventData' : JSON.stringify(kontxtUserObject)
    };

    // check first to see if div is present, this means we're on a page that's supposed to have recs
    // and only include the request for the appropriate rec on ajax callback success

    if( document.getElementById( 'kontxt_product_recs' ) !== null ) {

        recsType = '#kontxt_product_recs';
        data['return_product_recs'] = kontxtAjaxObject['return_product_recs'];

    }

    if( document.getElementById( 'kontxt_content_recs' ) !== null ) {

        recsType = '#kontxt_content_recs';
        data['return_content_recs'] = kontxtAjaxObject['return_content_recs'];

    }

    jQuery.ajax({

        type: 'post',
        url: kontxtAjaxObject.ajaxurl,
        security: kontxtAjaxObject['security'],
        data: jQuery.param( data ),
        action: 'kontxt_send_event',
        cache: false,
        success: function (response) {


            if( response.status !== 'error' ) {
                // if there's an error we want no impact to UX, so we will ignore

                let jsonResponse    = JSON.parse(response);
                let arraySize       = jQuery(jsonResponse).length;
                let recsList;

                // if we received a payload with recs, let's build an output array and enable the div

                if( arraySize >= 1 ) {

                    if( recsType === '#kontxt_product_recs' ) {

                        recsList = '<ul class="product_list_widget">';
                        counter = 0;

                        for (let item in jsonResponse) {

                            counter++;

                            recsList += '<li>' +
                                '<span class="product-title"><a class="woocommerce-LoopProduct-link woocommerce-loop-product__link" href="' + jsonResponse[item]['item_url'] + '">' + jsonResponse[item]['item_image'] +
                                '' + jsonResponse[item]['item_name'] + '</span></a>' +
                                '</li>';
                            if (counter >= 3) {
                                break;
                            }
                        }

                        recsList += '</ul>';

                    }

                    if( recsType === '#kontxt_content_recs' ) {

                        recsList = '<div><ul class="kontxt_content_recs">';
                        counter = 0;

                        for (let item in jsonResponse) {

                            counter++;

                            recsList += '<li>' +
                                '<a aria-current="page" href="' + jsonResponse[item]['item_url'] + '">' + jsonResponse[item]['item_name'] + '</a>' +
                                '</li>';
                            if (counter >= 3) {
                                break;
                            }
                        }

                        recsList += '</ul></div>';

                    }

                    jQuery( recsType ).show();
                    jQuery( '#kontxt_recs_objects' ).html( recsList );


                }

            }

        }
    });

});
