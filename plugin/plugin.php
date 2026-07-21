<?php
/*
  Plugin Name: Include Mastodon Feed
	Plugin URI: https://wolfgang.lol/code/include-mastodon-feed-wordpress-plugin
	Description: Plugin providing [include-mastodon-feed] shortcode
	Version: 2.1.1
	Author: wolfgang.lol
	Author URI: https://wolfgang.lol
  License: MIT
  License URI: https://directory.fsf.org/wiki/License:Expat
*/

namespace IncludeMastodonFeedPlugin;

// set defaults
$constants = [
  [
    'key' => 'INCLUDE_MASTODON_FEED_DEBUG',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_CACHE',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_CACHE_DURATION',
    'value' => 300,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_DEFAULT_INSTANCE',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_LIMIT',
    'value' => 20,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_EXCLUDE_BOOSTS',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_EXCLUDE_REPLIES',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_EXCLUDE_CONVERSATIONSTARTERS',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_ONLY_PINNED',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_ONLY_MEDIA',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_PRESERVE_IMAGE_ASPECT_RATIO',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_IMAGE_SIZE',
    'value' => 'preview',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_IMAGE_LINK',
    'value' => 'status',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TAGGED',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_EXCLUDE_TAGS',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_LINKTARGET',
    'value' => '_self',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_SHOW_PREVIEWCARDS',
    'value' => true,
  ],

  // set styles
  [
    'key' => 'INCLUDE_MASTODON_FEED_DARKMODE',
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
    'value' => 'rgb(86, 58, 204)',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_STYLE_ACCENT_FONT_COLOR',
    'value' => 'rgb(255, 255, 255)',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_STYLE_BORDER_RADIUS',
    'value' => '0.25rem',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_HIDE_STATUS_META',
    'value' => false,
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_HIDE_DATETIME',
    'value' => false,
  ],
  // set texts and localization
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_LOADING',
    'value' => 'Loading Mastodon feed...',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_NO_STATUSES',
    'value' => 'No statuses available',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_BOOSTED',
    'value' => 'boosted 🚀',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_VIEW_ON_INSTANCE',
    'value' => 'view on instance',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_SHOW_CONTENT',
    'value' => 'Show content',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_PERMALINK_PRE',
    'value' => 'on',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_PERMALINK_POST',
    'value' => '',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_TEXT_EDITED',
    'value' => '(edited)',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_DATE_LOCALE',
    'value' => 'en-US',
  ],
  [
    'key' => 'INCLUDE_MASTODON_FEED_DATE_OPTIONS',
    'value' => "{}",
  ],
];
foreach($constants as $constant) {
  if(!defined($constant['key'])) {
      define($constant['key'], $constant['value']);
  }
}
unset($constants);

add_action('rest_api_init', function () {
    register_rest_route('include-mastodon-feed/v1', '/feed/', [
        'methods'  => 'GET',
        'callback' => __NAMESPACE__ . '\handle_feed_auth_request',
        'permission_callback' => '__return_true',
        'args'     => [
            'url' => [
                'required'          => true,
                'validate_callback' => function($param, $request, $key) {
                    return wp_http_validate_url($param);
                },
                'sanitize_callback' => 'sanitize_url',
            ],
            'auth' => [
                'required'          => false,
                'validate_callback' => function($param, $request, $key) {
                    return is_string($param);
                },
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ]);
});

function handle_feed_auth_request(\WP_REST_Request $request) {
    $auth = $request->get_param('auth');
    $url = $request->get_param('url');

    if (empty($url)) {
        return new \WP_Error('invalid_request', '"url" missing.', ['status' => 400]);
    }

    $cacheKey = 'include_mastodon_feed_' . md5($url);
    $options = [
      'Content-Type: application/json',
    ];
    if(!empty($auth)) {
      if(defined('INCLUDE_MASTODON_FEED_AUTH') && is_array(INCLUDE_MASTODON_FEED_AUTH) && isset(INCLUDE_MASTODON_FEED_AUTH[$auth])) {
        $authToken = sanitize_text_field(INCLUDE_MASTODON_FEED_AUTH[$auth]);
        $options[] = 'Authorization: Bearer ' . $authToken;
        $cacheKey .=  '_' . md5($auth.$authToken);
      }
      else {
        return new \WP_Error('invalid_request', 'Auth token for key not found: ' . $auth, ['status' => 403]);
      }
    }

    $response = cache_get($cacheKey);
    if(false === $response) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $options);
      $rawResponse = curl_exec($ch);
      $error = false;
      if (curl_errno($ch)) {
          $error = curl_error($ch);
      }
      curl_close($ch);

      if(false !== $error) {
        return new \WP_Error('curl_error', $error, ['status' => 500]);
      }

      try {
        $response = json_decode($rawResponse);
        cache_set($cacheKey, $response, INCLUDE_MASTODON_FEED_CACHE_DURATION);
      }
      catch(\Exception $e) {
        return new \WP_Error('json_error', $e->getMessage(), ['status' => 500]);
      }
    }
    return rest_ensure_response($response);
}

