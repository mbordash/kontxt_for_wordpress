jQuery(function($) {

    $( "#datepicker" ).datepicker();

    // Experiments UI

    document.addEventListener('visibilitychange', () => {
        console.log(document.visibilityState);
        window.dispatchEvent(new Event('resize'));
    });

    // capture KONTXT form post and pass to handler

    jQuery( '#kontxt-experiment-input-button' ).click( function( e ) {
        e.preventDefault();

        var textToAnalyze =  jQuery( '#kontxt-input-text-field' ).val();

        kontxtExperimentFormPost( textToAnalyze )

    });

    // tab controller navigation

    var navTabs = jQuery( '#kontxt-results-navigation' ).children( '.nav-tab-wrapper' ),
        tabIndex = null;

    navTabs.children().each(function() {

        let dimension = 'sentiment';

        kontxtAnalyze( dimension );

        $(this).on('click', function (evt) {

            evt.preventDefault();

            // If this tab is not active...


            if (!$(this).hasClass('nav-tab-active')) {

                dimension = $(this).text().toLowerCase();

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
                    .children('div:nth-child(' + (tabIndex) + ')')
                    .addClass('hidden');

                // And display the new content
                $('#kontxt-results-navigation')
                    .children('div:nth-child( ' + (tabIndex + 2) + ')')
                    .removeClass('hidden');

                kontxtAnalyze(dimension);

            }
        });
    });
});

function kontxtAnalyze( dimension ) {

    jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

    jQuery('#kontxt-analyze-results-status').hide();
    jQuery('#kontxt-analyze-results-success').show();

    // prepare data for posting

    var data = jQuery.param({
        'action':       'kontxt_analyze_results',
        'apikey':       kontxtAjaxObject.apikey,
        'dimension':    dimension
    });

    var security = kontxtAjaxObject.security;

    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        security: security,
        data: data,
        cache: false,
        success: function(response) {

            if( response.status == 'error' ) {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                jQuery('#kontxt-analyze-results-success').hide();
                return false;
            }

            Plotly.purge('analyze_results_chart');

            jQuery('#analyze_results_title').html(dimension + ' analytics ').css('text-transform', 'capitalize');

            var jsonResponse = jQuery.parseJSON(response);
            // var jsonResponse = response;

            var eventDates = jsonResponse.map(function(e) {
                return Date.parse(e.event_date);
            });

            var eventValues = jsonResponse.map( function(e) {
                return e.event_value;
            });

            var groupBy = function(xs, key) {
                return xs.reduce(function(rv, x) {
                    (rv[x[key]] = rv[x[key]] || []).push(x);
                    return rv;
                }, {});
            };

            let data = [];
            let layout;
            let contentTable;

            switch( dimension ) {

                case 'sentiment':
                    data = [{
                        type: 'scatter',
                        fill: 'tozeroy',
                        y: eventValues,
                        x: eventDates
                    }];

                    layout = {
                        yaxis: {
                            range: [-1, 1]
                        },
                        xaxis: {
                            autorange: true,
                            type: 'date'
                        }
                    }

                    contentTable = '<table id="analyze_results_id" class="widefat"><thead><th>Date</th><th>Average</th></thead><tbody>';
                    for( var elem in jsonResponse ) {
                        contentTable  += '<tr><td>' + jsonResponse[elem]['event_date']  + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['event_value'] + '</td></tr>';

                    }
                    contentTable += '</tbody></table>';

                    break;

                case 'intents':

                    var groupByIntent = groupBy(jsonResponse, 'event_value_name' );

                    for( var elem in groupByIntent ) {

                        var eventValueDate = groupByIntent[elem].map( function(e) {
                            return  Date.parse(e.event_date);
                        });

                        var eventValueCount = groupByIntent[elem].map( function(e) {
                            return e.event_value_count;
                        });

                        data.push( {
                            x: eventValueDate,
                            y: eventValueCount,
                            name: elem,
                            stackgroup: 'one'
                        } );

                    }

                    console.log(data);

                    layout = {
                        xaxis: {
                            title: 'Date',
                            type: 'date'
                        },
                        yaxis: {
                            title: 'Frequency'
                        }
                    }

                    contentTable = '<table id="analyze_results_id" class="widefat"><thead><th>Date</th><th>Name</th><th>Count</th></thead><tbody>';
                    for( var elem in jsonResponse ) {
                        contentTable  += '<tr><td>' + jsonResponse[elem]['event_date']  + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['event_value_name'] + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['event_value_count'] + '</td></tr>';

                    }
                    contentTable += '</tbody></table>';

                    break;

                case 'emotion':

                    // there's a much cleaner way to do this that is not brute force, please have at it!
                    // Mjb

                    let joy = [];
                    let fear = [];
                    let anger = [];
                    let disgust = [];
                    let sadness = [];

                    jsonResponse.forEach( function( element ) {

                        emoJson = JSON.parse( element.event_value_name );

                        joy.push( Math.round(emoJson['joy']*100 ) );
                        fear.push( Math.round(emoJson['fear']*100 ) );
                        anger.push( Math.round(emoJson['anger']*100 ) );
                        disgust.push( Math.round(emoJson['disgust']*100 ) );
                        sadness.push( Math.round(emoJson['sadness']*100 ) );

                    } );

                    joy = {
                        x: eventDates,
                        y: joy,
                        name: 'joy',
                        stackgroup: 'one'
                    };

                    fear = {
                        x: eventDates,
                        y: fear,
                        name: 'fear',
                        stackgroup: 'one'
                    };

                    anger = {
                        x: eventDates,
                        y: anger,
                        name: 'anger',
                        stackgroup: 'one'
                    };

                    disgust = {
                        x: eventDates,
                        y: disgust,
                        name: 'disgust',
                        stackgroup: 'one'
                    };

                    sadness = {
                        x: eventDates,
                        y: sadness,
                        name: 'sadness',
                        stackgroup: 'one'
                    };

                    data = [ joy, fear, anger, disgust, sadness ];

                    layout = {
                        xaxis: {
                            title: 'Date',
                            type: 'date'
                        },
                        yaxis: {
                            title: 'Emotion distribution',
                            tickformat: ',.0',
                            range: [0,100]
                        }
                    }

                    console.log(data);

                    contentTable = '<table id="analyze_results_id" class="widefat"><thead><th>Date</th><th>Name/Average</th></thead><tbody>';
                    for( var elem in jsonResponse ) {
                        contentTable  += '<tr><td>' + jsonResponse[elem]['event_date']  + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['event_value_name'] + '</td></tr>';

                    }
                    contentTable += '</tbody></table>';

                    break;

                case 'keywords':

                    contentTable = '<table id="analyze_results_id" class="widefat"><thead><th>Extracted keyword</th><th>Count</th></thead><tbody>';
                    for( var elem in jsonResponse ) {
                        contentTable  += '<tr><td>' + jsonResponse[elem]['keywords']  + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['keywords_count'] + '</td></tr>';

                    }
                    contentTable += '</tbody></table>';

                    break;

                case 'concepts':

                    contentTable = '<table id="analyze_results_id" class="widefat"><thead><th>Extracted concept</th><th>Count</th></thead><tbody>';
                    for( var elem in jsonResponse ) {
                        contentTable  += '<tr><td>' + jsonResponse[elem]['concepts']  + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['concepts_count'] + '</td></tr>';

                    }
                    contentTable += '</tbody></table>';

                    break;
            }

            if( data.length > 0 ) {
                Plotly.newPlot('analyze_results_chart', data, layout);
            }

            jQuery('#analyze_results_table').html( contentTable ).show();

            jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');
        },
        error: function(response) {
            jQuery('#kontxt-results-status').html(response.message);
            return false;
        }

    });

    return false;
};


