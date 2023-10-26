<?php

$view = $view ?? [];
$classes = $view['classes'] ?? '';
$content = $view['content'] ?? '';
$id = $view['id'] ?? '';
$bemName = $view['bemName'] ?? '';

$newLine = "\r\n";
$classes = $classes ?
    ' ' . $classes :
    '';

printf(
    "<div class=\"{{ _view.classes }}%s %s--id--{{ _view.id }} %s--object-id--{{ _view.object_id }}\">",
    esc_html($bemName),
    esc_html($bemName),
    esc_html($bemName)
);
echo esc_html($newLine);
// no escaping for $content, because it's an HTML code (of other things, that have escaped variables)
echo $content;
echo "</div>";
echo esc_html($newLine);

