<?php
/*
  Plugin Name: Include Mastodon Feed
	Plugin URI: https://wolfgang.lol/code/include-mastodon-feed-wordpress-plugin
	Description: Plugin providing [include-mastodon-feed] shortcode
	Version: 1.0.1
	Author: wolfgang.lol
	Author URI: https://wolfgang.lol
*/

// load user config if available
if(file_exists( plugin_dir_path( __FILE__ ) . DIRECTORY_SEPARATOR . 'config.php' )) {
  require_once( plugin_dir_path( __FILE__ ) . DIRECTORY_SEPARATOR . 'config.php' );
}
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
];
foreach($constants as $constant) {
  if(!defined($constant['key'])) {
      define($constant['key'], $constant['value']);
  }
}
unset($constants);

function include_mastodon_feed_error($msg) {
  return '[include-mastodon-feed] ' . $msg;
}


function include_mastodon_feed_init_styles() {
  ob_start();
?>
  <style>
    :root {
      --include-mastodon-feed-bg-light: <?php echo esc_attr( INCLUDE_MASTODON_FEED_STYLE_BG_LIGHT_COLOR ); ?>;
      --include-mastodon-feed-bg-dark: <?php echo esc_attr( INCLUDE_MASTODON_FEED_STYLE_BG_DARK_COLOR ); ?>;
      --include-mastodon-feed-accent-color: <?php echo esc_attr( INCLUDE_MASTODON_FEED_STYLE_ACCENT_COLOR ); ?>;
      --include-mastodon-feed-accent-font-color: <?php echo esc_attr( INCLUDE_MASTODON_FEED_STYLE_ACCENT_FONT_COLOR ); ?>;
      --include-mastodon-feed-border-radius: <?php echo esc_attr( INCLUDE_MASTODON_FEED_STYLE_BORDER_RADIUS ); ?>;
    }

    .include-mastodon-feed .status {
      margin: 0.5rem 0 1.5rem;
      border-radius: var(--include-mastodon-feed-border-radius);
      padding: 0.5rem;
      background: var(--include-mastodon-feed-bg-light);
    }
    .include-mastodon-feed .status a {
      color: var(--include-mastodon-feed-accent-color);
      text-decoration: none;
    }
    .include-mastodon-feed .status a:hover {
      text-decoration: underline;
    }
    .include-mastodon-feed .account .permalink {
      float: right;
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
    .include-mastodon-feed .boosted .account a:nth-child(2),
    .include-mastodon-feed .contentWarning a {
      border-radius: var(--include-mastodon-feed-border-radius);
      padding: 0.15rem 0.5rem;
      background: var(--include-mastodon-feed-accent-color);
      color: var(--include-mastodon-feed-accent-font-color);
    }
    .include-mastodon-feed .boosted .account a:nth-child(2):hover,
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
    .include-mastodon-feed.content .emoji {
      height: 1rem;
    }
    .include-mastodon-feed .media {
      display: flex;
      justify-content: space-around;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin: 1rem;
    }
    .include-mastodon-feed .media .image {
      font-size: 0.8rem;
      font-weight: bold;
      text-align: center;
      flex-basis: calc(50% - 0.5rem);
      flex-grow: 1;
    }
    .include-mastodon-feed .media .image img {
      border-radius: var(--include-mastodon-feed-border-radius);
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
  


    .include-mastodon-feed.dark .status,
    .include-mastodon-feed.dark .contentWrapper.boosted,
    .include-mastodon-feed.dark .card {
      background: var(--include-mastodon-feed-bg-dark);
    }
  </style>
<?php
  echo ob_get_clean();
}
add_action('wp_head', 'include_mastodon_feed_init_styles', 7);

function include_mastodon_feed_init_scripts() {
  ob_start();
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

    const mastodonFeedCreateElementPermalink = function(status) {
      let linkElem = mastodonFeedCreateElement('a');
      linkElem.href = status.url;
      linkElem.appendChild(document.createTextNode('view on instance'));
      return linkElem;
    }

    const mastodonFeedCreateElementMediaAttachments = function(attachments) {
      let mediaWrapperElem = mastodonFeedCreateElement('div', 'media');
      for(let mediaIndex = 0; mediaIndex < attachments.length; mediaIndex++) {
        let media = attachments[mediaIndex];
        let mediaElem = mastodonFeedCreateElement('div', 'image');
        if('image' == media.type) {
          let mediaElemImg = mastodonFeedCreateElement('img');
          if(null === media.remote_url) {
            mediaElemImg.src = media.preview_url;
          }
          else {
            mediaElemImg.src = media.remote_url;
          }
          if(null !== media.description) {
            mediaElemImg.title = media.description;
          }
          mediaElem.appendChild(mediaElemImg);
        }
        else {
          // TODO implement support for other media types
          //      currently only image support implemented
          mediaElem.innerHTML = 'Stripped ' + media.type + ' - only available on instance<br />';
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

    const mastodonFeedCreateTimeinfo = function(status) {
      let createdInfo = document.createTextNode(' on ' + new Date(status.created_at).toLocaleDateString('en-US'));
      createdInfo.textContent += ' ' + new Date(status.created_at).toLocaleTimeString('en-US');
      if(null !== status.edited_at) {
        createdInfo.textContent += ' (edited)';
      }
      return createdInfo;
    }

    const mastodonFeedInjectEmoji = function(string, emoji) {
      return string.replace(':' + emoji.shortcode + ':', '<img class="emoji" src="' + emoji.url + '" title="' + emoji.shortcode + '" />');
    }

    const mastodonFeedRenderStatuses = function(statuses, rootElem) {
      for(let i = 0; i < statuses.length; i++) {
        let status = statuses[i];
        let isEdited = (null === status.edited_at ? true : false);
        let isReblog = (null === status.reblog ? false : true);

        let statusElem = mastodonFeedCreateElement('div', 'status');

        // add account meta info
        let accountElem = mastodonFeedCreateElement('div', 'account');

        if(isReblog) {
          let boosterElem = mastodonFeedCreateElement('span', 'booster');
          boosterElem.appendChild(document.createTextNode('boosted 🚀'));
          accountElem.appendChild(boosterElem);
        }
        else {
          let origPermalinkElem = mastodonFeedCreateElement('span', 'permalink');
          origPermalinkElem.appendChild(mastodonFeedCreateElementPermalink(status));
          accountElem.appendChild(origPermalinkElem);
        }
        accountElem.appendChild(mastodonFeedCreateElementAccountLink(status.account));
        accountElem.appendChild(mastodonFeedCreateTimeinfo(status));
        
        statusElem.appendChild(accountElem);

        // prepare content rendering
        let showStatus = status;
        if(isReblog) {
          showStatus = status.reblog;
        }
        let contentWrapperElem = mastodonFeedCreateElement('div', 'contentWrapper' + (isReblog ? ' boosted' : ''));
        let permalinkElem = mastodonFeedCreateElement('span', 'permalink');
        permalinkElem.appendChild(mastodonFeedCreateElementPermalink(showStatus));

        // add boosted post meta info
        if(isReblog) {
          let boostElem = mastodonFeedCreateElement('div', 'account');
          boostElem.appendChild(permalinkElem);
          let boostAccountLink = mastodonFeedCreateElementAccountLink(showStatus.account);
          boostElem.appendChild(boostAccountLink);
          boostElem.appendChild(mastodonFeedCreateTimeinfo(showStatus));

          contentWrapperElem.appendChild(boostElem);
        }

        let contentElem = mastodonFeedCreateElement('div', 'content');

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
          cwLinkElem.innerHTML = 'Show content';
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
          let mediaAttachmentsElem = mastodonFeedCreateElementMediaAttachments(showStatus.media_attachments);
          contentElem.appendChild(mediaAttachmentsElem);
        }

        // handle preview card
        if(showStatus.card != null) {
          let cardElem = mastodonFeedCreateElementPreviewCard(showStatus.card);
          contentElem.appendChild(cardElem);
        }

        contentWrapperElem.appendChild(contentElem);
        statusElem.appendChild(contentWrapperElem);
        rootElem.appendChild(statusElem);
      }
    }

    const mastodonFeedLoad = function(url, elementId) {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.responseType = 'json';
      xhr.onload = function() {
        const statuses = xhr.response;
        const rootElem = document.getElementById(elementId);
        rootElem.innerHTML = '';
        if (xhr.status === 200) {
          <?php if(true === INCLUDE_MASTODON_FEED_DEBUG) : ?>
            console.log(xhr.response);
          <?php endif; ?>
          mastodonFeedRenderStatuses(statuses, rootElem);
        }
        else {
          <?php if(true === INCLUDE_MASTODON_FEED_DEBUG) : ?>
            console.log(xhr);
          <?php endif; ?>
          rootElem.appendChild(document.createTextNode(xhr.response.error));
        }
      };
      xhr.send();
    }
  </script>
<?php
  echo ob_get_clean();
}
add_action('wp_footer', 'include_mastodon_feed_init_scripts');

function include_mastodon_feed_display_feed($atts) {
  $atts = shortcode_atts(
      array(
          'instance' => false,
					'account' => false,
          'loading' => null,
          'darkmode' => false,
      ), $atts
  );
  if(false === $atts['instance']) {
    return include_mastodon_feed_error('missing configuration: instance');
  }
  if(false === $atts['account']) {
    return include_mastodon_feed_error('missing configuration: account id');
  }

  $apiUrl = 'https://'.urlencode($atts['instance']).'/api/v1/accounts/'.urlencode($atts['account']).'/statuses';
  $elemId = uniqid('include-mastodon-feed-');
  ob_start();
?>
  <script>
    window.addEventListener("load", () => {
      mastodonFeedLoad("<?php echo esc_url( $apiUrl ); ?>", "<?php echo esc_js( $elemId ); ?>");
    });
  </script>
  <div class="include-mastodon-feed<?php echo ('true' == $atts['darkmode'] ? ' dark' : ''); ?>" id="<?php echo esc_attr( $elemId ); ?>"><?php echo (null === $atts['loading'] ? 'Loading Mastodon feed...' : esc_html( $atts['loading']) ); ?></div>
<?php
  return ob_get_clean();
}
add_shortcode('include-mastodon-feed', 'include_mastodon_feed_display_feed');