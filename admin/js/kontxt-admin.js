( function( wp ) {


    var KontxtButton = function( props ) {
        return wp.element.createElement(
            wp.editor.RichTextToolbarButton, {
                icon: 'editor-code',
                title: 'KONTXT',
                onClick: function() {
                    console.log( 'toggle format' );

                    var selected_text = props.value;
                    if (selected_text === "") {
                        selected_text = props.attributes.content;
                    }

                    kontxtHandleFormPost('Welcome to WordPress. This is your first post. Edit or delete it, then start writing! Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
                },
            }
        );
    }
    wp.richText.registerFormatType(
        'kontxt/analyze-content-button', {
            title: 'KONTXT',
            tagName: 'kontxt',
            className: null,
            edit: KontxtButton,
        }
    );
} )( window.wp );

jQuery(function($) {


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

                window.dispatchEvent(new Event('resize'));

            }

        });
    });


});

function kontxtHandleFormPost(return_text) {

    jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

    if ( !return_text || return_text.length === 0 ) {

        $('#kontxt-results-status').html('<p>You haven\'t entered any content yet. Please enter some content before trying to analyze.</p>');
        return false;
    }

    if ( return_text && return_text.length <= 100 ) {

        $('#kontxt-results-status').html('<p>You haven\'t entered enough content yet. Please enter at least 100 characters before trying to analyze.</p>');
        return false;
    }

    $('#kontxt-results-status').hide();
    $('#kontxt-results-success').show();

    //$('#kontxt_text_to_analyze').val(return_text);

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

            toneAnalysis = 'We detected a <strong>' + sentimentText + '</strong> sentiment with an offset of ' + sentimentScore + ' from neutral using a range of -1 to 1.'


            jQuery('#overall_tone').html( toneAnalysis ).show();

            nv.addGraph(function() {

                var chart = nv.models.multiBarHorizontalChart()

                    .x(function(d) {return d.label})
                    .y(function(d) {return d.value})
                    .forceY([-1,1])
                    .showLegend(false)
                    .showControls(false)
                    .showValues(true);


                d3.select("#sentiment_chart svg")
                    .datum(sentimentData())
                    .transition().duration(1200)
                    .call(chart);
            });

            function sentimentData() {
                return  [{
                    key: "Sentiment",
                    values: [{
                        "label": "Sentiment",
                        "value": sentimentScore
                    }]
                }]
            }


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

            var jsArr = [];

            var counter = 0;
            for( var elem in jsonResponse ) {
                jsArr[counter] = {
                    'key': elem,
                    'y': jsonResponse[elem]
                };
                counter++;
            }

            var height = 400;
            var width = 400;

            nv.addGraph(function() {
                var chart = nv.models.pieChart()
                    .x(function(d) { return d.key })
                    .y(function(d) { return d.y })
                    .width(width)
                    .height(height)
                    .labelType('percent')
                    .labelSunbeamLayout(true);

                d3.select("#emotion_chart svg")
                    .datum(jsArr)
                    .transition().duration(1200)
                    .attr('height', height)
                    .call(chart);

                return chart;
            });


        },
        error: function(response) {
            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        }
    });


    return false;
};