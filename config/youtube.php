<?php
function youtubeEmbed($url) {
    preg_match(
        '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([\w-]+)/',
        $url,
        $matches
    );
    return $matches[1] ?? null;
}
