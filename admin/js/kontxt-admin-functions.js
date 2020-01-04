jQuery(function($) {

    $( "#date_from" ).datepicker({ dateFormat: "yy-mm-dd" }).datepicker("setDate", "-7d");

    $( "#date_to" ).datepicker({dateFormat: "yy-mm-dd"}).datepicker('setDate', new Date());

    // Experiments UI
    document.addEventListener('visibilitychange', () => {
        console.log(document.visibilityState);
        window.dispatchEvent(new Event('resize'));
    });

    // capture KONTXT form post and pass to handler
    jQuery( '#kontxt-experiment-input-button' ).click( function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        var textToAnalyze =  jQuery( '#kontxt-input-text-field' ).val();

        kontxtExperimentFormPost( textToAnalyze )
    });

    // capture date range redraw
    jQuery( '#kontxt-events-date' ).click( function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        var dimension =  jQuery( '#dimension' ).val();
        var date_from =  jQuery( '#date_from' ).val();
        var date_to =  jQuery( '#date_to' ).val();

        kontxtAnalyze( dimension, date_from, date_to );
    });

    // capture intent redraw
    jQuery( '#kontxt-intent-overlay' ).click( function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        var overlay =  jQuery( '#overlay' ).val();
        var dimension =  jQuery( '#dimension' ).val();

        if ( Date.parse( jQuery( '#date_from' ).val() ) ) {
            var date_from =  jQuery( '#date_from' ).val();
        }
        if ( Date.parse( jQuery( '#date_to' ).val() ) ) {
            var date_to =  jQuery( '#date_to' ).val();
        }

        if( overlay ) {
            kontxtOverlay( overlay, date_from, date_to );
        } else {
            kontxtAnalyze( dimension, date_from, date_to);
        }
    });

    // capture intent filter
    jQuery( '#kontxt-intent-filter' ).click( function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        var filter =  jQuery( '#filter' ).val();
        var dimension =  jQuery( '#dimension' ).val();

        if ( Date.parse( jQuery( '#date_from' ).val() ) ) {
            var date_from =  jQuery( '#date_from' ).val();
        }
        if ( Date.parse( jQuery( '#date_to' ).val() ) ) {
            var date_to =  jQuery( '#date_to' ).val();
        }

        if( filter ) {
            kontxtFilter(filter, date_from, date_to);
        } else {
            kontxtAnalyze( dimension, date_from, date_to);
        }
    });

});

function kontxtFilter( filter, date_from, date_to ) {

    var data = jQuery.param({
        'action':       'kontxt_analyze_results',
        'apikey':       kontxtAjaxObject.apikey,
        'dimension':    'sentimentByIntent',
        'filter':       filter,
        'from_date':    date_from,
        'to_date':      date_to
    });

    var security = kontxtAjaxObject.security;

    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        security: security,
        data: data,
        cache: false,
        success: function (response) {

            if (response.status == 'error') {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                return false;
            }

            var jsonResponse = jQuery.parseJSON(response);
            // var jsonResponse = response;

            var eventDates = jsonResponse.map(function (e) {
                return Date.parse(e.event_date);
            });

            var eventValues = jsonResponse.map(function (e) {
                return e.event_value;
            });

            let data2 = [];
            let layout;
            let contentTable;

            data2 = [{
                type: 'scatter',
                fill: 'tozeroy',
                y: eventValues,
                x: eventDates,
                name: 'Sentiment'
            }];

            layout2 = {
                yaxis: {
                    range: [-1, 1]
                },
                xaxis: {
                    autorange: true,
                    type: 'date'
                }
            }

            jQuery('#sentiment-results-success').show();

            contentTable = '<table id="sentiment_results_id" class="widefat"><thead><th><strong>Date</strong></th><th><strong>Average</strong></th></thead><tbody>';
            for (var elem in jsonResponse) {
                contentTable += '<tr><td>' + jsonResponse[elem]['event_date'] + '</td>';
                contentTable += '<td>' + jsonResponse[elem]['event_value'] + '</td></tr>';

            }
            contentTable += '</tbody></table>';

            if (data2.length > 0) {
                Plotly.newPlot('sentiment_results_chart', data2, layout2);
            }

            jQuery('#sentiment_results_table').html(contentTable).show();

            jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');
        },
        error: function (response) {
            jQuery('#kontxt-results-status').html(response.message);
            return false;
        }
    });
}

