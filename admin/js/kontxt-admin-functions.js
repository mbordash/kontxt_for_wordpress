jQuery(function($) {

    "use strict";

    let navTabs = jQuery('#kontxt-settings-navigation').children('.nav-tab-wrapper');
    let tabIndex = null;

    navTabs.children().each(function() {

        $(this).on('click', function (evt) {

            evt.preventDefault();

            // If this tab is not active...
            if (!$(this).hasClass('nav-tab-active')) {

                // Unmark the current tab and mark the new one as active
                $('.nav-tab-active').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                // Save the index of the tab that's just been marked as active. It will be 0 - 2.
                tabIndex = jQuery(this).index();

                // Hide the old active content
                $('#kontxt-settings-navigation')
                    .children('div:not( .inside.hidden )')
                    .addClass('hidden');

                $('#kontxt-settings-navigation')
                    .children('div:nth-child(' + ( tabIndex ) + ')')
                    .addClass('hidden');

                // And display the new content
                $('#kontxt-settings-navigation')
                    .children('div:nth-child( ' + ( tabIndex + 2 ) + ')')
                    .removeClass('hidden');

                window.dispatchEvent(new Event('resize'));

            }

        });
    });


    $( "#date_from" ).datepicker({ dateFormat: "yy-mm-dd" }).datepicker("setDate", "-7d");

    $( "#date_to" ).datepicker({dateFormat: "yy-mm-dd"}).datepicker('setDate', new Date());

    // capture KONTXT form post and pass to handler
    jQuery( '#kontxt-experiment-input-button' ).on( 'click', function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        let textToAnalyze =  jQuery( '#kontxt-input-text-field' ).val();

        kontxtExperimentFormPost( textToAnalyze );
    });

    // capture date range redraw
    jQuery( '#kontxt-events-date' ).on( 'click',  function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        let dimension =  jQuery( '#dimension' ).val();
        let date_from =  jQuery( '#date_from' ).val();
        let date_to =  jQuery( '#date_to' ).val();
        let filter = jQuery( '#filter').val();

        if( dimension === 'journey' ) {
            kontxtJourney('journeyEvents', date_from, date_to, filter);
        } else {
            kontxtAnalyze(dimension, date_from, date_to, filter );
        }
    });

    // capture intent redraw
    jQuery( '#kontxt-intent-overlay' ).on( 'click',function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        let overlay     = jQuery('#overlay').val();
        let dimension   = jQuery('#dimension').val();
        let date_from;
        let date_to;

        if ( Date.parse( jQuery( '#date_from' ).val() ) ) {
            date_from =  jQuery( '#date_from' ).val();
        }
        if ( Date.parse( jQuery( '#date_to' ).val() ) ) {
            date_to =  jQuery( '#date_to' ).val();
        }

        if( overlay ) {
            kontxtOverlay( overlay, date_from, date_to );
        } else {
            kontxtAnalyze( dimension, date_from, date_to);
        }

    });

    // capture intent filter
    jQuery( '#kontxt-intent-filter' ).on( 'click', function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        let filter      =  jQuery( '#filter' ).val();
        let dimension   =  jQuery( '#dimension' ).val();
        let date_from;
        let date_to;

        if ( Date.parse( jQuery( '#date_from' ).val() ) ) {
            date_from =  jQuery( '#date_from' ).val();
        }
        if ( Date.parse( jQuery( '#date_to' ).val() ) ) {
            date_to =  jQuery( '#date_to' ).val();
        }

        if( filter ) {

            switch (dimension) {

                case "sentiment":
                    kontxtAnalyze( 'sentimentByIntent', date_from, date_to, filter );
                    break;

                case "journey":
                    kontxtJourney( 'journeyEventsByIntent', date_from, date_to, filter );
                    break;

            }

        } else {
            switch (dimension) {

                case "journey":
                    kontxtJourney('journeyEvents', date_from, date_to, filter);
                    break;

                default:
                    kontxtAnalyze(dimension, date_from, date_to);
                    break;
            }

        }
    });

});


