import { registerPlugin } from "@wordpress/plugins";
import { PluginDocumentSettingPanel } from "@wordpress/edit-post";
import { Button, Disabled, TextareaControl, Spinner } from '@wordpress/components';
import { withState } from '@wordpress/compose';


const MyDisabled = withState( {
    isDisabled: false,
} )( ( { isDisabled, setState } ) => {

    const runKontxtAnalyze = () => {
        setState( ( state ) => ( { isDisabled: ! state.isDisabled } ) );
        const { select } = wp.data;
        const title = select( 'core/editor' ).getEditedPostAttribute( 'title' ).trim();
        const content = select( 'core/editor' ).getEditedPostAttribute( 'content' ).trim();
        const security = kontxtAjaxObject.security;

        let txtToAnalyze = jQuery( '<div>' + title + ' ' + content + '</div>' ).text();

        txtToAnalyze = txtToAnalyze.replace(/[^0-9a-z\s]/gi, '');
        txtToAnalyze = txtToAnalyze.replace(/\u00A0/g, ' ');

        let data = jQuery.param({
            'action' : 'kontxt_analyze',
            'apikey' : kontxtAjaxObject.apikey,
            'kontxt_text_to_analyze' : txtToAnalyze,
            'service' : 'keywords'
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

                let jsonResponse = JSON.parse(response);
                let jsonResponseSize = jQuery(jsonResponse).length;
                let keywordFieldArray = [];
                let keywordFieldText = null;

                if( jsonResponseSize >= 1 ) {

                    jsonResponse.forEach(function (element) {

                        keywordFieldArray.push( element.text.trim() );

                    });

                    keywordFieldText = keywordFieldArray.join( ', ' );

                } else {

                    keywordFieldText = 'No keywords found.';

                }

                jQuery( '#kontxt_keywords' ).val( keywordFieldText );

                setState( ( state ) => ( { isDisabled: ! state.isDisabled } ) );

            }
        });
    };

    let kontxtButton = <Button onChange={ () => {} } isPrimary onClick={runKontxtAnalyze}>Analyze</Button>;

    if ( isDisabled ) {
        kontxtButton = <Disabled>{ kontxtButton } <Spinner/></Disabled>;
    }

    return (
        <div>
            <p>{ kontxtButton }</p>
            <TextareaControl name="kontxt_keywords" type="text" id="kontxt_keywords"
                              placeholder="Keywords" />

        </div>
    );
});



const KontxtSettingPanel = () => (

    <PluginDocumentSettingPanel
        name="kontxt-admin-panel"
        title="KONTXT SEO Adviser"
        className="kontxt-panel"
    >

        <p>KONTXT will analyze your content and provide you with a set of recommended keywords.
            Copy and paste these to your Tags to improve SEO.</p>

        <MyDisabled/>

    </PluginDocumentSettingPanel>

);
registerPlugin( 'plugin-document-setting-panel-kontxt', { render: KontxtSettingPanel, icon: 'performance' } );

