# mastodon-feed Wordpress plugin

Plugin that provides a `[mastodon-feed]` shortcode to easily integrate mastodon feeds into wordpress pages.

The plugin is written in PHP and generates native JavaScript to fetch and render the mastodon feed. No special libraries needed.

## Table of contents
* [Usage](#usage)
  * [Supported shortcode attributes](#supported-shortcode-attributes)
  * [Additional customization](#additional-customizations)
* [Installation](#installation)
  * [Installation via ZIP file](#installation-via-zip-file)
  * [Installation via git checkout](#installation-via-git-checkout)
* [Known issues](#known-issues)
  * [TODO](#todo)
* [FAQ](#faq)
  * [How do I find my account ID?](#how-do-i-find-my-account-id)
  * [Can I modify the plugin?](#can-i-modify-the-plugin)

## Usage

Place the following shortcode right into the page. Either as shortcode block or just copy and paste right within a text block:

```[mastodon-feed account="YOUR-ACCOUNT-ID"]```

### Supported shortcode attributes

 | Attribute*   | Default value                 | Description                                                       |
| ------------- | ----------------------------- | ----------------------------------------------------------------- |
| **account**   |                               | your account ID ([a long number](#how-do-i-find-my-account-id))   |
| instance      | "mastodon.social"             | domain name of the instance                                       |
| loading       | "Loading Mastodon feed..."    | loading text                                                      |
| darkmode      | false                         | enable dark mode                                                  |

\* Attributes marked **bold** are required

### Additional customizations

You can create a file config.php to overrwrite select settings

See [config-example.php](config-example.php) for a full list of supported settings

## Installation

The plugin is not yet listed in the official Wordpress plugin directory

### Installation via ZIP file

1. Click on the `<>Code` in the top right of this page
2. Select `Download ZIP`
3. Create a `mastodon-feed` folder in your Wordpress plugins directory
4. Unpack all the files from the ZIP there (files only, no sub-directory)
5. Enable plugin in Wordpress
6. Use shortcode

### Installation via git checkout

If you are familiar with Github you can clone the repository right into your Wordpress plugins folder

1. SSH into your webserver
2. `cd /path/to/wordpress/wp-content/plugins`
3. `git clone https://github.com/wolfgang101/mastodon-feed.git`
4. Enable plugin in Wordpress
5. Use shortcode

## Known issues

### TODO

* support custom emojis in account names
* support custom emojis in post content
* support additional media attachment types (currently only images supported)

## FAQ

### How do I find my account ID?
As an instance admin you can easily read your user ID in the admin backend. As regular user you can try an API v2 search to find your ID

Use the following URL to get your ID:

```https://instance.tld/api/v2/search?q=username@instance.tld&resolve=true&limit=5```

* Change `instance.tld` to your instance
* Replace `username` with your handle.
* Open the URL in your webbrowser

**Note:** You must be logged in to do that

### Can I modify the plugin?
The plugin is released unter the [MIT License](LICENSE) which is very permissive. Knock youself out!