function kontxtAnalyze( dimension, date_from, date_to, filter) {

    "use strict";

    jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

    jQuery('#kontxt-analyze-results-status').hide();

    // prepare data for posting

    let data = jQuery.param({
        'action':       'kontxt_analyze_results',
        'apikey':       kontxtAjaxObject.apikey,
        'dimension':    dimension,
        'filter':       filter,
        'from_date':    date_from,
        'to_date':      date_to
    });

    const security = kontxtAjaxObject.security;

    jQuery.ajax({
        type: 'post',
        url: kontxtAjaxObject.ajaxurl,
        security: security,
        data: data,
        cache: false,
        success: function(response) {

            if( response.status === 'error' ) {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                return false;
            }

            let jsonResponse = JSON.parse(response);
            // var jsonResponse = response;

            let eventDates = jsonResponse.map(function(e) {
                return Date.parse(e.event_date);
            });

            let eventValues = jsonResponse.map( function(e) {
                return e.event_value;
            });

            let groupBy = function(xs, key) {
                return xs.reduce(function(rv, x) {
                    (rv[x[key]] = rv[x[key]] || []).push(x);
                    return rv;
                }, {});
            };

            let data = [];
            let layout;
            let contentTable;

            switch( dimension ) {

                case 'sentimentByIntent':
                    dimension = 'sentiment';

                case 'sentiment':

                    data = [{
                        type: 'scatter',
                        fill: 'tozeroy',
                        y: eventValues,
                        x: eventDates,
                        name: 'Sentiment'
                    }];

                    layout = {
                        yaxis: {
                            range: [-1, 1]
                        },
                        xaxis: {
                            autorange: true,
                            type: 'date'
                        }
                    };

                    jQuery('#sentiment-results-success').show();

                    contentTable = '<table id="sentiment_results_id" class="widefat"><thead><th><strong>Date</strong></th><th><strong>Average</strong></th></thead><tbody>';
                    jsonResponse.forEach(function (element) {
                        contentTable += '<tr><td>' + element.event_date + '</td>';
                        contentTable += '<td>' + Math.round(element.event_value * 100) / 100 + '</td></tr>';

                    });
                    contentTable += '</tbody></table>';

                    break;

                case 'intents':

                    let groupByIntent = groupBy(jsonResponse, 'event_value_name' );

                    for( let elem in groupByIntent ) {

                        let eventValueDate = groupByIntent[elem].map( function(e) {
                            return  Date.parse(e.event_date);
                        });

                        let eventValueCount = groupByIntent[elem].map( function(e) {
                            return e.event_value_count;
                        });

                        data.push( {
                            x: eventValueDate,
                            y: eventValueCount,
                            name: elem,
                            stackgroup: 'one'
                        } );
                    }

                    layout = {
                        xaxis: {
                            title: 'Date',
                            type: 'date'
                        },
                        yaxis: {
                            title: 'Frequency of top intent'
                        }
                    };

                    jQuery('#intents-results-success').show();

                    contentTable = '<table id="intents_results_id" class="widefat"><thead><th><strong>Date</strong></th><th>Name</th><th><strong>Count</strong></th></thead><tbody>';
                    jsonResponse.forEach(function (element) {
                        contentTable += '<tr><td>' + element.event_date + '</td>';
                        contentTable += '<td>' + element.event_value_name + '</td>';
                        contentTable += '<td>' + element.event_value_count + '</td></tr>';
                    });
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

                        let emoJson = JSON.parse( element.event_value_name );

                        joy.push( Math.round(emoJson.joy * 100 ) );
                        fear.push( Math.round(emoJson.fear * 100 ) );
                        anger.push( Math.round(emoJson.anger * 100 ) );
                        disgust.push( Math.round(emoJson.disgust * 100 ) );
                        sadness.push( Math.round(emoJson.sadness * 100 ) );

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
                            title: 'Weighted emotions',
                            tickformat: ',.0'
                        }
                    };

                    jQuery('#emotion-results-success').show();

                    contentTable = '<table id="emotion_results_id" class="widefat">' +
                        '               <thead>' +
                        '                   <th><strong>Date</strong></th>' +
                        '                   <th><strong>Joy</strong></th>' +
                        '                   <th><strong>Fear</strong></th>' +
                        '                   <th><strong>Anger</strong></th>' +
                        '                   <th><strong>Disgust</strong></th>' +
                        '                   <th><strong>Sadness</strong></th>' +
                        '               </thead><tbody>';
                    for( let elem in jsonResponse ) {
                        let emotion = JSON.parse(jsonResponse[elem].event_value_name);

                        contentTable  += '<tr>' +
                            '               <td>' + jsonResponse[elem].event_date  + '</td>' +
                            '               <td>' + Math.round( emotion.joy * 100 ) + '%</td>' +
                            '               <td>' + Math.round( emotion.fear * 100 ) + '%</td>' +
                            '               <td>' + Math.round( emotion.anger * 100 ) + '%</td>' +
                            '               <td>' + Math.round( emotion.disgust * 100 ) + '%</td>' +
                            '               <td>' + Math.round( emotion.sadness * 100 ) + '%</td>' +
                            '             </tr>';

                    }
                    contentTable += '</tbody></table>';

                    break;

                case 'keywords':

                    jQuery('#keywords-results-success').show();

                    let keywords = [];
                    let count = [];

                    contentTable = '<table id="keywords_results_id" class="widefat">' +
                        '               <thead>' +
                        '                   <th><strong>Extracted keyword</strong></th>' +
                        '                   <th><strong>Count</strong></th>' +
                        '                   <th><strong>Relevance</strong></th>' +
                        '                   <th><strong>Sentiment</strong></th>' +
                        '                   <th><strong>Joy</strong></th>' +
                        '                   <th><strong>Fear</strong></th>' +
                        '                   <th><strong>Anger</strong></th>' +
                        '                   <th><strong>Disgust</strong></th>' +
                        '                   <th><strong>Sadness</strong></th>' +
                        '               </thead>' +
                        '               <tbody>';
                    jsonResponse.forEach(function (element) {

                        let keyword = JSON.parse(element.keywords);
                        let sentimentLabel = '';
                        let sentimentScore = '';
                        let joyLabel = '';
                        let fearLabel = '';
                        let angerLabel = '';
                        let disgustLabel = '';
                        let sadnessLabel = '';

                        if (typeof keyword.sentiment !== "undefined") {

                            sentimentLabel = keyword.sentiment.label;
                            sentimentScore = ' (' + Math.round(keyword.sentiment.score * 100) + '%)';

                        }

                        if (typeof keyword.emotion !== "undefined") {

                            joyLabel = Math.round(keyword.emotion.joy * 100) + '%';
                            fearLabel = Math.round(keyword.emotion.fear * 100) + '%';
                            angerLabel = Math.round(keyword.emotion.anger * 100) + '%';
                            disgustLabel = Math.round(keyword.emotion.disgust * 100) + '%';
                            sadnessLabel = Math.round(keyword.emotion.sadness * 100) + '%';

                        }

                        contentTable += '<tr>  <td>' + keyword.text + '</td>';
                        contentTable += '      <td>' + element.keywords_count + '</td>';
                        contentTable += '      <td>' + (Math.round(keyword.relevance * 100)) + '%</td>';
                        contentTable += '      <td>' + sentimentLabel + sentimentScore + '</td>';
                        contentTable += '      <td>' + joyLabel + '</td>';
                        contentTable += '      <td>' + fearLabel + '</td>';
                        contentTable += '      <td>' + angerLabel + '</td>';
                        contentTable += '      <td>' + disgustLabel + '</td>';
                        contentTable += '      <td>' + sadnessLabel + '</td></tr>';

                    });
                    contentTable += '</tbody></table>';

                    break;

                case 'latestActivity':

                    jQuery('#activity-results-success').show();

                    contentTable = '<table id="activity_results_id" class="widefat"><thead><th><strong>Event type</strong></th><th><strong>Event key</strong></th><th><strong>Event value</strong></th><th><strong>Timestamp</strong></th></thead><tbody>';
                    jsonResponse.forEach(function (element) {
                        contentTable  += '<tr><td>' + element.event_type  + '</td>';
                        contentTable  += '<td>' + element.event_key  + '</td>';
                        contentTable  += '<td>' + element.event_value  + '</td>';
                        contentTable  += '<td>' + element.event_date + '</td></tr>';

                    });
                    contentTable += '</tbody></table>';

                    console.log(contentTable);

                    break;

                case 'dashboard':

                    let currentScore = JSON.parse(jsonResponse[0].event_value).kontxt_score * 100;
                    let prevScore = 0;

                    try {
                        prevScore = JSON.parse(jsonResponse[1].event_value).kontxt_score * 100;
                    } catch (e) {
                        prevScore = JSON.parse(jsonResponse[0].event_value).kontxt_score * 100;
                    }

                    data = [
                        {
                            domain: { x: [0, 1], y: [0, 1] },
                            margin: 1,
                            title: 'KONTXTscore',
                            value: currentScore,
                            type: "indicator",
                            mode: "gauge+number+delta",
                            delta: { reference: prevScore },
                            gauge: {
                                bar: { color: "Black" },
                                borderwidth: 2,
                                axis: { range: [-100, 100] },
                                steps: [
                                    { range: [-100, -10], color: "#ff533d" },
                                    { range: [-10, 10], color: "LightGrey" },
                                    { range: [10, 100], color: "#66AB8C" }
                                ]
                            }
                        }
                    ];

                    layout = { width: 600, height: 350, margin: { t: 0, b: 0 } };

                    break;
            }

            if( data.length > 0 ) {
                Plotly.react(dimension + '_results_chart', data, layout);
            }

            jQuery('#' + dimension + '_results_table').html( contentTable ).show();

            jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');
        },
        error: function(response) {
            jQuery('#kontxt-results-status').html(response.message);
            return false;
        }

    });

    return false;
}


