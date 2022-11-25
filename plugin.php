<?php
/*
  Plugin Name: Mastodon Feed
	Plugin URI: https://www.wolfgang.lol/code/mastodon-feed-wordpress-plugin
	Description: Plugin providing [mastodon-feed] shortcode
	Version: 1.0
	Author: wolfgang.lol
	Author URI: https://www.wolfgang.lol
*/

// load user config if available
$userConfigPath = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
if(file_exists($userConfigPath) && is_readable($userConfigPath)) {
  require_once($userConfigPath);
}
// set defaults
$constants = [
  [
      'key' => 'MASTODON_FEED_DEBUG',
      'value' => false,
  ],
  [
      'key' => 'MASTODON_FEED_DEFAULT_INSTANCE',
      'value' => 'mastodon.social',
  ],
  [
      'key' => 'MASTODON_FEED_STYLE_BG_LIGHT_COLOR',
      'value' => 'rgba(100, 100, 100, 0.15)',
  ],
  [
      'key' => 'MASTODON_FEED_STYLE_BG_DARK_COLOR',
      'value' => 'rgba(155, 155, 155, 0.15)',
  ],
  [
      'key' => 'MASTODON_FEED_STYLE_ACCENT_COLOR',
      'value' => 'rgb(99, 100, 255)',
  ],
  [
      'key' => 'MASTODON_FEED_STYLE_ACCENT_FONT_COLOR',
      'value' => 'rgb(255, 255, 255)',
  ],
  [
      'key' => 'MASTODON_FEED_STYLE_BORDER_RADIUS',
      'value' => '0.25rem',
  ],
];
foreach($constants as $constant) {
  if(!defined($constant['key'])) {
      define($constant['key'], $constant['value']);
  }
}
unset($constants);

function mastodon_feed_error($msg) {
  return '[mastodon-feed] ' . $msg;
}


