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
import { useState } from '@wordpress/element';
import {
	PanelBody,
	PanelRow,
	TextControl,
	SelectControl,
	CheckboxControl,
  __experimentalSpacer as Spacer,
  Button,
  Modal
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
      // type is selected
      blockData.attributes.gutenbergType.default !== attributes.gutenbergType &&
      // instance is selected
      blockData.attributes.instance.default !== attributes.instance &&
      (
        // using account
        blockData.attributes.account.default !== attributes.account ||
        // using tag
        blockData.attributes.tag.default !== attributes.tag
      )
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

  const onChangeTag = ( tag ) => {
    setAttributes( { tag: tag } );
  };

  const onChangeTagged = ( tagged ) => {
    setAttributes( { tagged: tagged } );
  };

  const onChangeGutenbergType = (type) => {
    if(null === type) {
      if(attributes.instance && attributes.account && !confirm(__('This will clear instance & account. Continue?', 'include-mastodon-feed'))) {
        return;
      }
      else if(attributes.instance && attributes.tag && !confirm(__('This will clear instance & tag. Continue?', 'include-mastodon-feed'))) {
        return;
      }
      setAttributes( { gutenbergType: '', instance: '', account: '', tag: '' } );
    }
    else if (['account', 'tag'].includes(type)) {
      setAttributes( { gutenbergType: type, instance: '', account: '', tag: '' } );
    }
  }

  // account id lookup
  const [isAccountLookupOpen, setIsAccountLookupOpen] = useState(false);
  const openAccountLookupModal = () => setIsAccountLookupOpen(true);
  const closeAccountLookupModal = () => setIsAccountLookupOpen(false);
  const [lookupAccountHandle, setLookupAccountHandle] = useState('');
  const lookupAccountId = (handle) => {
    if (handle.startsWith('@')) {
        handle = handle.substring(1);
    }
    handle = handle.trim().split('@');
    if(handle.length !== 2) {
      alert('Please enter a correct Mastodon username');
    }
    else {
      const url = 'https://' + handle[1] + '/api/v2/search?q=' + handle[0] + '@' + handle[1] + '&resolve=false&limit=1';
      const xhr = new XMLHttpRequest();
      xhr.open('GET', url);
      xhr.onload = function() {
        if (xhr.status === 200) {
          const data = JSON.parse(xhr.responseText);
          if(data.accounts && data.accounts.length == 1) {
            setAttributes({ instance: handle[1] });
            setAttributes({ account: data.accounts[0].id });
            closeAccountLookupModal();
            setLookupAccountHandle('');
            alert( __( 'Found your account ID. Don\'t forget to save the changes!', 'include-mastodon-feed' ) );
            return;
          }
          alert( __( 'Sorry, something went wrong', 'include-mastodon-feed' ) );
        } else {
          console.error('Request failed. Status:', xhr.status);
          try {
            const data = JSON.parse(xhr.responseText);
            if(data.error) {
              alert(data.error);
              return;
            }
          }
          catch (e) {}
          console.error('Request failed. Status:', xhr.status);
        }
      };
      xhr.send();				
    }
  }

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
								'Please select account or tag',
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
							{blockData.attributes.account.default !== attributes.account && (
                <small>
                  { sprintf(
                    'Account ID %1$s on instance %2$s',
                    attributes.account,
                    attributes.instance
                  ) }
                </small>
              )}
              {blockData.attributes.tag.default !== attributes.tag && (
                <small>
                  { sprintf(
                    'Tag #%1$s on instance %2$s',
                    attributes.tag,
                    attributes.instance
                  ) }
                </small>
              )}
						</p>
					</div>
				</div>
			) }
			<InspectorControls key="setting">
				<PanelBody title={ __( 'Source', 'include-mastodon-feed' ) }>
          {!['account', 'tag'].includes(attributes.gutenbergType) && (
            <>
              <PanelRow>
                <Button variant="primary" onClick={() => onChangeGutenbergType('account')}>
                    Account
                </Button>
                <div>or</div>
                <Button variant="primary" onClick={() => onChangeGutenbergType('tag')}>
                    Tag
                </Button>
              </PanelRow>
              <PanelRow>
                <Spacer />
              </PanelRow>
            </>
          )}
          {blockData.attributes.gutenbergType.default !== attributes.gutenbergType && (
            <TextControl
              label={ __( 'Instance', 'include-mastodon-feed' ) }
              key="instance"
              onChange={ onChangeInstance }
              value={ attributes.instance }
              placeholder="e.g. mastodon.social"
            />
          )}
          {'account' === attributes.gutenbergType && (
            <>
              <TextControl
                label={ __( 'Account ID', 'include-mastodon-feed' ) }
                key="account"
                onChange={ onChangeAccount }
                value={ attributes.account }
              />
              <PanelRow>
                <Button variant="primary" onClick={openAccountLookupModal}>
                    Find my account ID
                </Button>
                <Button onClick={() => onChangeGutenbergType(null)}>
                  Change source
                </Button>
                {isAccountLookupOpen && (
                    <Modal
                        title="Account ID lookup tool"
                        onRequestClose={closeAccountLookupModal}
                    >
                        <TextControl
                          label={ __( 'Your Mastodon account', 'include-mastodon-feed' ) }
                          value={lookupAccountHandle}
                          onChange={(value) => setLookupAccountHandle(value)}
                          key="gutenbergLookupValue"
                          help={__('e.g. @w101@mastodon.social', 'include-mastodon-feed')}
                          autoFocus
                        />
                        <Button variant="primary" onClick={() => lookupAccountId(lookupAccountHandle)}>
                            Find
                        </Button>
                    </Modal>
                )}
              </PanelRow>
              <PanelRow>
                <div>
                  <a
                    href="https://wordpress.org/plugins/include-mastodon-feed/#how%20do%20i%20find%20my%20account%20id%3F"
                    target="_blank"
                    rel="noreferrer"
                  >
                    { __(
                      'More info on how finding your account ID works',
                      'include-mastodon-feed'
                    ) }
                  </a>
                </div>
              </PanelRow>
            </>
          )}
          {'tag' === attributes.gutenbergType && (
            <>
              <PanelRow>
                <TextControl
                  label={ __( 'Tag', 'include-mastodon-feed' ) }
                  key="tag"
                  onChange={ onChangeTag }
                  value={ attributes.tag }
                  placeholder="e.g. bloomscrolling"
                  help={__('Enter tag without leading # symbol.', 'include-mastodon-feed')}
                />
              </PanelRow>
              <PanelRow>
                <div></div>
                <Button onClick={() => onChangeGutenbergType(null)}>
                  Change source
                </Button>
              </PanelRow>
            </>
          )}
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

          {'account' === attributes.gutenbergType && (
            <>
              <TextControl
                label={ __( 'Tagged', 'include-mastodon-feed' ) }
                key="tagged"
                onChange={ onChangeTagged }
                value={ attributes.tagged }
                help={__('Show only statuses that are tagged with given tag name. No leading #, case insensitive', 'include-mastodon-feed')}
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
            </>
          )}

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
								'More advanced settings available with the ',
								'include-mastodon-feed'
							) }
							<code>[include-mastodon-feed]</code>
							{ __( ' shortcode and PHP constants.', 'include-mastodon-feed' ) }
						</div>
					</PanelRow>
					<PanelRow>
						<div>
							{ __('Additional settings and customization:', 'include-mastodon-feed') }
							<br />
							- { __('Labels', 'include-mastodon-feed') }
							<br />
							- { __('Date / time locales', 'include-mastodon-feed') }
							<br />
							- { __('Link target', 'include-mastodon-feed') }
							<br />
							- { __('Caching', 'include-mastodon-feed') }
							<br />- { __('API authentication', 'include-mastodon-feed') }
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
