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
  accountLinkElem.setAttribute('aria-label', 'Link to Mastodon account of ' + account.display_name);

  let accountImageElem = mastodonFeedCreateElement('img', 'avatar');
  accountImageElem.src = account.avatar_static;
  accountImageElem.loading = 'lazy';
  accountImageElem.alt = 'Mastodon avatar image of ' + account.display_name;

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

const mastodonFeedCreateElementPermalink = function(status, label, ariaLabel) {
  let linkElem = mastodonFeedCreateElement('a');
  linkElem.href = status.url;
  linkElem.appendChild(document.createTextNode(label));
  linkElem.setAttribute('aria-label', ariaLabel);
  return linkElem;
}

const mastodonFeedCreateElementMediaAttachments = function(status, options) {
  let attachments = status.media_attachments;
  let mediaWrapperElem = mastodonFeedCreateElement('ol', 'media');
  for(let mediaIndex = 0; mediaIndex < attachments.length; mediaIndex++) {
    let media = attachments[mediaIndex];
    let mediaElem = mastodonFeedCreateElement('li', media.type);
    if('image' == media.type) {
      let mediaElemImgLink = mastodonFeedCreateElement('a');
      let imageUrl = media.url;
      if('full' !== options.images.size && null !== media.preview_url) {
        imageUrl = media.preview_url;
      }
      mediaElemImgLink.href = status.url;
      if('image' === options.images.link) {
        mediaElemImgLink.href = media.remote_url ?? media.url;
      }
      let mediaElemImgImage = mastodonFeedCreateElement('img');
      mediaElemImgImage.src = imageUrl;
      mediaElemImgImage.loading = 'lazy';
      if(null === media.description) {
        mediaElemImgImage.alt = 'Image attachment of Mastodon post';
      }
      else {
        mediaElemImgImage.alt = media.description;
      }
      if(!options.images.preserveImageAspectRatio) {
        mediaElemImgLink.style.backgroundImage = 'url("' + imageUrl + '")';
        mediaElemImgImage.style.width = '100%';
        mediaElemImgImage.style.height = '100%';
        mediaElemImgImage.style.opacity = 0;
      }
      mediaElemImgLink.appendChild(mediaElemImgImage);
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
      if(null === media.description) {
        mediaElemGifv.alt = 'Video attachment of Mastodon post';
      }
      else {
        mediaElemGifv.alt = media.description;
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
    else if('video' == media.type) {
      if(null == media.preview_url || (null == media.remote_url && null == media.url)) {
        mediaElem.innerHTML = '<p class="hint">Error loading preview. <a href="' + status.url + '">Open on instance</a></p>';
      }
      else {
        const mediaElemImgLink = mastodonFeedCreateElement('a');
        const imageUrl = media.preview_url;
        mediaElemImgLink.href = status.url;
        const mediaElemImgImage = mastodonFeedCreateElement('img');
        mediaElemImgImage.src = imageUrl;
        mediaElemImgImage.loading = 'lazy';
        if(null === media.description) {
          mediaElemImgImage.alt = 'Video attachment of Mastodon post';
        }
        else {
          mediaElemImgImage.alt = media.description;
        }
        mediaElemImgLink.addEventListener('click', (event) => {
          event.stopPropagation();
          event.preventDefault();
          const videoElem = mastodonFeedCreateElement('video');
          videoElem.src = media.url;
          if(null == media.url) {
            videoElem.src = media.remote_url;
          }
          videoElem.controls = true;
          videoElem.autoplay = true;
          videoElem.muted = true;
          videoElem.addEventListener('error', () => {
            mediaElem.innerHTML = '<p class="hint">Error loading video. <a href="' + status.url + '">Open on instance</a></p>';
          });
          mediaElem.innerHTML = '';
          mediaElem.appendChild(videoElem);
        });
        mediaElemImgLink.appendChild(mediaElemImgImage);
        mediaElemImgLink.innerHTML += '<br />Click to play video';
        mediaElem.appendChild(mediaElemImgLink);
      }
    }
    else if('audio' == media.type) {
      if(null == media.url && null == media.remote_url) {
        mediaElem.innerHTML = '<p class="hint">Error loading audio media. <a href="' + status.url + '">Open on instance</a></p>';
      }
      else {
        const mediaElemAudioWrapper = mastodonFeedCreateElement('div');
        if(null !== media.preview_url) {
          mediaElemAudioWrapper.style.backgroundImage = 'url("' + media.preview_url + '")';
          mediaElemAudioWrapper.classList.add('has-preview');
        }
        const audioElem = mastodonFeedCreateElement('audio');
        audioElem.src = media.url;
        if(null == media.url) {
          audioElem.src = media.remote_url;
        }
        audioElem.controls = true;
        audioElem.addEventListener('error', () => {
          mediaElem.innerHTML = '<p class="hint">Error loading audio media. <a href="' + status.url + '">Open on instance</a></p>';
        });
        mediaElemAudioWrapper.appendChild(audioElem);
        mediaElem.appendChild(mediaElemAudioWrapper);
        if(null !== media.description) {
          const descriptionElem = mastodonFeedCreateElement('p', 'description');
          descriptionElem.innerHTML = media.description;
          mediaElem.appendChild(descriptionElem);
        }
      }
    }
    else {
      mediaElem.innerHTML = 'Stripped ' + media.type + ' - only available on instance<br />';
      let permalinkElem = mastodonFeedCreateElement('span', 'permalink');
      permalinkElem.appendChild(mastodonFeedCreateElementPermalink(status, options.text.viewOnInstance, 'Link to Mastodon post'));
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
      if(null === card.image_description) {
        cardElemImage.alt = 'Preview image content card';
      }
      else {
        cardElemImage.alt = card.image_description;
      }
      cardElemImage.src = card.image;
      cardElemImage.loading = 'lazy';
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
      cardElemLink.setAttribute('aria-label', 'Link embedded in Mastodon post');
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
    createdInfo.appendChild(mastodonFeedCreateElementPermalink(status, new Date(status.created_at).toLocaleString(options.localization.date.locale, options.localization.date.options), 'Link to Mastodon post'));
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

      let statusElem = mastodonFeedCreateElement('li', 'status');

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

      let contentElem = mastodonFeedCreateElement('div', 'content');
      // Add lang attribute from status or fallback to reblog's language
      if (showStatus.language) {
        contentElem.setAttribute('lang', showStatus.language);
      } else if (showStatus.reblog && showStatus.reblog.language) {
        contentElem.setAttribute('lang', showStatus.reblog.language);
      }

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
        cwLinkElem.setAttribute('aria-label', 'Show content despite warning');
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
  rootElem.querySelectorAll('a').forEach(function(e) {
    if('_self' != options.linkTarget) {
      e.target = options.linkTarget;
    }
  });
}

const mastodonFeedLoad = function(url, elementId, options) {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', url, true);
  xhr.responseType = 'json';
  xhr.onload = function() {
    let statuses = xhr.response;
    const rootElem = document.getElementById(elementId);
    rootElem.innerHTML = '';
    if (window.includeMastodonFeedDebug) {
      console.log("IncludeMastodonFeedPlugin", 'url', url);
      console.log("IncludeMastodonFeedPlugin", 'elementId', elementId);
      console.log("IncludeMastodonFeedPlugin", 'options', options);
    }
    if (xhr.status === 200) {
      if (window.includeMastodonFeedDebug) {
        console.log("IncludeMastodonFeedPlugin", 'response', xhr.response);
      }
      if(options.excludeTags) {
        const filteredStatuses = [];
        const excludeTags = options.excludeTags.toLowerCase().split(',');
        for (const status of statuses) {
          if(status.tags && Array.isArray(status.tags)) {
            let excludeStatus = false;
            for (const tag of status.tags) {
              if(excludeTags.includes(tag.name)) {
                excludeStatus = true;
                break;
              }
            }
            if(!excludeStatus) {
              filteredStatuses.push(status);
            }
          }
        }
        statuses = filteredStatuses;
      }
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
      if (window.includeMastodonFeedDebug) {
        console.log("IncludeMastodonFeedPlugin", 'response error', xhr);
      }
      rootElem.appendChild(document.createTextNode(xhr.response.error));
    }
  };
  xhr.send();
}
