<?php
/*
  Plugin Name: Include Mastodon Feed
	Plugin URI: https://wolfgang.lol/code/include-mastodon-feed-wordpress-plugin
	Description: Plugin providing [include-mastodon-feed] shortcode
	Version: 1.9.11
	Author: wolfgang.lol
	Author URI: https://wolfgang.lol
  License: MIT
  License URI: https://directory.fsf.org/wiki/License:Expat
*/

namespace IncludeMastodonFeedPlugin;

// set defaults
$constants = [
  [
    'key' => 'INCLUDE_MASTODON_FEED_DEBUG',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_DEFAULT_INSTANCE',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_LIMIT',
    'value' => 20,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_EXCLUDE_BOOSTS',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_EXCLUDE_REPLIES',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_EXCLUDE_CONVERSATIONSTARTERS',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_ONLY_PINNED',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_ONLY_MEDIA',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_PRESERVE_IMAGE_ASPECT_RATIO',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_IMAGE_SIZE',
    'value' => 'preview',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_IMAGE_LINK',
    'value' => 'status',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TAGGED',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_LINKTARGET',
    'value' => '_self',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_SHOW_PREVIEWCARDS',
    'value' => true,
  ],

  // set styles
  [
    'key' => 'INCLUDE_MASTODON_FEED_DARKMODE',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_STYLE_BG_LIGHT_COLOR',
    'value' => 'rgba(100, 100, 100, 0.15)',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_STYLE_BG_DARK_COLOR',
    'value' => 'rgba(155, 155, 155, 0.15)',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_STYLE_ACCENT_COLOR',
    'value' => 'rgb(99, 100, 255)',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_STYLE_ACCENT_FONT_COLOR',
    'value' => 'rgb(255, 255, 255)',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_STYLE_BORDER_RADIUS',
    'value' => '0.25rem',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_HIDE_STATUS_META',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_HIDE_DATETIME',
    'value' => false,
  ],
  // set texts and localization
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_LOADING',
    'value' => 'Loading Mastodon feed...',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_NO_STATUSES',
    'value' => 'No statuses availablae',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_BOOSTED',
    'value' => 'boosted 🚀',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_VIEW_ON_INSTANCE',
    'value' => 'view on instance',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_SHOW_CONTENT',
    'value' => 'Show content',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_PERMALINK_PRE',
    'value' => 'on',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_PERMALINK_POST',
    'value' => '',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_EDITED',
    'value' => '(edited)',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_DATE_LOCALE',
    'value' => 'en-US',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_DATE_OPTIONS',
    'value' => "{}",
  ],
];
foreach($constants as $constant) {
  if(!defined($constant['key'])) {
      define($constant['key'], $constant['value']);
  }
}
unset($constants);

function error($msg) {
  return '[include-mastodon-feed] ' . $msg;
}