function kontxtOverlay( overlay, date_from, date_to ) {

    var data = jQuery.param({
        'action':       'kontxt_analyze_results',
        'apikey':       kontxtAjaxObject.apikey,
        'dimension':    overlay,
        'from_date':    date_from,
        'to_date':      date_to
    });

    var security = kontxtAjaxObject.security;

    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        security: security,
        data: data,
        cache: false,
        success: function (response) {

            var jsonResponse = jQuery.parseJSON(response);
            // var jsonResponse = response;

            var eventDates = jsonResponse.map(function (e) {
                return Date.parse(e.event_date);
            });

            var eventValues = jsonResponse.map(function (e) {
                return e.event_value;
            });

            var groupBy = function (xs, key) {
                return xs.reduce(function (rv, x) {
                    (rv[x[key]] = rv[x[key]] || []).push(x);
                    return rv;
                }, {});
            };

            let data2 = [];

            var groupByIntent = groupBy(jsonResponse, 'event_value_name');

            for (var elem in groupByIntent) {

                var eventValueDate = groupByIntent[elem].map(function (e) {
                    return Date.parse(e.event_date);
                });

                var eventValueCount = groupByIntent[elem].map(function (e) {
                    return e.event_value_count;
                });

                data2.push({
                    x: eventValueDate,
                    y: eventValueCount,
                    name: elem,
                    stackgroup: 'one',
                    yaxis: 'y2',
                });

            }

            Plotly.addTraces(
                'sentiment_results_chart',
                data2
            )
            Plotly.relayout(
                'sentiment_results_chart',
                {
                    ['yaxis2']: {
                        overlaying: 'y1',
                        side: 'right'
                    }
                }
            );
        }
    });
}

