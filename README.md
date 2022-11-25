# mastodon-feed Wordpress plugin

Plugin that provides a `[mastodon-feed]` shortcode to easily integrate mastodon feeds into wordpress pages.

The plugin is written in PHP and generates native JavaScript to fetch and render the mastodon feed. No special libraries needed.

## Table of contents
* [Usage](#usage)
  * [Supported shortcode attributes](#supported-shortcode-attributes)
  * [Additional customization](#additional-customizations)
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
| loading       | "Loading mastodon feed..."    | loading text                                                      |
| darkmode      | false                         | enable dark mode                                                  |

\* Attributes marked **bold** are required

### Additional customizations

You can create a file config.local.php to overrwrite select settings

See [config.local-example.php](config.local-example.php) for a full list of supported settings

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