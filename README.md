# Include Mastodon Feed Wordpress Plugin

Plugin that provides an `[include-mastodon-feed]` shortcode to easily integrate mastodon feeds into wordpress pages.

The plugin is written in PHP and generates native JavaScript to fetch and render the mastodon feed. No special libraries needed.

## Table of contents
* [Usage](#usage)
  * [Supported shortcode attributes](#supported-shortcode-attributes)
  * [Additional customization](#additional-customizations)
* [Installation](#installation)
  * [Installation via ZIP file](#installation-via-zip-file)
  * [Installation via git checkout](#installation-via-git-checkout)
* [Known issues](#known-issues)
  * [Todo](#todo)
* [FAQ](#faq)
  * [How do I find my account ID?](#how-do-i-find-my-account-id)
  * [Can I modify the plugin?](#can-i-modify-the-plugin)

## Usage

Place the following shortcode right into the page. Either as shortcode block or just copy and paste right within a text block:

```[include-mastodon-feed instance="YOUR-INSTANCE" account="YOUR-ACCOUNT-ID"]```

### Supported shortcode attributes

 | Attribute*         | Default value                 | Description                                                       |
| ------------------- | ----------------------------- | ----------------------------------------------------------------- |
| **account**         |                               | your account ID ([a long number](#how-do-i-find-my-account-id))   |
| **instance**        |                               | domain name of the instance (e.g. example.org)                    |
| excludeReplies      | false                         | exclude replies to other accounts                                 |
| excludeBoosts       | false                         | exclude boosted statuses                                          |
| onlyPinned          | false                         | Show only pinned statuses                                         |
| onlyMedia           | false                         | Show only statuses containing media                               |
| darkmode            | false                         | enable dark mode                                                  |
| text-loading        | "Loading Mastodon feed..."    | loading text                                                      |
| text-boosted        | "boosted 🚀"                  | boosted status indicator text                                     |
| text-viewOnInstance | "view on instance"            | view status on instance link text                                 |
| text-showContent    | "Show content"                | text for content warning buttons                                  |
| date-locale         | "en-US"                       | locale for date string, used in toLocaleString()                  |
| date-options        | "{}"                          | format options directly fed into toLocaleString()                 |

\* Attributes marked **bold** are required

### Additional customizations

You can define several plugin constants to set custom default options that will be applied site-wide.

1. Open your `wp-config.php` file
2. Search for the line `/* Add any custom values between this line and the "stop editing" line. */`
3. Define the options you want to override between this line and `/* That's all, stop editing! Happy publishing. */`

See [config-example.php](config-example.php) for a full list of supported settings.

## Installation

The plugin is not yet listed in the official Wordpress plugin directory

### Installation via ZIP file

1. Click on the `<>Code` in the top right of this page
2. Select `Download ZIP`
3. Create a `include-mastodon-feed` folder in your Wordpress plugins directory
4. Unpack all the files from the ZIP there (files only, no sub-directory)
5. Enable plugin in Wordpress
6. Use shortcode

### Installation via git checkout

If you are familiar with Github you can clone the repository right into your Wordpress plugins folder

1. SSH into your webserver
2. `cd /path/to/wordpress/wp-content/plugins`
3. `git clone https://github.com/wolfgang101/include-mastodon-feed.git`
4. Enable plugin in Wordpress
5. Use shortcode

## Known issues

### Todo

* improve support for video and audio media attachment types

## FAQ

### How do I find my account ID?
As an instance admin you can easily read your user ID in the admin backend. As regular user you can try an API v2 search to find your ID

Use the following URL to get your ID:

```https://example.org/api/v2/search?q=username@example.org&resolve=true&limit=5```

* Change `example.org` to your instance
* Replace `username` with your handle.
* Open the URL in your webbrowser

**Note:** You must be logged in to do that

### Can I modify the plugin?
The plugin is released unter the [Expat License](LICENSE) which is very permissive. Knock youself out!