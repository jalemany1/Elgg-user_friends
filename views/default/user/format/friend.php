<?php

$size = elgg_extract('size', $vars, 'small');
$entity = elgg_extract('entity', $vars);

if (!$entity instanceof ElggUser) {
	return;
}

$subtitle = array();
$friend_count = $entity->getVolatileData('select:friend_count');
if (isset($friend_count)) {
	if (user_friends_can_view_friends($entity)) {
		$subtitle['friend_count'] = elgg_echo('user:friends:count', [$friend_count]);
	}
}

//$subtitle[] = elgg_echo('user:friends:member_since', [date('j M, Y', $entity->time_created)]);

if ($entity->last_action) {
	$subtitle['last_action'] = elgg_echo('user:friends:last_action', [elgg_get_friendly_time($entity->last_action)]);
}

$menu_params = $vars;
$menu_params['sort_by'] = 'priority';
$menu = elgg_view_menu('membership', $menu_params);

$metadata = '';
if (!elgg_in_context('widgets')) {
	$menu_params['class'] = 'elgg-menu-hz';
	$metadata = elgg_view_menu('entity', $menu_params);
}

$title = null;
$query = elgg_extract('query', $vars, get_input('query'));
if ($query && elgg_is_active_plugin('search')) {
	$name = search_get_highlighted_relevant_substrings($entity->getDisplayName(), $query);
	$username = search_get_highlighted_relevant_substrings(strtolower($entity->username), $query);
	$title = elgg_view('output/url', array(
		'href' => $entity->getURL(),
		'text' => "$name (<small>@$username</small>)",
	));
}

$subtitle = elgg_trigger_plugin_hook('subtitle', 'user', $vars, $subtitle);

$subtitle_str = '';
foreach ($subtitle as $s) {
	$subtitle_str .= elgg_format_element('span', ['class' => 'elgg-friend-subtitle-element'], $s);
}

if ($entity->briefdescription) {
	$view_subtitle = $subtitle_str . '<br />' . $entity->briefdescription;
} else {
	$view_subtitle = $subtitle_str;
}

$icon = elgg_view_entity_icon($entity, $size);
$summary = elgg_view('user/elements/summary', array(
	'entity' => $entity,
	'title' => $title,
	'metadata' => $metadata,
	'content' => $menu,
	'subtitle' => $view_subtitle,
		));

echo elgg_view_image_block($icon, $summary, array(
	'class' => 'elgg-user-friend',
));
