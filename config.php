<?php

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