function init_styles() {
?>
  <style>
    :root {
      --include-mastodon-feed-bg-light: <?php echo filter_var( INCLUDE_MASTODON_FEED_STYLE_BG_LIGHT_COLOR, FILTER_UNSAFE_RAW ); ?>;
      --include-mastodon-feed-bg-dark: <?php echo filter_var( INCLUDE_MASTODON_FEED_STYLE_BG_DARK_COLOR, FILTER_UNSAFE_RAW ); ?>;
      --include-mastodon-feed-accent-color: <?php echo filter_var( INCLUDE_MASTODON_FEED_STYLE_ACCENT_COLOR, FILTER_UNSAFE_RAW ); ?>;
      --include-mastodon-feed-accent-font-color: <?php echo filter_var( INCLUDE_MASTODON_FEED_STYLE_ACCENT_FONT_COLOR, FILTER_UNSAFE_RAW ); ?>;
      --include-mastodon-feed-border-radius: <?php echo filter_var( INCLUDE_MASTODON_FEED_STYLE_BORDER_RADIUS, FILTER_UNSAFE_RAW ); ?>;
    }

    .include-mastodon-feed .include-mastodon-feed-status {
      margin: 0.5rem 0 1.5rem;
      border-radius: var(--include-mastodon-feed-border-radius);
      padding: 0.5rem;
      background: var(--include-mastodon-feed-bg-light);
    }
    .include-mastodon-feed .include-mastodon-feed-status a {
      color: var(--include-mastodon-feed-accent-color);
      text-decoration: none;
      word-wrap: break-word;
    }
    .include-mastodon-feed .include-mastodon-feed-status a:hover {
      text-decoration: underline;
    }
    .include-mastodon-feed .avatar {
      height: 1.25rem;
      border-radius: var(--include-mastodon-feed-border-radius);
      vertical-align: top;
    }
    .include-mastodon-feed .account {
      font-size: 0.8rem;
    }
    .include-mastodon-feed .account a {
      display: inline-block;
    }
    .include-mastodon-feed .account .booster {
      float: right;
      font-style: italic;
    }
    .include-mastodon-feed .boosted .account > a:first-child,
    .include-mastodon-feed .contentWarning a {
      border-radius: var(--include-mastodon-feed-border-radius);
      padding: 0.15rem 0.5rem;
      background: var(--include-mastodon-feed-accent-color);
      color: var(--include-mastodon-feed-accent-font-color);
    }
    .include-mastodon-feed .boosted .account > a:first-child:hover,
    .include-mastodon-feed .contentWarning a:hover {
      border-radius: var(--include-mastodon-feed-border-radius);
      padding: 0.15rem 0.5rem;
      background: var(--include-mastodon-feed-accent-font-color);
      color: var(--include-mastodon-feed-accent-color);
      text-decoration: none;
    }
    .include-mastodon-feed .contentWrapper.boosted {
      margin: 0.5rem 0;
      padding: 0.5rem;
      background: var(--include-mastodon-feed-bg-light);
    }
    .include-mastodon-feed .contentWarning {
      text-align: center;
      margin: 1rem;
      padding: 1rem;
    }
    .include-mastodon-feed .contentWarning .title {
      font-weight: bold;
    }
    .include-mastodon-feed img.emoji {
      height: 1rem;
    }
    .include-mastodon-feed .include-mastodon-feed-content .invisible {
      display: none;
    }
    .include-mastodon-feed .media {
      display: flex;
      justify-content: space-around;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin: 1rem;
    }
    .include-mastodon-feed .media > div {
      flex-basis: calc(50% - 0.5rem);
      flex-grow: 1;
    }
    .include-mastodon-feed .media > .image {
      font-size: 0.8rem;
      font-weight: bold;
      text-align: center;
    }
    .include-mastodon-feed .media > .image a { 
      border-radius: var(--include-mastodon-feed-border-radius);
      display: block;
      aspect-ratio: 1.618;                                                      
      background-size: cover;
      background-position: center;
    }
        .include-mastodon-feed .media > .image a:hover {
      filter: contrast(110%) brightness(130%) saturate(130%);
    }
    .include-mastodon-feed .media > .image a img {
      width: 100%;
    }
    .include-mastodon-feed .media > .gifv video {
      max-width: 100%;
    }

    .include-mastodon-feed .card {
      border-radius: var(--include-mastodon-feed-border-radius);
      margin: 1rem 0.5rem;
    }
    .include-mastodon-feed .card iframe {
      border-radius: var(--include-mastodon-feed-border-radius);
      width: 100%;
      height: 100%;
      aspect-ratio: 2 / 1.25;
    }
    .include-mastodon-feed .card a {
      border-radius: var(--include-mastodon-feed-border-radius);
      display: block;
      text-decoration: none;
      color: #000;
    }
    .include-mastodon-feed.dark .card a {
      color: #fff;
    }
    .include-mastodon-feed .card a:hover {
      text-decoration: none;
      background: var(--include-mastodon-feed-accent-color);
      color: var(--include-mastodon-feed-accent-font-color);
    }
    .include-mastodon-feed .card .meta {
      background: var(--include-mastodon-feed-bg-light);
      font-size: 0.8rem;
      padding: 1rem;
    }
    .include-mastodon-feed .card .image {
      margin-bottom: 0.5rem;
      text-align: center;
    }
    .include-mastodon-feed .card .image img {
      max-width: 75%;
    }
    .include-mastodon-feed .card .title {
      font-weight: bold;
    }
    .include-mastodon-feed.dark .include-mastodon-feed-status,
    .include-mastodon-feed.dark .contentWrapper.boosted,
    .include-mastodon-feed.dark .card {
      background: var(--include-mastodon-feed-bg-dark);
    }
  </style>
<?php
}
add_action('wp_head', __NAMESPACE__ . '\init_styles', 7);