function kontxtExperimentFormPost(return_text) {

    jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

    if ( !return_text || return_text.length === 0 ) {

        jQuery('#kontxt-results-status').html('<p>You haven\'t entered any content yet. Please enter some content before trying to analyze.</p>');
        jQuery('#spinner').removeClass('is-active').addClass('is-inactive');

        return false;
    }

    jQuery('#kontxt-results-status').hide();
    jQuery('#kontxt-results-success').show();

    // jQuery('#kontxt-text-to-analyze').val(return_text);

    // prepare data for posting

    var data = jQuery.param({
        'kontxt_text_to_analyze': return_text,
        'action': 'kontxt_analyze',
        'apikey': kontxtAjaxObject.apikey,
        'request_id': makeid(20)
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
                jQuery('#kontxt-results-status').html(response.message).show();
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
                jQuery('#kontxt-results-status').html(response.message).show();
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
                jQuery('#kontxt-results-status').html(response.message).show();
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

            let barColor = 'rgba(55,128,191,0.6)';

            if( sentimentScore < 0 ) {
                barColor = 'rgba(255,0,50,0.6)'
            }

            var data = [{
                type: 'bar',
                y: [sentimentScore],
                x: ['Sentiment'],
                orientation: 'v',
                marker: {
                    color: barColor
                }
            }];

            var layout = {
                yaxis: {
                    range: [-1, 1]
                },
                height: '450',
                margin: {
                    l: 20,
                    r: 20,
                    b: 30,
                    t: 20
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
                type: 'scatterpolar',
                r: emotionValues,
                theta: emotionLabels,
                fill: 'toself',
                name: 'Emotions detected'
            }];


            var layout = {
                width: 'auto',
                height: 'auto',
                polar: {
                    radialaxis: {
                        visible: true,
                        range: [0, 100]
                    }
                },
                showlegend: true,
                margin: {
                    l: 10,
                    r: 10,
                    b: 20,
                    t: 10
                }
            };

            Plotly.newPlot('emotion_chart', data, layout, {displayModeBar: false});

        },
        error: function(response) {
            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        }
    });

    return false;
};



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