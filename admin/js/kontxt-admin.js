jQuery(function($) {

    // Experiments UI

    document.addEventListener('visibilitychange', () => {
        console.log(document.visibilityState);
        window.dispatchEvent(new Event('resize'));
    });

    // capture KONTXT form post and pass to handler

    jQuery( '#kontxt-input-button' ).click( function( e ) {
        e.preventDefault();

        var textToAnalyze =  jQuery( '#kontxt-input-text-field' ).val()

        kontxtHandleFormPost( textToAnalyze )

    });

    // craft tab controller navigation

    var navTabs = jQuery( '#kontxt-results-navigation' ).children( '.nav-tab-wrapper' ),
        tabIndex = null;

    navTabs.children().each(function() {

        $(this).on('click', function (evt) {


            evt.preventDefault();

            // If this tab is not active...
            if (!$(this).hasClass('nav-tab-active')) {

                // Unmark the current tab and mark the new one as active
                $('.nav-tab-active').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                // Save the index of the tab that's just been marked as active. It will be 0 - 3.
                tabIndex = jQuery(this).index();

                // Hide the old active content
                $('#kontxt-results-navigation')
                    .children('div:not( .inside.hidden )')
                    .addClass('hidden');

                $('#kontxt-results-navigation')
                    .children('div:nth-child(' + ( tabIndex ) + ')')
                    .addClass('hidden');

                // And display the new content
                $('#kontxt-results-navigation')
                    .children('div:nth-child( ' + ( tabIndex + 2 ) + ')')
                    .removeClass('hidden');


            }
        });
    });
});


