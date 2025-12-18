=== Include Mastodon Feed ===
Contributors: wolfgang101
Donate link: https://www.buymeacoffee.com/w101
Tags: mastodon, status, feed
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.17.0
License: MIT
License URI: https://directory.fsf.org/wiki/License:Expat

Plugin that provides a shortcode to easily integrate mastodon feeds into wordpress pages.

== Description ==
Plugin that provides an `[include-mastodon-feed]` shortcode to easily integrate mastodon feeds into wordpress pages. Supports personal and tag feeds.

Account and post images are lazy loaded if preserveImageAspectRatio is set to true (default: false).

The plugin is written in PHP and generates native JavaScript to fetch and render the mastodon feed. No special libraries needed.

== Installation ==

1. Upload the "include-mastodon-feed"  directory to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Insert shortcode into any page.

= Shortcode example =
`[include-mastodon-feed instance="YOUR-INSTANCE" account="YOUR-ACCOUNT-ID"]`

= Shortcode attributes =
* **instance** (required)
Domain name of the instance without https:// (e.g. example.org)

* **account** (required)
The account ID (a long number - see FAQ on how to get it)

* **tag**
Use **tag** instead of **account** if you want to embed a tag feed instead of a personal feed

* **cache**
If wordpress should cache Mastodon server API calls (Default: false)
Note: automatically enabled for feeds where auth is used

* **auth**
Auth key that should be used if Mastodon server API needs authentication

* **limit**
Maximum number of statuses (Default: 20)

* **excludeReplies**
Exclude replies to other accounts (Default: false)

* **excludeConversationStarters**
Exclude statuses that start with a user mention (Default: false)

* **excludeBoosts**
Exclude boosted statuses (Default: false)

* **onlyPinned**
Show only pinned statuses (Default: false)

* **onlyMedia**
Show only statuses containing media (Default: false)

* **preserveImageAspectRatio**
Preserve image aspect ratio (Default: false)

* **imageSize**
Load small sized preview images or full size high quality images (Default: preview, full)

* **imageLink**
Link image to status or image (Default: status, image)

* **tagged**
Show only statuses that are tagged with given tag name (Default: false)
No leading #, case insensitive, e.g.: tagged="tagname"

* **excludeTags**
Exclude statuses that are tagged with any of the given tag names (Default: false)
Comma separated list of tags, no leading #, case insensitive, e.g.: excludeTags="tag1,tag2"

* **linkTarget**
Target for all links e.g. new tab would be "_blank" (Default: _self)

* **showPreviewCards**
Show preview cards (Default: true)

* **hideStatusMeta**
Hide status meta information, automatically also hides date and time (Default: false)

* **hideDateTime**
Hide date and time from status meta information (Default: false)

* **darkmode**
Enable dark mode (Default: false)

* **text-loading**
Loading text (Default: Loading Mastodon feed...)

* **text-noStatuses**
Text if no statuses are available (Default: No statuses available)

* **text-boosted**
Text indicating boosted statuses (Default: boosted 🚀)

* **text-viewOnInstance**
View status on instance link text (Default: view on instance)

* **text-showContent**
Text for content warning buttons (Default: Show content)

* **text-permalinkPre**
Text before post permalink (date & time) (Default: on)

* **text-permalinkPost**
Text after post permalink (date & time) (Default: )

* **text-edited**
Text indicating edited posts (Default: (edited))

* **date-locale**
Locale for date string, used in toLocaleString() (Default: en-US)


= Additional customizations =

You can define several plugin constants to set custom default options that will be applied site-wide (e.g. date options can only be set as php constant to mitigate an XSS vulnerability).

1. Open your `wp-config.php` file
2. Search for the line `/* Add any custom values between this line and the "stop editing" line. */`
3. Define the options you want to override between the line from step #2 and `/* That's all, stop editing! Happy publishing. */`

See the included `config-example.php` file for a full list of supported settings.


== Frequently Asked Questions ==