function cache_set($key, $data, $expiration = 300) {
  if (wp_using_ext_object_cache()) {
      wp_cache_set($key, $data, 'include_mastodon_feed', $expiration);
  } else {
      set_transient($key, $data, $expiration);
  }
}

function cache_get($key) {
  if (wp_using_ext_object_cache()) {
      $data = wp_cache_get($key, 'include_mastodon_feed');
      if ($data !== false) {
          return $data;
      }
  }
  $data = get_transient($key);
  if ($data !== false) {
      return $data;
  }
  return false;
}

function error($msg) {
  return '<strong>include-mastodon-feed: </strong> ' . $msg;
}


function gutenberg_block_render_callback($attributes) {
  // Convert camelCase attribute names to snake_case for compatibility with display_feed
  $atts = [
    'instance' => $attributes['instance'] ?? '',
    'account' => $attributes['account'] ?? '',
    'tag' => $attributes['tag'] ?? '',
    'limit' => $attributes['limit'] ?? INCLUDE_MASTODON_FEED_LIMIT,
    'tagged' => $attributes['tagged'] ?? INCLUDE_MASTODON_FEED_TAGGED,
    'excludeboosts' => $attributes['excludeBoosts'] ?? INCLUDE_MASTODON_FEED_EXCLUDE_BOOSTS,
    'excludereplies' => $attributes['excludeReplies'] ?? INCLUDE_MASTODON_FEED_EXCLUDE_REPLIES,
    'excludeconversationstarters' => $attributes['excludeConversationStarters'] ?? INCLUDE_MASTODON_FEED_EXCLUDE_CONVERSATIONSTARTERS,
    'onlypinned' => $attributes['onlyPinned'] ?? INCLUDE_MASTODON_FEED_ONLY_PINNED,
    'onlymedia' => $attributes['onlyMedia'] ?? INCLUDE_MASTODON_FEED_ONLY_MEDIA,
    'showpreviewcards' => $attributes['showPreviewCards'] ?? INCLUDE_MASTODON_FEED_SHOW_PREVIEWCARDS,
    'hidestatusmeta' => $attributes['hideStatusMeta'] ?? INCLUDE_MASTODON_FEED_HIDE_STATUS_META,
    'hidedatetime' => $attributes['hideDateTime'] ?? INCLUDE_MASTODON_FEED_HIDE_DATETIME,
    'darkmode' => $attributes['darkmode'] ?? INCLUDE_MASTODON_FEED_DARKMODE,
    'preserveimageaspectratio' => $attributes['preserveImageAspectRatio'] ?? INCLUDE_MASTODON_FEED_PRESERVE_IMAGE_ASPECT_RATIO,
    'imagesize' => $attributes['imageSize'] ?? INCLUDE_MASTODON_FEED_IMAGE_SIZE,
    'imagelink' => $attributes['imageLink'] ?? INCLUDE_MASTODON_FEED_IMAGE_LINK,
  ];
  
  return display_feed($atts);
}