function kontxtAnalyze( dimension, date_from, date_to) {

    jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

    jQuery('#kontxt-analyze-results-status').hide();

    // prepare data for posting

    var data = jQuery.param({
        'action':       'kontxt_analyze_results',
        'apikey':       kontxtAjaxObject.apikey,
        'dimension':    dimension,
        'from_date':    date_from,
        'to_date':      date_to
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
                return false;
            }

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
                    }

                    jQuery('#sentiment-results-success').show();

                    contentTable = '<table id="sentiment_results_id" class="widefat"><thead><th><strong>Date</strong></th><th><strong>Average</strong></th></thead><tbody>';
                    for( var elem in jsonResponse ) {
                        contentTable  += '<tr><td>' + jsonResponse[elem]['event_date']  + '</td>';
                        contentTable  += '<td>' + Math.round(jsonResponse[elem]['event_value'] * 100)/100 + '</td></tr>';

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

                    layout = {
                        xaxis: {
                            title: 'Date',
                            type: 'date'
                        },
                        yaxis: {
                            title: 'Frequency of top intent'
                        }
                    }

                    jQuery('#intents-results-success').show();

                    contentTable = '<table id="intents_results_id" class="widefat"><thead><th><strong>Date</strong></th><th>Name</th><th><strong>Count</strong></th></thead><tbody>';
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
                            title: 'Weighted emotions',
                            tickformat: ',.0'
                        }
                    }

                    jQuery('#emotion-results-success').show();

                    contentTable = '<table id="emotion_results_id" class="widefat"><thead><th><strong>Date</strong></th><th><strong>Name/Average</strong></th></thead><tbody>';
                    for( let elem in jsonResponse ) {
                        contentTable  += '<tr><td>' + jsonResponse[elem]['event_date']  + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['event_value_name'] + '</td></tr>';

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
                        '                   <th><strong>Relevance</strong></th>' +
                        '                   <th><strong>Sentiment</strong></th>' +
                        '                   <th><strong>Joy</strong></th>' +
                        '                   <th><strong>Fear</strong></th>' +
                        '                   <th><strong>Anger</strong></th>' +
                        '                   <th><strong>Disgust</strong></th>' +
                        '                   <th><strong>Sadness</strong></th>' +
                        '                   <th><strong>Count</strong></th>' +
                        '               </thead>' +
                        '               <tbody>';
                    for( let elem in jsonResponse ) {

                        //keywords.push( jsonResponse[elem]['keywords'] );
                        //count.push( jsonResponse[elem]['keywords_count'] );
                        let keyword = JSON.parse(jsonResponse[elem]['keywords']);
                        let sentimentLabel = '';
                        let sentimentScore = '';
                        let emotionLabel = '';
                        let joyLabel = '';
                        let fearLabel = '';
                        let angerLabel = '';
                        let disgustLabel = '';
                        let sadnessLabel = '';

                        if( typeof keyword['sentiment'] !== "undefined" ) {
                           // sentiment = JSON.parse(keyword['sentiment']);
                            //sentimentLabel = sentiment['label'];
                            sentimentLabel = keyword['sentiment']['label'];
                            sentimentScore = ' (' + Math.round(keyword['sentiment']['score'] * 100 ) + '%)';
                        }

                        if( typeof keyword['emotion'] !== "undefined" ) {
                            // sentiment = JSON.parse(keyword['sentiment']);
                            //sentimentLabel = sentiment['label'];
                            joyLabel = Math.round(keyword['emotion']['joy']* 100 ) + '%';
                            fearLabel = Math.round(keyword['emotion']['fear']* 100 )+ '%';
                            angerLabel = Math.round(keyword['emotion']['anger']* 100 )+ '%';
                            disgustLabel = Math.round(keyword['emotion']['disgust']* 100 )+ '%';
                            sadnessLabel = Math.round(keyword['emotion']['sadness']* 100 )+ '%';
                        }

                        //let sentiment = JSON.parse(keyword['sentiment']);
                        // console.log(keyword);
                        //console.log(keyword);

                        contentTable  += '<tr>  <td>' + keyword['text'] + '</td>';
                        contentTable  += '      <td>' + ( Math.round(keyword['relevance'] * 100 )) + '%</td>';
                        contentTable  += '      <td>' + sentimentLabel + sentimentScore + '</td>';
                        contentTable  += '      <td>' + joyLabel + '</td>';
                        contentTable  += '      <td>' + fearLabel + '</td>';
                        contentTable  += '      <td>' + angerLabel + '</td>';
                        contentTable  += '      <td>' + disgustLabel + '</td>';
                        contentTable  += '      <td>' + sadnessLabel + '</td>';
                        contentTable  += '      <td>' + jsonResponse[elem]['keywords_count'] + '</td></tr>';

                    }
                    contentTable += '</tbody></table>';

                    // data = [{
                    //     type: 'bar',
                    //     y: count,
                    //     x: keywords,
                    //     name: 'Keywords',
                    //     text: count.map(String),
                    //     textposition: 'auto',
                    //     hoverinfo: 'none',
                    //     marker: {
                    //         color: 'rgb(158,202,225)',
                    //         opacity: 0.6,
                    //         line: {
                    //             color: 'rgb(8,48,107)',
                    //             width: 1.5
                    //         }
                    //     }
                    // }];
                    //
                    // layout = {
                    //     yaxis: {
                    //     },
                    //     xaxis: {
                    //         autorange: true
                    //     }
                    // }

                    break;

                case 'latestActivity':

                    jQuery('#activity-results-success').show();

                    contentTable = '<table id="activity_results_id" class="widefat"><thead><th><strong>Event type</strong></th><th><strong>Event key</strong></th><th><strong>Event value</strong></th><th><strong>Timestamp</strong></th></thead><tbody>';
                    for( let elem in jsonResponse ) {
                        contentTable  += '<tr><td>' + jsonResponse[elem]['event_type']  + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['event_key']  + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['event_value']  + '</td>';
                        contentTable  += '<td>' + jsonResponse[elem]['created'] + '</td></tr>';

                    }
                    contentTable += '</tbody></table>';

                    break;

                case 'dashboard':

                    var currentScore = JSON.parse(jsonResponse[0]['event_value'])['kontxt_score'] * 100;

                    try {
                        var prevScore = JSON.parse(jsonResponse[1]['event_value'])['kontxt_score'] * 100;
                    } catch (e) {
                        var prevScore = JSON.parse(jsonResponse[0]['event_value'])['kontxt_score'] * 100
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
                Plotly.newPlot( dimension + '_results_chart', data, layout );
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
};


function kontxtExperimentFormPost(return_text) {

    jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

    if ( !return_text || return_text.length === 0 ) {

        jQuery('#kontxt-results-status').html('<p>You haven\'t entered any content yet. Please enter some content before trying to analyze.</p>');
        jQuery('#spinner').removeClass('is-active').addClass('is-inactive');

        return false;
    }

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
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                jQuery('#kontxt-results-success').hide();
                return false;
            }

            var jsonResponse = jQuery.parseJSON(response);

            var contentTable = '<table id="kontxt_intents" class="widefat"><thead><th>Intent</th><th>Relevance</th></th></thead><tbody>';
            for( var elem in jsonResponse ) {
                contentTable  += '<tr><td>' + jsonResponse[elem]['class_name'] + '</td>';
                contentTable  += '<td>' + ( Math.round(jsonResponse[elem]['confidence'] * 100 )) + '%</td></tr>';

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
        data: data + '&service=keywords',
        action: 'kontxt_analyze',
        cache: false,
        success: function(response){

            if( response.status == 'error' ) {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
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
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
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
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
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


function kontxtJourney( ) {

    jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

    jQuery('#kontxt-analyze-results-status').hide();

    // prepare data for posting

    let data = jQuery.param({
        'action': 'kontxt_analyze_results',
        'apikey': kontxtAjaxObject.apikey,
        'service': 'events'
    });

    let security = kontxtAjaxObject.security;

    let layout;
    let contentTable;

    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        security: security,
        data: data + '&dimension=journeyLabels',
        cache: false,
        success: function (response) {

            if (response.status == 'error') {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                return false;
            }

            let jsonResponseLabels = jQuery.parseJSON(response);

            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                security: security,
                data: data + '&dimension=journeyEvents',
                cache: false,
                success: function (response) {

                    if (response.status == 'error') {
                        jQuery('#kontxt-analyze-results-status').html(response.message).show();
                        return false;
                    }

                    let jsonResponseEvents = jQuery.parseJSON(response);

                    let eventLabels     = Object.keys(jsonResponseLabels).map((key) => jsonResponseLabels[key]['event_key_label']) ;
                    let eventSource     = Object.keys(jsonResponseEvents).map((key) => jsonResponseEvents[key]['event_source']);
                    let eventTarget     = Object.keys(jsonResponseEvents).map((key) => jsonResponseEvents[key]['event_target']);
                    let eventFlowValue  = Object.keys(jsonResponseEvents).map((key) => jsonResponseEvents[key]['flow_value']);

                    var data = {
                        type: "sankey",
                        arrangement: "snap",
                        valueformat: ".0f",
                        valuesuffix: " sessions",
                        orientation: "h",
                        node: {
                            pad: 15,
                            thickness: 30,
                            line: {
                                color: "black",
                                width: 0.5
                            },
                            label: eventLabels,
                            pad:10
                        },

                        link: {
                            source: eventSource,
                            target: eventTarget,
                            value:  eventFlowValue
                        }
                    }

                    var data = [data]

                    var layout = {
                        font: {
                            size: 10
                        }
                    }

                    Plotly.newPlot('journey_results_chart', data, layout);

                    jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');

                }
            });


        }
    });


}


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