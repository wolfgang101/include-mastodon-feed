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
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	PanelRow,
	TextControl,
	SelectControl,
	CheckboxControl,
} from '@wordpress/components';
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
		if (
			blockData.attributes.instance.default !== attributes.instance &&
			blockData.attributes.account.default !== attributes.account
		) {
			return true;
		}
		return false;
	};

	const onChangeInstance = ( instance ) => {
		setAttributes( { instance: instance } );
	};

	const onChangeAccount = ( account ) => {
		setAttributes( { account: account } );
	};

	return (
		<div { ...useBlockProps() }>
			{ validateSettings() || (
				<div className="editor-block">
					<p className="text-center">
						<strong>
							{ __( 'Mastodon Feed', 'include-mastodon-feed' ) }
						</strong>
						<br />
						<small>
							{ __(
								'Please set instance and account id',
								'include-mastodon-feed'
							) }
						</small>
					</p>
				</div>
			) }
			{ validateSettings() && (
				<div
					className={
						'include-mastodon-feed' +
						( attributes.darkmode && true === attributes.darkmode
							? ' dark'
							: '' )
					}
				>
					<div className="editor-block">
						<p className="text-center">
							<strong>
								{ __(
									'Mastodon Feed',
									'include-mastodon-feed'
								) }
							</strong>
							<br />
							<small>
								{ sprintf(
									'Account ID %1$s on instance %2$s',
									attributes.account,
									attributes.instance
								) }
							</small>
						</p>
					</div>
				</div>
			) }
			<InspectorControls key="setting">
				<PanelBody title={ __( 'Source', 'include-mastodon-feed' ) }>
					<TextControl
						label={ __( 'Instance', 'include-mastodon-feed' ) }
						key="instance"
						onChange={ onChangeInstance }
						value={ attributes.instance }
						placeholder="e.g. mastodon.social"
					/>
					<TextControl
						label={ __( 'Account ID', 'include-mastodon-feed' ) }
						key="account"
						onChange={ onChangeAccount }
						value={ attributes.account }
					/>
					<PanelRow>
						<div>
							<a
								href="https://wordpress.org/plugins/include-mastodon-feed/#how%20do%20i%20find%20my%20account%20id%3F"
								target="_blank"
								rel="noreferrer"
							>
								{ __(
									'How to find your account ID?',
									'include-mastodon-feed'
								) }
							</a>
						</div>
					</PanelRow>
					<PanelRow>
						<div>
							{ __(
								'Use the following sections for additional settings.',
								'include-mastodon-feed'
							) }
						</div>
					</PanelRow>
				</PanelBody>
				<PanelBody
					title={ __( 'Post filters', 'include-mastodon-feed' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __(
							'Number of posts',
							'include-mastodon-feed'
						) }
						key="limit"
						options={ [
							{ label: '40', value: 40 },
							{ label: '39', value: 39 },
							{ label: '38', value: 38 },
							{ label: '37', value: 37 },
							{ label: '36', value: 36 },
							{ label: '35', value: 35 },
							{ label: '34', value: 34 },
							{ label: '33', value: 33 },
							{ label: '32', value: 32 },
							{ label: '31', value: 31 },
							{ label: '30', value: 30 },
							{ label: '29', value: 29 },
							{ label: '28', value: 28 },
							{ label: '27', value: 27 },
							{ label: '26', value: 26 },
							{ label: '25', value: 25 },
							{ label: '24', value: 24 },
							{ label: '23', value: 23 },
							{ label: '22', value: 22 },
							{ label: '21', value: 21 },
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
						onChange={ ( value ) =>
							setAttributes( { limit: value } )
						}
					/>

					<CheckboxControl
						label={ __(
							'Exclude boosts',
							'include-mastodon-feed'
						) }
						checked={ attributes.excludeBoosts }
						onChange={ ( value ) =>
							setAttributes( { excludeBoosts: value } )
						}
					/>

					<CheckboxControl
						label={ __(
							'Exclude replies',
							'include-mastodon-feed'
						) }
						checked={ attributes.excludeReplies }
						onChange={ ( value ) =>
							setAttributes( { excludeReplies: value } )
						}
					/>

					<CheckboxControl
						label={ __(
							'Exclude conversation starters',
							'include-mastodon-feed'
						) }
						checked={ attributes.excludeConversationStarters }
						onChange={ ( value ) =>
							setAttributes( {
								excludeConversationStarters: value,
							} )
						}
					/>

					<CheckboxControl
						label={ __( 'Only pinned', 'include-mastodon-feed' ) }
						checked={ attributes.onlyPinned }
						onChange={ ( value ) =>
							setAttributes( { onlyPinned: value } )
						}
					/>

					<CheckboxControl
						label={ __( 'Only media', 'include-mastodon-feed' ) }
						checked={ attributes.onlyMedia }
						onChange={ ( value ) =>
							setAttributes( { onlyMedia: value } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Appearance', 'include-mastodon-feed' ) }
					initialOpen={ false }
				>
					<CheckboxControl
						label={ __(
							'Show preview cards',
							'include-mastodon-feed'
						) }
						checked={ attributes.showPreviewCards }
						onChange={ ( value ) =>
							setAttributes( { showPreviewCards: value } )
						}
					/>

					<CheckboxControl
						label={ __(
							'Hide status meta',
							'include-mastodon-feed'
						) }
						checked={ attributes.hideStatusMeta }
						onChange={ ( value ) =>
							setAttributes( { hideStatusMeta: value } )
						}
					/>

					<CheckboxControl
						label={ __(
							'Hide date and time',
							'include-mastodon-feed'
						) }
						checked={ attributes.hideDateTime }
						onChange={ ( value ) =>
							setAttributes( { hideDateTime: value } )
						}
					/>

					<CheckboxControl
						label={ __( 'Dark mode', 'include-mastodon-feed' ) }
						checked={ attributes.darkmode }
						onChange={ ( value ) =>
							setAttributes( { darkmode: value } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Media', 'include-mastodon-feed' ) }
					initialOpen={ false }
				>
					<CheckboxControl
						label={ __(
							'Preserve image aspect ratio',
							'include-mastodon-feed'
						) }
						checked={ attributes.preserveImageAspectRatio }
						onChange={ ( value ) =>
							setAttributes( { preserveImageAspectRatio: value } )
						}
					/>

					<SelectControl
						label={ __( 'Image size', 'include-mastodon-feed' ) }
						key="imageSize"
						options={ [
							{ label: 'preview', value: 'preview' },
							{ label: 'full', value: 'full' },
						] }
						value={ attributes.imageSize }
						onChange={ ( value ) =>
							setAttributes( { imageSize: value } )
						}
					/>

					<SelectControl
						label={ __( 'Image link', 'include-mastodon-feed' ) }
						key="imageLink"
						options={ [
							{ label: 'status', value: 'status' },
							{ label: 'image', value: 'image' },
						] }
						value={ attributes.imageLink }
						onChange={ ( value ) =>
							setAttributes( { imageLink: value } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'More', 'include-mastodon-feed' ) }
					initialOpen={ false }
				>
					<PanelRow>
						<div>
							{ __(
								'More settings available with the ',
								'include-mastodon-feed'
							) }
							<code>[include-mastodon-feed]</code>
							{ __( ' shortcode and PHP constants.' ) }
						</div>
					</PanelRow>
					<PanelRow>
						<div>
							Additional settings and customization:
							<br />
							- Tag feeds
							<br />
							- Tag filtering
							<br />
							- Labels
							<br />
							- Date / time locales
							<br />
							- Link target
							<br />
							- Caching
							<br />- API authentication
						</div>
					</PanelRow>
					<PanelRow>
						<div>
							{ __( 'See the ', 'include-mastodon-feed' ) }
							<a
								href="https://wordpress.org/plugins/include-mastodon-feed/#installation"
								target="_blank"
								rel="noreferrer"
							>
								{ __( 'plugin page', 'include-mastodon-feed' ) }
							</a>
							{ __( ' for details.', 'include-mastodon-feed' ) }
						</div>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
		</div>
	);
}
