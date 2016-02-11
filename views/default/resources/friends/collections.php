<?php

if (!elgg_get_plugin_setting('show_collections', 'user_friends', true)) {
	return;
}

$segments = (array) elgg_extract('segments', $vars, array());
$section = array_shift($segments);

if (!$section) {
	$section = 'owner';
}

echo elgg_view_resource("friends/collections/$section", $vars);
