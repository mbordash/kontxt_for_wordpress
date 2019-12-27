jQuery(function($) {

    // check for events (viewProductID, pageID, searchText, reviewTextPresent,  )
    // loop through API calls to cognitive

    var security = kontxtAjaxObject['security'];
    var postUrl =  kontxtAjaxObject['ajaxurl'];

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