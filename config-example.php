<?php

    /*
     * NOTE
     * 
     * None, any, or all constants can be defined in the
     * config.local.php file to override default settings
     * 
     * default settings apply if file does not exist
     */

    /*
     *  DEFAULT SETTINGS
     */

    // enable/discable JavaScript console debug output
    define('INCLUDE_MASTODON_FEED_DEBUG', false);

    // set a default instance
    // can still be overriden in shortcode
    // plugin will show a warning if no default is set and instance is omitted in shortcode
    //define('INCLUDE_MASTODON_FEED_DEFAULT_INSTANCE', 'example.org');

    // enable dark mode
    define('INCLUDE_MASTODON_FEED_DARKMODE', false);

    // exclude boosts from statuses
    define('INCLUDE_MASTODON_FEED_EXCLUDE_BOOSTS', false);

    // exclude replies to other accounts from statuses
    define('INCLUDE_MASTODON_FEED_EXCLUDE_REPLIES', false);

    // show only pinned statuses
    define('INCLUDE_MASTODON_FEED_ONLY_PINNED', false);

    // show only statuses containing media
    define('INCLUDE_MASTODON_FEED_ONLY_MEDIA', false);

    // only tagged statuses
    // tag name without leading #, case insensitive
    define('INCLUDE_MASTODON_FEED_TAGGED', 'tagname');

    /*
     *  DEFAULT STYLES
     */

    // define background color in light mode
    // (light mode default)
    define('INCLUDE_MASTODON_FEED_STYLE_BG_LIGHT_COLOR', 'rgba(100, 100, 100, 0.15)');

    // define background color in dark mode
    // (dark mode can be set in shortcode)
    define('INCLUDE_MASTODON_FEED_STYLE_BG_DARK_COLOR', 'rgba(155, 155, 155, 0.15)');

    // main accent color
    // (highlighting - button backgrounds and link colors)
    define('INCLUDE_MASTODON_FEED_STYLE_ACCENT_COLOR', 'rgb(99, 100, 255)');

    // font color to use with accent color background
    define('INCLUDE_MASTODON_FEED_STYLE_ACCENT_FONT_COLOR', 'rgb(255, 255, 255)');

    // general border radius on elements
    define('INCLUDE_MASTODON_FEED_STYLE_BORDER_RADIUS', '0.25rem',);


    /*
     *  DEFAULT TEXTS AND LOCALIZATION
     */

    // default loading text
    // can be overridden in shortcode
    define('INCLUDE_MASTODON_FEED_TEXT_LOADING', 'Loading Mastodon feed...',);

    // default "boosted" indicator
    // can be overridden in shortcode
    define('INCLUDE_MASTODON_FEED_TEXT_BOOSTED', 'boosted 🚀',);

    // default view status on instance link text
    // can be overridden in shortcode
    define('INCLUDE_MASTODON_FEED_TEXT_VIEW_ON_INSTANCE', 'view on instance',);

    // default text for content warning buttons
    define('INCLUDE_MASTODON_FEED_TEXT_SHOW_CONTENT', 'Show content');
    
    // locale for date string, used in toLocaleString()
    // see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/toLocaleString
    define('INCLUDE_MASTODON_FEED_DATE_LOCALE', 'en-US');

    // format options directly fed into toLocaleString()
    // see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/toLocaleString
    define('INCLUDE_MASTODON_FEED_DATE_OPTIONS', '{}');