function kontxtExperimentFormPost(return_text) {

    "use strict";

    jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

    if ( !return_text || return_text.length === 0 ) {

        jQuery('#kontxt-results-status').html('<p>You haven\'t entered any content yet. Please enter some content before trying to analyze.</p>');
        jQuery('#spinner').removeClass('is-active').addClass('is-inactive');

        return false;
    }

    jQuery('#kontxt-results-success').show();

    // prepare data for posting

    let data = jQuery.param({
        'kontxt_text_to_analyze': return_text,
        'action': 'kontxt_analyze',
        'apikey': kontxtAjaxObject.apikey,
        'request_id': makeid(20)
    });

    const security = kontxtAjaxObject.security;

    jQuery.ajax({
        type: 'post',
        url: kontxtAjaxObject.ajaxurl,
        security: security,
        data: data + '&service=intents',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response) {

            if( response.status === 'error' ) {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            let jsonResponse = JSON.parse(response);

            let contentTable = '<table id="kontxt_intents" class="widefat"><thead><th>Intent</th><th>Score</th></th></thead><tbody>';
            jsonResponse.forEach(function (element) {
                contentTable += '<tr><td>' + element.class_name + '</td>';
                contentTable += '<td>' + (Math.round(element.confidence * 100)) + '</td></tr>';

            });
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
        url: kontxtAjaxObject.ajaxurl,
        security: security,
        data: data + '&service=keywords',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response){

            if( response.status === 'error' ) {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            let jsonResponse = JSON.parse(response);

            let contentTable = '<table id="kontxt_keywords" class="widefat"><thead><th>Keyword</th><th>Relevance</th></thead><tbody>';
            jsonResponse.forEach(function (element) {
                contentTable += '<tr><td>' + element.text + '</td>';
                contentTable += '<td>' + (Math.round(element.relevance * 100)) + '%</td></tr>';
            });
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
        url: kontxtAjaxObject.ajaxurl,
        security: security,
        data: data + '&service=sentiment',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response){

            if( response.status === 'error' ) {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            let jsonResponse = JSON.parse(response);

            let arraySize = jQuery(jsonResponse).length;
            let sentimentText;
            let sentimentScore;

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

            let toneAnalysis = 'We detected a <strong>' + sentimentText + '</strong> sentiment with an offset of ' + Math.round(sentimentScore * 100) / 100 + ' from neutral using a range of -1 to 1.';

            jQuery('#overall_tone').html( toneAnalysis ).show();

            let barColor = 'rgba(55,128,191,0.6)';

            if( sentimentScore < 0 ) {
                barColor = 'rgba(255,0,50,0.6)';
            }

            let data = [{
                type: 'bar',
                y: [sentimentScore],
                x: ['Sentiment'],
                orientation: 'v',
                marker: {
                    color: barColor
                }
            }];

            let layout = {
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
            };

            Plotly.newPlot('sentiment_chart', data, layout, {displayModeBar: false}).then();

            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        },
        error: function(response) {
            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        }

    });

    jQuery.ajax({
        type: 'post',
        url: kontxtAjaxObject.ajaxurl,
        security: security,
        data: data + '&service=emotion',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response) {

            if( response.status === 'error' ) {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            let jsonResponse = JSON.parse(response);

            let emotionLabels = [];
            let emotionValues = [];

            let counter = 0;

            for( let elem in jsonResponse ) {
                emotionLabels[counter] = elem;
                emotionValues[counter] = Math.round(jsonResponse[elem]*100);
                counter++;
            }

            let data = [{
                type: 'scatterpolar',
                r: emotionValues,
                theta: emotionLabels,
                fill: 'toself',
                name: 'Emotions detected'
            }];

            let layout = {
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

            Plotly.newPlot('emotion_chart', data, layout, {displayModeBar: false}).then();

        },
        error: function(response) {
            jQuery('#spinner').removeClass('is-active').addClass('is-inactive');
        }
    });

    // Experiments UI
    document.addEventListener('visibilitychange', () => {
        console.log(document.visibilityState);
        window.dispatchEvent(new Event('resize'));
    });

    return false;
}


function kontxtJourney( dimension, date_from, date_to, filter ) {

    "use strict";

    jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

    jQuery('#kontxt-analyze-results-status').hide();

    // prepare data for posting

    let data = jQuery.param({
        'action':       'kontxt_analyze_results',
        'apikey':       kontxtAjaxObject.apikey,
        'service':      'events',
        'filter':       filter,
        'from_date':    date_from,
        'to_date':      date_to
    });

    const security = kontxtAjaxObject.security;

    let layout;
    let contentTable;

    jQuery.ajax({
        type: 'post',
        url: kontxtAjaxObject.ajaxurl,
        security: security,
        data: data + '&dimension=journeyLabels',
        cache: false,
        success: function (response) {

            if (response.status === 'error') {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                return false;
            }

            let jsonResponseLabels = JSON.parse(response);

            jQuery.ajax({
                type: 'post',
                url: kontxtAjaxObject.ajaxurl,
                security: security,
                data: data + '&dimension=' + dimension,
                cache: false,
                success: function (response) {
                    if (response.status === 'error') {
                        jQuery('#kontxt-analyze-results-status').html(response.message).show();
                        return false;
                    }

                    let jsonResponseEvents = JSON.parse(response);

                    let eventLabels     = Object.keys(jsonResponseLabels).map((key) => jsonResponseLabels[key].event_key_label);
                    let eventSource     = Object.keys(jsonResponseEvents).map((key) => jsonResponseEvents[key].event_source);
                    let eventTarget     = Object.keys(jsonResponseEvents).map((key) => jsonResponseEvents[key].event_target);
                    let eventFlowValue  = Object.keys(jsonResponseEvents).map((key) => jsonResponseEvents[key].flow_value);

                    let data = [{
                        type: "sankey",
                        arrangement: "freeform",
                        valueformat: ".0f",
                        valuesuffix: " sessions",
                        orientation: "h",
                        node: {
                            pad: 15,
                            thickness: 30,
                            line: {
                                color: "black",
                                width: 1
                            },
                            label: eventLabels
                        },

                        link: {
                            source: eventSource,
                            target: eventTarget,
                            value: eventFlowValue
                        }
                    }];

                    const layout = {
                        font: {
                            size: 10
                        },
                        hovermode: true
                    };

                    Plotly.react('journey_results_chart', data, layout).then();

                    jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');

                    document.getElementById('journey_results_chart').on('plotly_hover', function(data) {

                        jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

                        let nodeLabel = data.points[0].label;
                        let dateFrom    =  jQuery( '#date_from' ).val();
                        let dateTo      =  jQuery( '#date_to' ).val();

                        if( nodeLabel ) {

                            setTimeout(
                                function()
                                {

                                jQuery('#journey_node_details_box').show();
                                jQuery('#journey_node_details_header').html('Node details for ' + nodeLabel );

                                let data = jQuery.param({
                                    'action': 'kontxt_analyze_results',
                                    'apikey': kontxtAjaxObject.apikey,
                                    'dimension': 'journeyNode',
                                    'filter': nodeLabel,
                                    'from_date': dateFrom,
                                    'to_date': dateTo
                                });

                                jQuery.ajax({
                                    type: 'post',
                                    url: kontxtAjaxObject.ajaxurl,
                                    security: security,
                                    data: data,
                                    cache: false,
                                    success: function (response) {

                                        if (response.status === 'error') {
                                            jQuery('#kontxt-analyze-results-status').html(response.message).show();
                                            return false;
                                        }

                                        let jsonResponse = JSON.parse( response );

                                        contentTable = '<table id="journey_node_results" class="widefat"><thead><th><strong>Event Value</strong></th><th><strong>Count</strong></th></thead><tbody>';

                                        jsonResponse.forEach(function (element) {

                                            contentTable += '<tr><td>' + element.event_value + '</td>';
                                            contentTable += '<td>' + element.event_count + '</td></tr>';

                                        });

                                        contentTable += '</tbody></table>';

                                        jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');

                                        jQuery( '#journey_node_details_table' ).html( contentTable );

                                    }

                                });

                            }, 1000 );

                        }

                    });
                }
            });
        }
    });
 }


function makeid(length) {

    "use strict";

    // from SO: https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript

    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    for ( let i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}