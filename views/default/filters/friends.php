<?php

$entity = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
$filter_context = elgg_extract('filter_context', $vars, 'user');

$tabs = [
	'index' => "friends/$entity->username",
];


if ($entity->canEdit()) {
	$tabs['requests'] = "friends/$entity->username/requests";
	if (elgg_get_plugin_setting('show_collections', 'user_friends', true)) {
		$tabs['collections'] = "friends/$entity->username/collections";
	}
	if (elgg_is_active_plugin('invitefriends')) {
		$tabs['invite'] = "friends/$entity->username/invite";
	}
}

foreach ($tabs as $tab => $url) {
	elgg_register_menu_item('filter', array(
		'name' => "user:friends:$tab",
		'text' => elgg_echo("user:friends:$tab"),
		'href' => elgg_normalize_url($url),
		'selected' => $tab == $filter_context,
	));
}

echo elgg_view_menu('filter', array(
	'sort_by' => 'priority',
	'entity' => $entity,
	'filter_context' => $filter_context,
));