function init_scripts() {
?>
  <script>

    const mastodonFeedCreateElement = function(type, className = null) {
      let element = document.createElement(type);
      if(null !== className) {
        element.className = className;
      }
      return element;
    }

    const mastodonFeedCreateElementAccountLink = function(account) {
      let accountLinkElem = mastodonFeedCreateElement('a');
      accountLinkElem.href = account.url;

      let accountImageElem = mastodonFeedCreateElement('img', 'avatar');
      accountImageElem.src = account.avatar_static;

      accountLinkElem.addEventListener('mouseover', (event) => {
        accountLinkElem.querySelector('.avatar').src = account.avatar;
      });
      accountLinkElem.addEventListener('mouseout', (event) => {
        accountLinkElem.querySelector('.avatar').src = account.avatar_static;
      });

      accountLinkElem.appendChild(accountImageElem);
      // inject emojis
      let displayName = account.display_name;
      if(account.emojis.length > 0) {
        account.emojis.forEach(function(emoji) {
          displayName = mastodonFeedInjectEmoji(displayName, emoji);
        });
      }
      accountLinkElem.innerHTML += ' ' + displayName;
      return accountLinkElem;
    }

    const mastodonFeedCreateElementPermalink = function(status, label) {
      let linkElem = mastodonFeedCreateElement('a');
      linkElem.href = status.url;
      linkElem.appendChild(document.createTextNode(label));
      return linkElem;
    }

    const mastodonFeedCreateElementMediaAttachments = function(status, options) {
      let attachments = status.media_attachments;
      let mediaWrapperElem = mastodonFeedCreateElement('div', 'media');
      for(let mediaIndex = 0; mediaIndex < attachments.length; mediaIndex++) {
        let media = attachments[mediaIndex];
        let mediaElem = mastodonFeedCreateElement('div', media.type);
        if('image' == media.type) {
          let mediaElemImgLink = mastodonFeedCreateElement('a');
          let imageUrl = media.url;
          if(null !== media.remote_url) {
            imageUrl = media.remote_url;
          }
          else if('full' !== options.images.size && null !== media.preview_url) {
            imageUrl = media.preview_url;
          }
          mediaElemImgLink.href = status.url;
          if('image' === options.images.link) {
            mediaElemImgLink.href = media.remote_url ?? media.url;
          }
          mediaElemImgLink.style.backgroundImage = 'url("' + imageUrl + '")';
          if(null !== media.description) {
            mediaElem.title = media.description;
          }
          if(options.images.preserveImageAspectRatio) {
            let mediaElemImgImage = mastodonFeedCreateElement('img');
            mediaElemImgImage.src = imageUrl;
            mediaElemImgLink.appendChild(mediaElemImgImage);
          }
          mediaElem.appendChild(mediaElemImgLink);
        }
        else if('gifv' == media.type) {
          let mediaElemGifvLink = mastodonFeedCreateElement('a');
          mediaElemGifvLink.href = status.url;
          let mediaElemGifv = mastodonFeedCreateElement('video', 'requiresInteraction');
          if(null === media.remote_url) {
            mediaElemGifv.src = media.url;
          }
          else {
            mediaElemGifv.src = media.remote_url;
          }
          mediaElemGifv.loop = true;
          mediaElemGifv.muted = 'muted';
          if(null !== media.description) {
            mediaElemGifv.title = media.description;
          }
          mediaElemGifvLink.appendChild(mediaElemGifv);
          mediaElem.appendChild(mediaElemGifvLink);

          mediaElemGifv.addEventListener('mouseover', (event) => {
            mediaElemGifv.play();
          });
          mediaElemGifv.addEventListener('mouseout', (event) => {
            mediaElemGifv.pause();
            mediaElemGifv.currentTime = 0;
          });
        }
        else {
          // TODO implement support for other media types
          //      currently only image and gifv support implemented
          mediaElem.innerHTML = 'Stripped ' + media.type + ' - only available on instance<br />';
          let permalinkElem = mastodonFeedCreateElement('span', 'permalink');
          permalinkElem.appendChild(mastodonFeedCreateElementPermalink(status, options.text.viewOnInstance));
          mediaElem.appendChild(permalinkElem);
        }
        mediaWrapperElem.appendChild(mediaElem);
      }
      return mediaWrapperElem;
    }

    const mastodonFeedCreateElementPreviewCard = function(card)  {
      let cardElem = mastodonFeedCreateElement('div', 'card');
          
      if(null === card.html || card.html.length < 1) {
        let cardElemMeta = mastodonFeedCreateElement('div', 'meta');

        if(null !== card.image) {
          let cardElemImageWrapper = mastodonFeedCreateElement('div', 'image');
          let cardElemImage = mastodonFeedCreateElement('img');
          cardElemImage.src = card.image;
          cardElemImageWrapper.appendChild(cardElemImage);
          cardElemMeta.appendChild(cardElemImageWrapper);
        }

        let cardElemTitle = mastodonFeedCreateElement('div', 'title');
        cardElemTitle.innerHTML = card.title;
        cardElemMeta.appendChild(cardElemTitle);

        let cardElemDescription = mastodonFeedCreateElement('div', 'description');
        cardElemDescription.innerHTML = card.description;
        cardElemMeta.appendChild(cardElemDescription);
        
        if(card.url === null) {
          cardElem.appendChild(cardElemMeta);
        }
        else {
          let cardElemLink = mastodonFeedCreateElement('a');
          cardElemLink.href = card.url;
          cardElemLink.appendChild(cardElemMeta);
          cardElem.appendChild(cardElemLink);
        }
      }
      else {
        cardElem.innerHTML = card.html;
      }
      return cardElem;
    }

    const mastodonFeedCreateElementTimeinfo = function(status, options, url = false) {
      let createdInfo = mastodonFeedCreateElement('span', 'permalink');
      createdInfo.innerHTML = ' ' + options.text.permalinkPre + ' ';
      if(false === url) {
        createdInfo.innerHTML += new Date(status.created_at).toLocaleString(options.localization.date.locale, options.localization.date.options);
      }
      else {
        createdInfo.appendChild(mastodonFeedCreateElementPermalink(status, new Date(status.created_at).toLocaleString(options.localization.date.locale, options.localization.date.options)));
      }
      createdInfo.innerHTML += ' ' + options.text.permalinkPost;
      return createdInfo;
    }

    const mastodonFeedInjectEmoji = function(string, emoji) {
      return string.replaceAll(':' + emoji.shortcode + ':', '<img class="emoji" src="' + emoji.url + '" title="' + emoji.shortcode + '" />');
    }

    const mastodonFeedRenderStatuses = function(statuses, rootElem, options) {
      if(statuses.length < 1) {
        rootElem.innerHTML = options.text.noStatuses;
      }
      else {
        for(let i = 0; i < statuses.length; i++) {
          let status = statuses[i];
          let isEdited = (null === status.edited_at ? true : false);
          let isReblog = (null === status.reblog ? false : true);

          let statusElem = mastodonFeedCreateElement('div', 'include-mastodon-feed-status');

          // add account meta info
          if(!options.content.hideStatusMeta) {
            let accountElem = mastodonFeedCreateElement('div', 'account');
            if(isReblog) {
              let boosterElem = mastodonFeedCreateElement('span', 'booster');
              boosterElem.appendChild(document.createTextNode( options.text.boosted ));
              accountElem.appendChild(boosterElem);
            }            
            accountElem.appendChild(mastodonFeedCreateElementAccountLink(status.account));
            if(!options.content.hideDateTime) {
              accountElem.appendChild(mastodonFeedCreateElementTimeinfo(status, options, (isReblog ? false : status.url)));
            }
            if(null !== status.edited_at) {
              accountElem.innerHTML += ' ' + options.text.edited;
            }
            statusElem.appendChild(accountElem);
          }

          // prepare content rendering
          let showStatus = status;
          if(isReblog) {
            showStatus = status.reblog;
          }
          let contentWrapperElem = mastodonFeedCreateElement('div', 'contentWrapper' + (isReblog ? ' boosted' : ''));

          // add boosted post meta info
          if(isReblog) {
            let boostElem = mastodonFeedCreateElement('div', 'account');
            let boostAccountLink = mastodonFeedCreateElementAccountLink(showStatus.account);
            boostElem.appendChild(boostAccountLink);
            boostElem.appendChild(mastodonFeedCreateElementTimeinfo(showStatus, options, showStatus.url));

            contentWrapperElem.appendChild(boostElem);
          }

          let contentElem = mastodonFeedCreateElement('div', 'include-mastodon-feed-content');

          // handle content warnings
          if(showStatus.sensitive || showStatus.spoiler_text.length > 0) {
            let cwElem = mastodonFeedCreateElement('div', 'contentWarning');

            if(showStatus.spoiler_text.length > 0) {
              let cwTitleElem = mastodonFeedCreateElement('div', 'title');
              cwTitleElem.innerHTML = showStatus.spoiler_text;
              cwElem.appendChild(cwTitleElem);
            }

            let cwLinkElem = mastodonFeedCreateElement('a');
            cwLinkElem.href = '#';
            cwLinkElem.onclick = function() {
              this.parentElement.style = 'display: none;';
              this.parentElement.nextSibling.style = 'display: block;';
              return false;
            }
            cwLinkElem.innerHTML = options.text.showContent;
            cwElem.appendChild(cwLinkElem);

            contentWrapperElem.appendChild(cwElem);
            contentElem.style = 'display: none;';
          }

          // add regular content
          let renderContent = showStatus.content;
          // inject emojis
          if(showStatus.emojis.length > 0) {
            showStatus.emojis.forEach(function(emoji) {
              renderContent = mastodonFeedInjectEmoji(renderContent, emoji);
            });
          }
          contentElem.innerHTML += renderContent;

          // handle media attachments
          if(showStatus.media_attachments.length > 0) {
            let mediaAttachmentsElem = mastodonFeedCreateElementMediaAttachments(showStatus, options);
            contentElem.appendChild(mediaAttachmentsElem);
          }

          // handle preview card
          if(options.showPreviewCards && showStatus.card != null) {
            let cardElem = mastodonFeedCreateElementPreviewCard(showStatus.card);
            contentElem.appendChild(cardElem);
          }

          contentWrapperElem.appendChild(contentElem);
          statusElem.appendChild(contentWrapperElem);
          rootElem.appendChild(statusElem);
        }
      }
      if('_self' != options.linkTarget) {
        rootElem.querySelectorAll('a').forEach(function(e) {
          e.target = options.linkTarget;
        });
      }
    }

    const mastodonFeedLoad = function(url, elementId, options) {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.responseType = 'json';
      xhr.onload = function() {
        const statuses = xhr.response;
        const rootElem = document.getElementById(elementId);
        rootElem.innerHTML = '';
        <?php if(true === INCLUDE_MASTODON_FEED_DEBUG) : ?>
          console.log("<?php echo __NAMESPACE__; ?>", 'url', url);
          console.log("<?php echo __NAMESPACE__; ?>", 'elementId', elementId);
          console.log("<?php echo __NAMESPACE__; ?>", 'options', options);
        <?php endif; ?>
        if (xhr.status === 200) {
          <?php if(true === INCLUDE_MASTODON_FEED_DEBUG) : ?>
            console.log("<?php echo __NAMESPACE__; ?>", 'response', xhr.response);
          <?php endif; ?>
          if(options.excludeConversationStarters && statuses.length > 0) {
            const filteredStatuses = [];
            for(let i = 0; i < statuses.length; i++) {
              let includeStatus = true;
              if(statuses[i].mentions.length > 0) {
                const statusContent = document.createElement('div');
                statusContent.innerHTML = statuses[i].content;
                const mentionUsername = statuses[i].mentions[0].acct.split('@')[0];
                const plainTextContent = statusContent.textContent || statusContent.innerText;
                if(plainTextContent.substring(1, ('@' + mentionUsername).length) == mentionUsername) {
                  includeStatus = false;
                }
              }
              if(includeStatus) {
                filteredStatuses.push(statuses[i]);
              }
            }
            mastodonFeedRenderStatuses(filteredStatuses, rootElem, options);
          }
          else  {
            mastodonFeedRenderStatuses(statuses, rootElem, options);
          }
        }
        else {
          <?php if(true === INCLUDE_MASTODON_FEED_DEBUG) : ?>
            console.log("<?php echo __NAMESPACE__; ?>", 'response error', xhr);
          <?php endif; ?>
          rootElem.appendChild(document.createTextNode(xhr.response.error));
        }
      };
      xhr.send();
    }
  </script>
<?php
}
add_action('wp_footer', __NAMESPACE__ . '\init_scripts');