function mastodon_feed_init_styles() {
  ob_start();
?>
  <style>
    :root {
      --mastodon-feed-bg-light: <?php echo MASTODON_FEED_STYLE_BG_LIGHT_COLOR; ?>;
      --mastodon-feed-bg-dark: <?php echo MASTODON_FEED_STYLE_BG_DARK_COLOR; ?>;
      --mastodon-feed-accent-color: <?php echo MASTODON_FEED_STYLE_ACCENT_COLOR; ?>;
      --mastodon-feed-accent-font-color: <?php echo MASTODON_FEED_STYLE_ACCENT_FONT_COLOR; ?>;
      --mastodon-feed-border-radius: <?php echo MASTODON_FEED_STYLE_BORDER_RADIUS; ?>;
    }

    .mastodon-feed .status {
      margin: 0.5rem 0 1.5rem;
      border-radius: var(--mastodon-feed-border-radius);
      padding: 0.5rem;
      background: var(--mastodon-feed-bg-light);
    }
    .mastodon-feed .status a {
      color: var(--mastodon-feed-accent-color);
      text-decoration: none;
    }
    .mastodon-feed .status a:hover {
      text-decoration: underline;
    }
    .mastodon-feed .account .permalink {
      float: right;
    }
    .mastodon-feed .avatar {
      height: 1.25rem;
      border-radius: var(--mastodon-feed-border-radius);
      vertical-align: top;
    }
    .mastodon-feed .account {
      font-size: 0.8rem;
    }
    .mastodon-feed .account a {
      display: inline-block;
    }
    .mastodon-feed .account .booster {
      float: right;
      font-style: italic;
    }
    .mastodon-feed .boosted .account a:nth-child(2),
    .mastodon-feed .contentWarning a {
      border-radius: var(--mastodon-feed-border-radius);
      padding: 0.15rem 0.5rem;
      background: var(--mastodon-feed-accent-color);
      color: var(--mastodon-feed-accent-font-color);
    }
    .mastodon-feed .boosted .account a:nth-child(2):hover,
    .mastodon-feed .contentWarning a:hover {
      border-radius: var(--mastodon-feed-border-radius);
      padding: 0.15rem 0.5rem;
      background: var(--mastodon-feed-accent-font-color);
      color: var(--mastodon-feed-accent-color);
      text-decoration: none;
    }
    .mastodon-feed .contentWrapper.boosted {
      margin: 0.5rem 0;
      padding: 0.5rem;
      background: var(--mastodon-feed-bg-light);
    }
    .mastodon-feed .contentWarning {
      text-align: center;
      margin: 1rem;
      padding: 1rem;
    }
    .mastodon-feed .contentWarning .title {
      font-weight: bold;
    }
    .mastodon-feed .media {
      display: flex;
      justify-content: space-around;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin: 1rem;
    }
    .mastodon-feed .media .image {
      font-size: 0.8rem;
      font-weight: bold;
      text-align: center;
      flex-basis: calc(50% - 0.5rem);
      flex-grow: 1;
    }
    .mastodon-feed .media .image img {
      border-radius: var(--mastodon-feed-border-radius);
      max-width: 100%;
    }
    .mastodon-feed .card {
      border-radius: var(--mastodon-feed-border-radius);
      margin: 1rem 0.5rem;
    }
    .mastodon-feed .card iframe {
      border-radius: var(--mastodon-feed-border-radius);
      width: 100%;
      height: 100%;
      aspect-ratio: 2 / 1.25;
    }
    .mastodon-feed .card a {
      border-radius: var(--mastodon-feed-border-radius);
      display: block;
      text-decoration: none;
      color: #000;
    }
    .mastodon-feed.dark .card a {
      color: #fff;
    }
    .mastodon-feed .card a:hover {
      text-decoration: none;
      background: var(--mastodon-feed-accent-color);
      color: var(--mastodon-feed-accent-font-color);
    }
    .mastodon-feed .card .meta {
      background: var(--mastodon-feed-bg-light);
      font-size: 0.8rem;
      padding: 1rem;
    }
    .mastodon-feed .card .image {
      margin-bottom: 0.5rem;
      text-align: center;
    }
    .mastodon-feed .card .image img {
      max-width: 75%;
    }
    .mastodon-feed .card .title {
      font-weight: bold;
    }
  


    .mastodon-feed.dark .status,
    .mastodon-feed.dark .contentWrapper.boosted,
    .mastodon-feed.dark .card {
      background: var(--mastodon-feed-bg-dark);
    }
  </style>
<?php
  echo ob_get_clean();
}
add_action('wp_head', 'mastodon_feed_init_styles', 7);