= How do I find my account ID? =
Please feel free to use [this handy lookup tool](https://wolfgang.lol/code/include-mastodon-feed-wordpress-plugin/)

To look your ID up manually there are several ways.

As an instance admin you can easily read your user ID in the admin backend.

As regular user you can try an API v2 search to find your ID.

**API v2 notes:**
* Change `example.org` to your instance
* Replace `username` with your handle.

Use the following URL to get your ID:

`https://example.org/api/v2/search?q=username@example.org&resolve=false&limit=5`


= How does caching work? =

Server-side caching is disabled by default. When disabled every page load will trigger a new API request to your Mastodon instance for every single feed. This is how the public feeds API is intended and usually not a problem.

If you have a high-traffic site and want to help out your Mastodon instance you can enable caching globally or per shortcode. When enabled the plugin will cache the feed for 5 minutes as a default.

The plugin automatically uses any enabled cache plugin or the Wordpress internal transient cache (= Wordpress database). Only the statuses JSON response is cached - any media is still served from the Mastodon instance directly.

Note: If you Mastodon instance needs API authentication server-side caching is automatically enabled for all feeds that use authentication. That way your auth token is not exposed to your website visitors.


= API authentication =

If your Mastodon server needs API authentication you can use the `auth` parameter.

> NOTE
> Do NOT add your API auth token directly to the plugin short code
> 
> To avoid exposing the auth token to website visitors you have to take extra steps to
> set up authentication support
> 
> See the very end of [config-example.php](config-example.php) for an in-depth configuration example

**Steps to set up API authentication:**

1. Log into your Mastodon instance and go to Settings > Development (https://yourinstance.example.org/settings/applications)
2. Create a new Application (any name, only check one single scope `read:statuses`)
3. Add the `auth` mapping configuration to your `wp-config.php` (See very bottom of the included `config-example.php`)
4. Add your custom `auth` reference to your shortcode


= Known Issues / Todo =
* integrate i18n into translate.wordpress.org instead of text constants
* re-build plugin as custom gutenberg block

== Screenshots ==

* No screenshots

== Changelog ==

= 1.17.0 =
* feat: added server side caching (see included `config-example.php` for global CACHE and CACHE_DURATION settings1). `cache` can be set as short code param as well.
* feat: added API authentication support

= 1.16.0 =
* fix: local instance video urls
* feat: added audio media support
* feat: added excludeTags shortcode attribute - Exclude statuses that are tagged, posts containing any one of the given tags (comma separated list) will be excluded. Note: can lead to empty status list as the filtering is handled client-side. Mastodon API does not support this parameter natively. (thank you @zambunny)

= 1.15.1 =
* fix: added line break

= 1.15.0 =
* feat: now supports video attachments

= 1.14.0 =
* accessibility: add HTML lang attribute for even better screen reader support (thank you @oldrup@mastodon.green)

= 1.13.1 =
* fix: removed unnecessary, broken aria-label functionality

= 1.13 =
Special release for Global Accessibility Awareness Day
in collaboration with @oldrup@mastodon.green

Happy [Accesssibility Day](https://accessibility.day)

* accessibility (fix): image alt attributes - initial implementation was faulty
* accessibility: added alt text to image / gifv attachments
* accessibility: added alt text to avatar images
* accessibility: added alt text to preview card media
* accessibility: added descriptive aria-labels
* accessibility: increased default text / background color contrast
* accessibility: switched from DIV to semantic OL / LI structure

= 1.12 =
* accessibility: added image alt attribute (thank you @oldrup@mastodon.green)

= 1.11 =
* now favoring preview_url (smaler size) instead of remote_url (full size) for image previews (thank you @oldrup@mastodon.green)

= 1.10 =
* added image lazy loading for account and post images - post image lazy loading only works with preserveImageAspectRatio set to true (thank you @oldrup@mastodon.green)

= 1.9.11 =
* fixed typo (thank you @hjek)
* cleaned up code after 1.9.10 release

= 1.9.10 =
* fixed XSS vulnerability: removed support for date-options as shortcode attribute completely - to mitigate an XSS vulnerability where authenticated attackers with contributor permission could insert malicious JavaScript (still can be set as constant in PHP code)

= 1.9.9 =
* fixed esc_url context that previously broke the URL for the Mastodon API JS ajax request (thank you @beach@illo.social)

= 1.9.8 =
* fix broken date-locale and date-options parameters (thank you @crusy@chaos.social)
* improved string excaping for text parameters and added url escaping
* removed unnecessary output buffering
* fix license SPDX Identifier

= 1.9.7 =
* fix option to either display smaller image media attachment previews (default) or large image versions (thank you @beach@illo.social)

= 1.9.6 =
* fixed XSS vulnerability where authenticated attackers with contributor permissions could insert malicious JavaScript

= 1.9.5 =
* added option to either display smaller image media attachment previews (default) or large image versions (thank you @beach@illo.social)
* added option to point image media attachment links to either status (default) or image

= 1.9.4 =
* added option to hide status meta information and date/time (thank you @PaulKingtiger@dice.camp)
* added tag support - you can now embed tag feeds (thank you @martin@openedtech.social)
* added option to show embedded images in original aspect ratio (thank you @beach@illo.social)
* fix: correctly inject repeating emojis in display names and status texts (thank you @kanigsson@discuss.systems)

= 1.9.3 =
* fix: improved excludeConversationStarters detection (did not work correctly)
* fix: undid last refactor to load JS inline with markup instead footer to fix problem with JS that was added to footer even if shortcode was not visibly rendered

= 1.9.2 =
* fix: style for embedded videos / GIFs
* refactor: play gifv on mouseover
* refactor: load markup related javascript in footer instead of embedding it directly with the html markup

= 1.9.1 =
* refactor: show meaningful message if no statuses are available
* fix: broken excludeConversationStarters logic

= 1.9.0 =
* added option to exclude conversation starters (posts that start with a user mention)

= 1.8.1 =
* fix: boolean param validation was wonky
* fix: stop links from overflowing (thanks to https://github.com/moan0s for contributing)
* refactoring: improved styling of embedded images

= 1.8.0 =
* added option to show/hide preview cards
* refactoring: introducing plugin namespace
* refactoring: improved debug console output

= 1.7.0 =
* bumped tested wordpress version to 6.2
* added option to customize permalink text (before and after date/time)
* added option to customize text indicating edited posts

= 1.6.0 =
* image attachments are now clickable (link to original status)

= 1.5.0 =
* added option to show only statuses with specific tag
* added option to set link target to make links open in new tab
* added option to set maximum number of statuses

= 1.4.2 =
* fixed styling issue with emojis in account display name

= 1.4.1 =
* fixed styling issue with boosted account links

= 1.4.0 =
* removed "view on instance" link and made date info clickable instead
* added custom date locale and format option
* fixed emoji and inline link styling issues in content blocks
* fixed an issue with gifv media attachments
* refactored option sanitizing and filtering

= 1.3.1 =
* updated documentation that plugin constants for setting custom default options have to be defined in `wp-config.php`, as the previous config.php file gets removed with every automatic plugin update
* removed `config.php` support

= 1.3.0 =
* added new feed options: excludeReplies, onlyPinned, onlyMedia

= 1.2.0 =
* fixed broken JavaScript if post included media attachments other than images
* fixed custom "view on instance" option
* added option for custom content warning button text
* added option to exclude boosted statuses
* added support for gifv media attachments

= 1.1.0 =
* added support for more custom text overrides (loading, boosted, view on instnace)
* switched to showing static avatars, animated avatars only on hover

= 1.0.1 =
* escaped options when echoing them
* secured local file inclusion
* changed versioning to semantic versioning - now including patch number

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.8.1 =
* minor style updates - if you have custom styling make sure to check if all is good

= 1.3.1 =
* plugin constants for setting custom default options have to be defined in `wp-config.php`, as the previous config.php file gets removed with every automatic plugin update

= 1.1.0 =
* "loading" shortcode attribute was renamed to "text-loading"
