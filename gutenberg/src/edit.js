/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __, sprintf } from '@wordpress/i18n';

import metadata from './block.json';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';
import { cog as IconAppearance } from '@wordpress/icons';
import { getBlockType } from '@wordpress/blocks';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style-editor.css';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {

	const blockData = getBlockType( metadata.name );

	const validateSettings = () => {
		// TODO: implement proper validation
		if( blockData.attributes.instance.default !== attributes.instance
			&& blockData.attributes.account.default !== attributes.account  
		) {
				return true;
		}
		return false;
	}
		
	const onChangeInstance = ( instance ) => {
		// TODO: validate if correct instance
		setAttributes( { 'instance': instance });
	}

	const onChangeAccount = ( account ) => {
		// TODO: validate if is int
		setAttributes( { 'account': account } );
	}

	const loadFeed = (apiUrl, event) => {
		event.preventDefault();
		if(attributes.instance && attributes.account) {
			console.log('loading feed', apiUrl);
			const options = {
				linkTarget: "",
				showPreviewCards: true,
				excludeConversationStarters: false,
				text: {
					boosted: "boosted",
					noStatuses: "no statuses",
					viewOnInstance: "view on instance",
					showContent: "show content",
					permalinkPre: "",
					permalinkPost: "",
					edited: "edited",
				},
				localization: {
					date: {
						locale: "en-US",
						options: {},
					}
				}
			};
			window.mastodonFeedLoad(apiUrl, event.currentTarget, options);
		}
	}

	const apiUrl = 'https://' + (attributes.instance) + '/api/v1/accounts/' + attributes.account + '/statuses';
	attributes.darkmode = true;

	return (
		<div { ...useBlockProps() }>
			{
				validateSettings() ||
					<div className="editor-block">
						<p className="text-center">
							<strong>{ __("Mastodon Feed") }</strong><br />
							<small>{ __("Please set instance and account id") }</small>
						</p>
					</div>
			}
			{
				// TODO: rebuild render function
				validateSettings() &&
					<div onClick={ (e) => loadFeed(apiUrl, e) } className={ 'include-mastodon-feed' + (attributes.darkmode ? ' dark' : '') }>
						<div className="editor-block">
							<p className="text-center">
								<strong>{ __("Mastodon Feed") }</strong><br />
								<small>{ sprintf("Account ID %1$s on instance %2$s", attributes.account, attributes.instance) }</small>
							</p>
							<div className="text-center">
								Click to preview
							</div>
						</div>
					</div>
			}
			<InspectorControls key="setting">
				<PanelBody title={ __("Source") }>
					<TextControl 
						label={ __("Instance") }
						key="instance" 
						onChange={ onChangeInstance }
						value={ attributes.instance }
						placeholder="e.g. mastodon.social"
					/>
					<TextControl 
						label={ __("Account ID") }
						key="account" 
						onChange={ onChangeAccount }
						value={ attributes.account }
					/>
					<div>
						<a href="https://wordpress.org/plugins/include-mastodon-feed/#how%20do%20i%20find%20my%20account%20id%3F" target="_blank" rel="noreferrer"><small>{ __("How to find your account ID?") }</small></a>
					</div><br />
				</PanelBody>
				<PanelBody title={ __("Appearance") } icon={ IconAppearance } initialOpen={ false }>
					<SelectControl
						label={ __("Limit") }
						key="limit" 
						options={ [
							{ label: '20', value: 20 },
							{ label: '19', value: 19 },
							{ label: '18', value: 18 },
							{ label: '17', value: 17 },
							{ label: '16', value: 16 },
							{ label: '15', value: 15 },
							{ label: '14', value: 14 },
							{ label: '13', value: 13 },
							{ label: '12', value: 12 },
							{ label: '11', value: 11 },
							{ label: '10', value: 10 },
							{ label: '9', value: 9 },
							{ label: '8', value: 8 },
							{ label: '7', value: 7 },
							{ label: '6', value: 6 },
							{ label: '5', value: 5 },
							{ label: '4', value: 4 },
							{ label: '3', value: 3 },
							{ label: '2', value: 2 },
							{ label: '1', value: 1 },
						] }
						value={ attributes.limit }
						onChange={ (value) => setAttributes( { 'limit': value } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
		</div>
	);
}