function register_gutenberg_block() {

  register_block_type( __DIR__ .'/gutenberg/build', [
    'name' => 'include-mastodon-feed/gutenberg-block',
    'api_version' => 3,
    'attributes'      => [
      'gutenbergType'    => [
        'type'      => 'string',
      ],
      'instance'    => [
        'type'      => 'string',
      ],
      'account' => [
        'type'      => 'string',
      ],
      'tag' => [
        'type'      => 'string',
      ],
      'limit' => [
        'type'    => 'integer',
      ],
      'tagged' => [
        'type'      => 'string',
      ],
      'excludeBoosts' => [
        'type' => 'boolean',
      ],
      'excludeReplies' => [
        'type' => 'boolean',
      ],
      'excludeConversationStarters' => [
        'type' => 'boolean',
      ],
      'onlyPinned' => [
        'type' => 'boolean',
      ],
      'onlyMedia' => [
        'type' => 'boolean',
      ],
      'showPreviewCards' => [
        'type' => 'boolean',
      ],
      'hideStatusMeta' => [
        'type' => 'boolean',
      ],
      'hideDateTime' => [
        'type' => 'boolean',
      ],
      'darkmode' => [
        'type' => 'boolean',
      ],
      'preserveImageAspectRatio' => [
        'type' => 'boolean',
      ],
      'imageSize' => [
        'type' => 'string',
      ],
      'imageLink' => [
        'type' => 'string',
      ],
    ],
    'render_callback' => __NAMESPACE__ . '\gutenberg_block_render_callback'
  ] );

}
add_action( 'init', __NAMESPACE__ . '\register_gutenberg_block' );

