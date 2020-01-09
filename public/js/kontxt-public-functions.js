jQuery(function($) {

    // check for events (viewProductID, pageID, searchText, reviewTextPresent,  )
    // loop through API calls to cognitive

    const security = kontxtAjaxObject['security'];
    const postUrl = kontxtAjaxObject['ajaxurl'];

    jQuery.ajax({
        type: 'post',
        url: postUrl,
        security: security,
        data: 'action=kontxt_send_event&eventData=' + encodeURIComponent(JSON.stringify(kontxtUserObject)),
        action: 'kontxt_send_event',
        cache: false,
        success: function (response) {

        }
    });

});