function kontxtHandleFormPost(return_text) {

    jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

    if ( !return_text || return_text.length === 0 ) {

        jQuery('#kontxt-results-status').html('<p>You haven\'t entered any content yet. Please enter some content before trying to analyze.</p>');
        jQuery('#spinner').removeClass('is-active').addClass('is-inactive');

        return false;
    }

    if ( return_text && return_text.length <= 100 ) {

        jQuery('#kontxt-results-status').html('<p>You haven\'t entered enough content yet. Please enter at least 100 characters before trying to analyze.</p>');
        jQuery('#spinner').removeClass('is-active').addClass('is-inactive');

        return false;
    }

    jQuery('#kontxt-results-status').hide();
    jQuery('#kontxt-results-success').show();

    // jQuery('#kontxt-text-to-analyze').val(return_text);

    // prepare data for posting

    var data = jQuery.param({
        'post_ID': kontxtAjaxObject.postID,
        'kontxt_text_to_analyze': return_text,
        'action' : 'kontxt_analyze',
        'apikey' : kontxtAjaxObject.apikey,
    });

    var security = kontxtAjaxObject.security;

    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        security: security,
        data: data + '&service=intents',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response) {

            if( response.status == 'error' ) {
                jQuery('#kontxt-results-success').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            var jsonResponse = jQuery.parseJSON(response);

            var contentTable = '<table id="kontxt_intents" class="widefat"><thead><th>Intent</th><th>Relevance</th><th>Accurate?</th></th></thead><tbody>';
            for( var elem in jsonResponse ) {
                contentTable  += '<tr><td>' + jsonResponse[elem]['class_name'] + '</td>';
                contentTable  += '<td>' + ( Math.round(jsonResponse[elem]['confidence'] * 100 )) + '%</td>';
                contentTable  += '<td><a href="">Yes</a> | <a href="">No</a></td></tr>';

            }
            contentTable += '</tbody></table>';

            jQuery('#intents_chart').html( contentTable ).show();

            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        },
        error: function(response) {
            jQuery('#kontxt-results-status').html(response.message);
            return false;
        }

    });

    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        security: security,
        data: data + '&service=concepts',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response) {

            if( response.status == 'error' ) {
                jQuery('#kontxt-results-success').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            var jsonResponse = jQuery.parseJSON(response);

            var contentTable = '<table id="kontxt_concepts" class="widefat"><thead><th>Concept</th><th>Relevance</th></thead><tbody>';
            for( var elem in jsonResponse ) {
                contentTable  += '<tr><td>' + jsonResponse[elem]['text'] + '</td>';
                contentTable  += '<td>' + ( Math.round(jsonResponse[elem]['relevance'] * 100 )) + '%</td></tr>';
            }
            contentTable += '</tbody></table>';

            jQuery('#concepts_chart').html( contentTable ).show();

            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        },
        error: function(response) {
            jQuery('#kontxt-results-status').html(response.message);
            return false;
        }

    });

    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        security: security,
        data: data + '&service=keywords',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response){

            if( response.status == 'error' ) {
                jQuery('#kontxt-results-success').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            var jsonResponse = jQuery.parseJSON(response);

            var contentTable = '<table id="kontxt_keywords" class="widefat"><thead><th>Keyword</th><th>Relevance</th></thead><tbody>';
            for( var elem in jsonResponse ) {
                contentTable  += '<tr><td>' + jsonResponse[elem]['text'] + '</td>';
                contentTable  += '<td>' + ( Math.round(jsonResponse[elem]['relevance'] * 100 )) + '%</td></tr>';
            }
            contentTable += '</tbody></table>';

            jQuery('#keywords_chart').html( contentTable ).show();

            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        },
        error: function(response) {
            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        }
    });

    jQuery.ajax({

        type: 'post',
        url: ajaxurl,
        security: security,
        data: data + '&service=sentiment',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response){

            if( response.status == 'error' ) {
                jQuery('#kontxt-results-status').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            var jsonResponse = jQuery.parseJSON(response);

            var arraySize = jQuery(jsonResponse).size();
            var sentimentText;
            var sentimentScore;

            if ( arraySize === 3 ) {
                sentimentText = jsonResponse[2];
                sentimentScore = jsonResponse[1];
            } else {
                sentimentScore = jsonResponse[0];

                if (sentimentScore > 0 ) {
                    sentimentText = "positive";
                } else {
                    sentimentText = "negative";
                }
            }

            toneAnalysis = 'We detected a <strong>' + sentimentText + '</strong> sentiment with an offset of ' + Math.round(sentimentScore * 100 ) / 100 + ' from neutral using a range of -1 to 1.'

            jQuery('#overall_tone').html( toneAnalysis ).show();

            function sentimentData() {
                return  [{
                    key: "Sentiment",
                    values: [{
                        "label": "Sentiment",
                        "value": sentimentScore
                    }]
                }]
            }

            let barColor = 'rgba(55,128,191,0.6)';

            if( sentimentScore < 0 ) {
                barColor = 'rgba(255,0,50,0.6)'
            }

            var data = [{
                type: 'bar',
                y: ['Sentiment'],
                x: [sentimentScore],
                orientation: 'h',
                marker: {
                    color: barColor
                }
            }];

            var layout = {
                xaxis: {
                    range: [-1, 1]
                },
                height: 100,
                margin: {
                    l: 100,
                    r: 100,
                    t: 20,
                    b: 20
                }
            }

            Plotly.newPlot('sentiment_chart', data, layout, {displayModeBar: false});


            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        },
        error: function(response) {
            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        }

    });

    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        security: security,
        data: data + '&service=emotion',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response){

            if( response.status == 'error' ) {
                jQuery('#kontxt-results-status').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            var jsonResponse = jQuery.parseJSON(response);

            var emotionLabels = [];
            var emotionValues = [];

            var counter = 0
            for( var elem in jsonResponse ) {
                emotionLabels[counter] = elem
                emotionValues[counter] = Math.round(jsonResponse[elem]*100)
                counter++
            }

            var pieColors = [
                ['rgb(101,195,166)', 'rgb(252,141,98)', 'rgb(141,160,204)', 'rgb(232,138,196)', 'rgb(166, 217, 83)', 'rgb(255, 217, 47)' ]
            ];

            var data = [{
                values: emotionValues,
                labels: emotionLabels,
                type: 'pie',
                hoverinfo: 'label+percent',
                textposition: 'inside',
                marker : {
                    colors : pieColors[0]
                },

            }];


            var layout = {
                height : 350,
                width : 260,
                showlegend : true,
                legend : {"orientation": "h"},
                margin: {"t": 0, "b": 0, "l": 0, "r": 0},
            };

            Plotly.newPlot('emotion_chart', data, layout, {displayModeBar: false});

        },
        error: function(response) {
            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        }
    });

    return false;
};