function mastodon_feed_init_scripts() {
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

    const mastodonFeedCreateAccountLink = function(account) {
      let accountLinkElem = mastodonFeedCreateElement('a');
      accountLinkElem.href = account.url;

      let accountImageElem = mastodonFeedCreateElement('img', 'avatar');
      accountImageElem.src = account.avatar;

      accountLinkElem.appendChild(accountImageElem);
      accountLinkElem.appendChild(document.createTextNode(' ' + account.display_name));
      return accountLinkElem;
    }

    const mastodonFeedCreateTimeinfo = function(status) {
      let createdInfo = document.createTextNode(' on ' + new Date(status.created_at).toLocaleDateString('en-US'));
      createdInfo.textContent += ' ' + new Date(status.created_at).toLocaleTimeString('en-US');
      if(null !== status.edited_at) {
        createdInfo.textContent += ' (edited)';
      }
      return createdInfo;
    }

    const mastodonFeedCreatePermalink = function(status) {
      let linkElem = mastodonFeedCreateElement('a');
      linkElem.href = status.url;
      linkElem.appendChild(document.createTextNode('view on instance'));
      return linkElem;
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
          origPermalinkElem.appendChild(mastodonFeedCreatePermalink(status));
          accountElem.appendChild(origPermalinkElem);
        }
        accountElem.appendChild(mastodonFeedCreateAccountLink(status.account));
        accountElem.appendChild(mastodonFeedCreateTimeinfo(status));
        
        statusElem.appendChild(accountElem);

        // prepare content rendering
        let showStatus = status;
        if(isReblog) {
          showStatus = status.reblog;
        }
        let contentWrapperElem = mastodonFeedCreateElement('div', 'contentWrapper' + (isReblog ? ' boosted' : ''));
        let permalinkElem = mastodonFeedCreateElement('span', 'permalink');
        permalinkElem.appendChild(mastodonFeedCreatePermalink(showStatus));

        // add boosted post meta info
        if(isReblog) {
          let boostElem = mastodonFeedCreateElement('div', 'account');
          boostElem.appendChild(permalinkElem);
          let boostAccountLink = mastodonFeedCreateAccountLink(showStatus.account);
          // boostAccountLink.className = 'account';
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
        contentElem.innerHTML += showStatus.content;

        // handle media attachments
        if(showStatus.media_attachments.length > 0) {
          let mediaWrapperElem = mastodonFeedCreateElement('div', 'media');
          for(let mediaIndex = 0; mediaIndex < showStatus.media_attachments.length; mediaIndex++) {
            let media = showStatus.media_attachments[mediaIndex];
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
          contentElem.appendChild(mediaWrapperElem);
        }

        // handle preview card
        if(showStatus.card != null) {
          let cardElem = mastodonFeedCreateElement('div', 'card');
          
          if(null === showStatus.card.html || showStatus.card.html.length < 1) {
            let cardElemMeta = mastodonFeedCreateElement('div', 'meta');

            if(null !== showStatus.card.image) {
              let cardElemImageWrapper = mastodonFeedCreateElement('div', 'image');
              let cardElemImage = mastodonFeedCreateElement('img');
              cardElemImage.src = showStatus.card.image;
              cardElemImageWrapper.appendChild(cardElemImage);
              cardElemMeta.appendChild(cardElemImageWrapper);
            }

            let cardElemTitle = mastodonFeedCreateElement('div', 'title');
            cardElemTitle.innerHTML = showStatus.card.title;
            cardElemMeta.appendChild(cardElemTitle);

            let cardElemDescription = mastodonFeedCreateElement('div', 'description');
            cardElemDescription.innerHTML = showStatus.card.description;
            cardElemMeta.appendChild(cardElemDescription);
            
            if(showStatus.card.url === null) {
              cardElem.appendChild(cardElemMeta);
            }
            else {
              let cardElemLink = mastodonFeedCreateElement('a');
              cardElemLink.href = showStatus.card.url;
              cardElemLink.appendChild(cardElemMeta);
              cardElem.appendChild(cardElemLink);
            }
          }
          else {
            cardElem.innerHTML = showStatus.card.html;
          }

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
          <?php if(true === MASTODON_FEED_DEBUG) : ?>
            console.log(xhr.response);
          <?php endif; ?>
          mastodonFeedRenderStatuses(statuses, rootElem);
        }
        else {
          <?php if(true === MASTODON_FEED_DEBUG) : ?>
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
add_action('wp_footer', 'mastodon_feed_init_scripts');

function mastodon_feed_display_feed($atts) {
  $atts = shortcode_atts(
      array(
          'instance' => MASTODON_FEED_DEFAULT_INSTANCE,
					'account' => false,
          'loading' => null,
          'darkmode' => false,
      ), $atts
  );
  if(false === $atts['account']) {
    return mastodon_feed_error('missing account id');
  }

  $apiUrl = 'https://'.urlencode($atts['instance']).'/api/v1/accounts/'.urlencode($atts['account']).'/statuses';
  $elemId = uniqid('mastodon-feed-');
  ob_start();
?>
  <script>
    window.addEventListener("load", () => {
      mastodonFeedLoad("<?php echo $apiUrl; ?>", "<?php echo $elemId; ?>");
    });
  </script>
  <div class="mastodon-feed<?php echo ('true' == $atts['darkmode'] ? ' dark' : ''); ?>" id="<?php echo $elemId; ?>"><?php echo (null === $atts['loading'] ? 'Loading mastodon feed...' : $atts['loading']); ?></div>
<?php
  return ob_get_clean();
}
add_shortcode('mastodon-feed', 'mastodon_feed_display_feed');