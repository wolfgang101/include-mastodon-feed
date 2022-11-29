=== Include Mastodon Feed ===
Contributors: wolfgang101
Donate link: https://wolfgang.lol/
Tags: mastodon, status, feed
Requires at least: 6.0
Tested up to: 6.1.1
Requires PHP: 7.4
Stable tag: 1.0
License: Expat License
License URI: https://directory.fsf.org/wiki/License:Expat

Plugin that provides a shortcode to easily integrate mastodon feeds into wordpress pages.

== Description ==
Plugin that provides an `[include-mastodon-feed]` shortcode to easily integrate mastodon feeds into wordpress pages.

The plugin is written in PHP and generates native JavaScript to fetch and render the mastodon feed. No special libraries needed.

== Installation ==

1. Upload the "include-mastodon-feed"  directory to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Insert shortcode into any page.

= Shortcode example =
`[include-mastodon-feed instance="YOUR-INSTANCE" account="YOUR-ACCOUNT-ID"]`

= Shortcode attributes =
* **account** (required)
The account ID (a long number - see FAQ on how to get it)

* **instance** (required)
Domain name of the instance (e.g. example.org)

* **loading**
 Loading text (Default: Loading Mastodon feed...)

* **darkmode**
Enable dark mode (Default: false)

= Additional customizations =

You can create a file `config.php` to overrwrite select settings

See the included `config-example.php` file for a full list of supported settings


== Frequently Asked Questions ==

= How do I find my account ID? =
As an instance admin you can easily read your user ID in the admin backend.

As regular user you can try an API v2 search to find your ID.

**API v2 notes:**
* You might have to be logged in to get any results
* Change `example.org` to your instance
* Replace `username` with your handle.

Use the following URL to get your ID:

`https://example.org/api/v2/search?q=username@example.org&resolve=true&limit=5`

= Known Issues =
* TODO: support additional media attachment types (currently only images supported)

== Screenshots ==

* No screenshots

== Changelog ==

= 1.0 =
* Initial release