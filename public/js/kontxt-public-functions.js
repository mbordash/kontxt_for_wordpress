jQuery(function($) {

    // check for events (viewProductID, pageID, searchText, reviewTextPresent,  )
    // loop through API calls to cognitive

    var data = jQuery.param({
        'action': 'kontxt_send_event',
        'apikey': kontxtAjaxObject.apikey,
        'request_id': makeid(20)
    });

    var security = kontxtAjaxObject.security;

    jQuery.ajax({
        type: 'post',
        url: kontxtAjaxObject.ajaxurl,
        security: security,
        data: data + '&service=intents',
        action: 'kontxt_send_event',
        cache: false,
        success: function (response) {

        }
    })

})


function makeid(length) {
    // from SO: https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript

    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}