function display_feed($atts) {
  $atts = shortcode_atts(
      array(
          'instance' => ( INCLUDE_MASTODON_FEED_DEFAULT_INSTANCE === false ? false : filter_var( INCLUDE_MASTODON_FEED_DEFAULT_INSTANCE, FILTER_UNSAFE_RAW ) ),
					'account' => false,
          'tag' => false,
          'limit' => INCLUDE_MASTODON_FEED_LIMIT,
          'excludeboosts' => filter_var(esc_html(INCLUDE_MASTODON_FEED_EXCLUDE_BOOSTS), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'excludereplies' => filter_var(esc_html(INCLUDE_MASTODON_FEED_EXCLUDE_REPLIES), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'excludeconversationstarters' => filter_var(esc_html(INCLUDE_MASTODON_FEED_EXCLUDE_CONVERSATIONSTARTERS), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'onlypinned' => filter_var(esc_html(INCLUDE_MASTODON_FEED_ONLY_PINNED), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'onlymedia' => filter_var(esc_html(INCLUDE_MASTODON_FEED_ONLY_MEDIA), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'preserveimageaspectratio' => filter_var(esc_html(INCLUDE_MASTODON_FEED_PRESERVE_IMAGE_ASPECT_RATIO), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'imagesize' => INCLUDE_MASTODON_FEED_IMAGE_SIZE,
          'imagelink' => INCLUDE_MASTODON_FEED_IMAGE_LINK,
          'tagged' => INCLUDE_MASTODON_FEED_TAGGED,
          'linktarget' => INCLUDE_MASTODON_FEED_LINKTARGET,
          'showpreviewcards' => filter_var(esc_html(INCLUDE_MASTODON_FEED_SHOW_PREVIEWCARDS), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'hidestatusmeta' => filter_var(esc_html(INCLUDE_MASTODON_FEED_HIDE_STATUS_META), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'hidedatetime' => filter_var(esc_html(INCLUDE_MASTODON_FEED_HIDE_DATETIME), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'text-loading' => INCLUDE_MASTODON_FEED_TEXT_LOADING,
          'text-nostatuses' => INCLUDE_MASTODON_FEED_TEXT_NO_STATUSES,
          'text-boosted' => INCLUDE_MASTODON_FEED_TEXT_BOOSTED,
          'text-viewoninstance' => INCLUDE_MASTODON_FEED_TEXT_VIEW_ON_INSTANCE,
          'text-showcontent' => INCLUDE_MASTODON_FEED_TEXT_SHOW_CONTENT,
          'text-permalinkpre' => INCLUDE_MASTODON_FEED_TEXT_PERMALINK_PRE,
          'text-permalinkpost' => INCLUDE_MASTODON_FEED_TEXT_PERMALINK_POST,
          'text-edited' => INCLUDE_MASTODON_FEED_TEXT_EDITED,
          'date-locale' => INCLUDE_MASTODON_FEED_DATE_LOCALE,
          'darkmode' => filter_var(esc_html(INCLUDE_MASTODON_FEED_DARKMODE), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
      ), array_change_key_case($atts, CASE_LOWER)
  );

  if(false === filter_var($atts['instance'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    return error('missing configuration: instance');
  }
  if(false === filter_var($atts['account'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) && false === filter_var($atts['tag'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    return error('missing configuration: account id or tag');
  }


  if(false !== filter_var($atts['account'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $apiUrl = 'https://'.urlencode($atts['instance']).'/api/v1/accounts/'.$atts['account'].'/statuses';
  }
  if(false !== filter_var($atts['tag'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $apiUrl = 'https://'.urlencode($atts['instance']).'/api/v1/timelines/tag/'.urlencode($atts['tag']);
  }

  $getParams = [];
  if($atts['limit'] != 20 && $atts['limit'] > 0) {
    $getParams[] = 'limit=' . filter_var( $atts['limit'], FILTER_SANITIZE_NUMBER_INT );
  }
  if(false !== filter_var($atts['excludeboosts'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'exclude_reblogs=true';
  }
  if(false !== filter_var($atts['excludereplies'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'exclude_replies=true';
  }
  if(true === filter_var($atts['onlypinned'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'pinned=true';
  }
  if(true === filter_var($atts['onlymedia'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'only_media=true';
  }
  if(false !== filter_var($atts['tagged'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'tagged=' . filter_var( $atts['tagged'], FILTER_UNSAFE_RAW );
  }
  if(sizeof($getParams) > 0) {
    $apiUrl .= '?' . implode('&', $getParams);
  }
  $elemId = uniqid('include-mastodon-feed-');
  ob_start();
?>
  <script>
    window.addEventListener("load", () => {
      mastodonFeedLoad(
        "<?php echo esc_url( $apiUrl, ['https'], 'apicall' ); ?>",
        "<?php echo filter_var( $elemId, FILTER_UNSAFE_RAW ); ?>",
        {
          linkTarget: "<?php echo esc_js(filter_var( $atts['linktarget'], FILTER_UNSAFE_RAW )); ?>",
          showPreviewCards: <?php echo (filter_var( $atts['showpreviewcards'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>,
          excludeConversationStarters: <?php echo (filter_var( $atts['excludeconversationstarters'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>,
          content: {
            hideStatusMeta: <?php echo (filter_var( $atts['hidestatusmeta'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>,
            hideDateTime: <?php echo (filter_var( $atts['hidedatetime'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>
          },
          images: {
            preserveImageAspectRatio: <?php echo (filter_var( $atts['preserveimageaspectratio'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>,
            size: "<?php echo ( "full" === $atts['imagesize'] ? "full" : "preview" ); ?>",
            link: "<?php echo ( "image" === $atts['imagelink'] ? "image" : "status" ); ?>",
          },
          text: {
            boosted: "<?php echo esc_html( $atts['text-boosted'] ); ?>",
            noStatuses: "<?php echo esc_html( $atts['text-nostatuses'] ); ?>",
            viewOnInstance: "<?php echo esc_js( $atts['text-viewoninstance'] ); ?>",
            showContent: "<?php echo esc_html( $atts['text-showcontent'] ); ?>",
            permalinkPre: "<?php echo esc_html( $atts['text-permalinkpre'] ); ?>",
            permalinkPost: "<?php echo esc_html( $atts['text-permalinkpost'] ); ?>",
            edited: "<?php echo esc_html( $atts['text-edited'] ); ?>",
          },
          localization: {
            date: {
              locale: "<?php echo esc_js( filter_var( $atts['date-locale'], FILTER_UNSAFE_RAW ) ); ?>",
              options: <?php echo filter_var( INCLUDE_MASTODON_FEED_DATE_OPTIONS, FILTER_UNSAFE_RAW ); ?>,
            }
          }
        }
      );
    });
  </script>
  <div class="include-mastodon-feed<?php echo (true == $atts['darkmode'] ? ' dark' : ''); ?>" id="<?php echo esc_attr( $elemId ); ?>"><?php echo esc_html( $atts['text-loading'] ); ?></div>
<?php
  return ob_get_clean();
}
add_shortcode('include-mastodon-feed', __NAMESPACE__ . '\display_feed');