function register_assets() {
  $cssPath = __DIR__ . '/assets/include-mastodon-feed.css';
  $jsPath  = __DIR__ . '/assets/include-mastodon-feed.js';
  $cssVer  = file_exists($cssPath) ? filemtime($cssPath) : false;
  $jsVer   = file_exists($jsPath) ? filemtime($jsPath) : false;

  wp_register_style(
    'include-mastodon-feed',
    plugins_url('assets/include-mastodon-feed.css', __FILE__),
    [],
    $cssVer
  );

  // dynamic style variables, attached to the registered stylesheet
  $inlineVars = ':root {'
    . '--include-mastodon-feed-bg-light: ' . filter_var( INCLUDE_MASTODON_FEED_STYLE_BG_LIGHT_COLOR, FILTER_UNSAFE_RAW ) . ';'
    . '--include-mastodon-feed-bg-dark: ' . filter_var( INCLUDE_MASTODON_FEED_STYLE_BG_DARK_COLOR, FILTER_UNSAFE_RAW ) . ';'
    . '--include-mastodon-feed-accent-color: ' . filter_var( INCLUDE_MASTODON_FEED_STYLE_ACCENT_COLOR, FILTER_UNSAFE_RAW ) . ';'
    . '--include-mastodon-feed-accent-font-color: ' . filter_var( INCLUDE_MASTODON_FEED_STYLE_ACCENT_FONT_COLOR, FILTER_UNSAFE_RAW ) . ';'
    . '--include-mastodon-feed-border-radius: ' . filter_var( INCLUDE_MASTODON_FEED_STYLE_BORDER_RADIUS, FILTER_UNSAFE_RAW ) . ';'
    . '}';
  wp_add_inline_style('include-mastodon-feed', $inlineVars);

  wp_register_script(
    'include-mastodon-feed',
    plugins_url('assets/include-mastodon-feed.js', __FILE__),
    [],
    $jsVer,
    true
  );

  if(true === INCLUDE_MASTODON_FEED_DEBUG) {
    wp_add_inline_script('include-mastodon-feed', 'window.includeMastodonFeedDebug = true;', 'before');
  }
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\register_assets');

function display_feed($atts) {
  $atts = shortcode_atts(
      array(
          'instance' => ( INCLUDE_MASTODON_FEED_DEFAULT_INSTANCE === false ? false : filter_var( INCLUDE_MASTODON_FEED_DEFAULT_INSTANCE, FILTER_UNSAFE_RAW ) ),
					'account' => false,
          'tag' => false,
          'cache' => filter_var(esc_html(INCLUDE_MASTODON_FEED_CACHE), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'auth' => false,
          'limit' => INCLUDE_MASTODON_FEED_LIMIT,
          'excludeboosts' => filter_var(esc_html(INCLUDE_MASTODON_FEED_EXCLUDE_BOOSTS), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'excludereplies' => filter_var(esc_html(INCLUDE_MASTODON_FEED_EXCLUDE_REPLIES), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'excludeconversationstarters' => filter_var(esc_html(INCLUDE_MASTODON_FEED_EXCLUDE_CONVERSATIONSTARTERS), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'onlypinned' => filter_var(esc_html(INCLUDE_MASTODON_FEED_ONLY_PINNED), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'onlymedia' => filter_var(esc_html(INCLUDE_MASTODON_FEED_ONLY_MEDIA), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'preserveimageaspectratio' => filter_var(esc_html(INCLUDE_MASTODON_FEED_PRESERVE_IMAGE_ASPECT_RATIO), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'imagesize' => INCLUDE_MASTODON_FEED_IMAGE_SIZE,
          'imagelink' => INCLUDE_MASTODON_FEED_IMAGE_LINK,
          'tagged' => INCLUDE_MASTODON_FEED_TAGGED,
          'excludetags' => INCLUDE_MASTODON_FEED_EXCLUDE_TAGS,
          'linktarget' => INCLUDE_MASTODON_FEED_LINKTARGET,
          'showpreviewcards' => filter_var(esc_html(INCLUDE_MASTODON_FEED_SHOW_PREVIEWCARDS), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'hidestatusmeta' => filter_var(esc_html(INCLUDE_MASTODON_FEED_HIDE_STATUS_META), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'hidedatetime' => filter_var(esc_html(INCLUDE_MASTODON_FEED_HIDE_DATETIME), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
          'text-loading' => INCLUDE_MASTODON_FEED_TEXT_LOADING,
          'text-nostatuses' => INCLUDE_MASTODON_FEED_TEXT_NO_STATUSES,
          'text-boosted' => INCLUDE_MASTODON_FEED_TEXT_BOOSTED,
          'text-viewoninstance' => INCLUDE_MASTODON_FEED_TEXT_VIEW_ON_INSTANCE,
          'text-showcontent' => INCLUDE_MASTODON_FEED_TEXT_SHOW_CONTENT,
          'text-permalinkpre' => INCLUDE_MASTODON_FEED_TEXT_PERMALINK_PRE,
          'text-permalinkpost' => INCLUDE_MASTODON_FEED_TEXT_PERMALINK_POST,
          'text-edited' => INCLUDE_MASTODON_FEED_TEXT_EDITED,
          'date-locale' => INCLUDE_MASTODON_FEED_DATE_LOCALE,
          'darkmode' => filter_var(esc_html(INCLUDE_MASTODON_FEED_DARKMODE), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
      ), array_change_key_case($atts, CASE_LOWER)
  );

  $atts['cache'] = filter_var( $atts['cache'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? true : false;

  if(false === filter_var($atts['instance'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    return error('missing configuration (instance)');
  }
  
  $feedType = null;
  if(false !== filter_var($atts['account'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $feedType = 'account';
  }
  else if(false !== filter_var($atts['tag'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $feedType = 'tag';
  }

  $isAuth = false;
  if(false !== filter_var($atts['auth'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    if(defined('INCLUDE_MASTODON_FEED_AUTH')) {
      if(is_array(INCLUDE_MASTODON_FEED_AUTH) && isset(INCLUDE_MASTODON_FEED_AUTH[$atts['auth']])) {
        $isAuth = true;
        if (!function_exists('curl_init')) {
          return 'webserver does not suppport PHP CURL - it is needed when using auth';
        }
      }
      else {
        return error('missing configuration (auth reference "' . sanitize_text_field($atts['auth']) . '")');
      }
    }
    else {
      return error('missing configuration (auth)');
    }
  }

  if(null === $feedType) {
    return error($feedType . 'missing configuration (account id or tag)');
  }

  if('account' === $feedType) {
    $apiUrl = 'https://'.urlencode($atts['instance']).'/api/v1/accounts/'.$atts['account'].'/statuses';
  }
  else if('tag' === $feedType) {
    $apiUrl = 'https://'.urlencode($atts['instance']).'/api/v1/timelines/tag/'.urlencode($atts['tag']);
  }
  else {
    return error('unsupported type: ' . sanitize_text_field($feedType));
  }

  $getParams = [];
  if($atts['limit'] != 20 && $atts['limit'] > 0) {
    $getParams[] = 'limit=' . filter_var( $atts['limit'], FILTER_SANITIZE_NUMBER_INT );
  }
  if(false !== filter_var($atts['excludeboosts'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'exclude_reblogs=true';
  }
  if(false !== filter_var($atts['excludereplies'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'exclude_replies=true';
  }
  if(true === filter_var($atts['onlypinned'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'pinned=true';
  }
  if(true === filter_var($atts['onlymedia'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'only_media=true';
  }
  if(false !== filter_var($atts['tagged'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
    $getParams[] = 'tagged=' . filter_var( $atts['tagged'], FILTER_UNSAFE_RAW );
  }
  if(sizeof($getParams) > 0) {
    $apiUrl .= '?' . implode('&', $getParams);
  }
  if(true === $isAuth) {
    $apiUrl = get_rest_url() . 'include-mastodon-feed/v1/feed?auth=' . urlencode($atts['auth']) . '&url=' . urlencode($apiUrl);
  }
  else if(true === $atts['cache']) {
    $apiUrl = get_rest_url() . 'include-mastodon-feed/v1/feed?url=' . urlencode($apiUrl);
  }

  $apiUrl = esc_url( $apiUrl, ['http', 'https'], 'apicall' );
  if(empty($apiUrl)) {
    return 'only http and https urls supported';
  }

  // load the global stylesheet and script only when a feed is actually output
  wp_enqueue_style('include-mastodon-feed');
  wp_enqueue_script('include-mastodon-feed');

  $elemId = uniqid('include-mastodon-feed-');
  ob_start();
?>
  <script>
    window.addEventListener("load", () => {
      mastodonFeedLoad(
        "<?php echo $apiUrl; ?>",
        "<?php echo filter_var( $elemId, FILTER_UNSAFE_RAW ); ?>",
        {
          linkTarget: "<?php echo esc_js(filter_var( $atts['linktarget'], FILTER_UNSAFE_RAW )); ?>",
          showPreviewCards: <?php echo (filter_var( $atts['showpreviewcards'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>,
          excludeConversationStarters: <?php echo (filter_var( $atts['excludeconversationstarters'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>,
          excludeTags: "<?php echo esc_html( $atts['excludetags'] ); ?>",
          content: {
            hideStatusMeta: <?php echo (filter_var( $atts['hidestatusmeta'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>,
            hideDateTime: <?php echo (filter_var( $atts['hidedatetime'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>
          },
          images: {
            preserveImageAspectRatio: <?php echo (filter_var( $atts['preserveimageaspectratio'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? "true" : "false"); ?>,
            size: "<?php echo ( "full" === $atts['imagesize'] ? "full" : "preview" ); ?>",
            link: "<?php echo ( "image" === $atts['imagelink'] ? "image" : "status" ); ?>",
          },
          text: {
            boosted: "<?php echo esc_html( $atts['text-boosted'] ); ?>",
            noStatuses: "<?php echo esc_html( $atts['text-nostatuses'] ); ?>",
            viewOnInstance: "<?php echo esc_js( $atts['text-viewoninstance'] ); ?>",
            showContent: "<?php echo esc_html( $atts['text-showcontent'] ); ?>",
            permalinkPre: "<?php echo esc_html( $atts['text-permalinkpre'] ); ?>",
            permalinkPost: "<?php echo esc_html( $atts['text-permalinkpost'] ); ?>",
            edited: "<?php echo esc_html( $atts['text-edited'] ); ?>",
          },
          localization: {
            date: {
              locale: "<?php echo esc_js( filter_var( $atts['date-locale'], FILTER_UNSAFE_RAW ) ); ?>",
              options: <?php echo filter_var( INCLUDE_MASTODON_FEED_DATE_OPTIONS, FILTER_UNSAFE_RAW ); ?>,
            }
          }
        }
      );
    });
  </script>
  <div class="include-mastodon-feed-wrapper"><ol class="include-mastodon-feed<?php echo (true == $atts['darkmode'] ? ' dark' : ''); ?>" id="<?php echo esc_attr( $elemId ); ?>"><li><?php echo esc_html( $atts['text-loading'] ); ?></li></ol></div>
<?php
  return ob_get_clean();
}
add_shortcode('include-mastodon-feed', __NAMESPACE__ . '\display_feed');
