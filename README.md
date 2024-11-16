# Include Mastodon Feed Wordpress Plugin

Plugin that provides an `[include-mastodon-feed]` shortcode to easily integrate mastodon feeds into wordpress pages. Supports personal and tag feeds.

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

 | Attribute          | Default value                 | Example                   | Description                                                       |
| ------------------- | ----------------------------- | ------------------------- | ----------------------------------------------------------------- |
| **account**         |                               | id="012345678910"         | (required attribute) Your account ID ([a long number](#how-do-i-find-my-account-id))   |
| tag                 |                               | tag="travel"              | use **tag** instead of **account** if you want to embed a tag feed instead of a personal feed   |
| **instance**        |                               | instance="example.org"    | (required attribute) Domain name of the instance without https:// |
| limit               | 20                            | limit="10"                | Maximum number of statuses                                        |
| excludeBoosts       | false                         | excludeBoosts="true"      | Exclude boosted statuses                                          |
| excludeReplies      | false                         | excludeReplies="true"     | Exclude replies to other accounts                                 |
| excludeConversationStarters       | false           | excludeConversationStarters="true"      | Exclude statuses that start with a user mention     |
| onlyPinned          | false                         | onlyPinned="true"         | Show only pinned statuses                                         |
| onlyMedia           | false                         | onlyMedia="true"          | Show only statuses containing media                               |
| preserveImageAspectRatio          | false           | preserveImageAspectRatio="true"         | Preserve image aspect ratio                         |
| imageSize           | "preview"                     | imageSize="full"          | Load small sized preview images or full size high quality images  |
| imageLink           | "status"                      | imageLink="image"         | Link image to status or image                                     |
| tagged              | false                         | tagged="tagname"          | Show only statuses that are tagged with given tag name (no #!)    |
| linkTarget          | "_self"                       | linkTarget="_blank"       | Target for all links                                              |
| showPreviewCards    | true                          | showPreviewCards="false"  | Show preview cards                                                |
| hideStatusMeta      | false                         | hideStatusMeta="true"     | Hide status meta information (automatically also hides date and time) |
| hideDateTime        | false                         | hideDateTime="true"       | Hide date and time from status meta information                   |
| darkmode            | false                         | darkmode="true"           | Enable dark mode                                                  |
| text-loading        | "Loading Mastodon feed..."    | text-loading="Loading ⏳" | Loading text                                                      |
| text-noStatuses     | "No statuses available"       | text-noStatuses="💩"      | Text if no statuses are available                                 |
| text-boosted        | "boosted 🚀"                  | text-boosted="🚀"         | Boosted status indicator text                                     |
| text-viewOnInstance | "view on instance"            | text-viewOnInstance="🔗"  | View status on instance link text                                 |
| text-showContent    | "Show content"                | text-showContent="👀"     | Text for content warning buttons                                  |
| text-permalinkPre   | "on"                          | text-showContent="📅"     | Text before post permalink (date & time)                          |
| text-permalinkPost  | ""                            | text-showContent="📅"     | Text after post permalink (date & time)                           |
| text-edited         | "(edited)"                    | text-showContent="✏"      | Text indicating edited posts                                      |
| date-locale         | "en-US"                       | date-locale="de-DE"       | Locale for date string, used in toLocaleString()                  |
| date-options        | "{}"                          | date-options="{dateStyle: 'short', timeStyle: 'long'}" | Format options directly fed into toLocaleString() |

### Additional customizations

You can define several plugin constants to set custom default options that will be applied site-wide.

1. Open your `wp-config.php` file
2. Search for the line `/* Add any custom values between this line and the "stop editing" line. */`
3. Define the options you want to override between this line and `/* That's all, stop editing! Happy publishing. */`

See [config-example.php](config-example.php) for a full list of supported settings.

## Installation

The plugin is available through the official Wordpress plugin directory https://wordpress.org/plugins/include-mastodon-feed/

1. Log into your Wordpress installation
2. Go to "Plugins" and select "Add New"
3. Search for "Include Mastodon Feed"
4. Hit the "Install" button
5. After installation hit the "Activate" button

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
* integrate i18n into translate.wordpress.org instead of text constants
* re-build plugin as custom gutenberg block

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