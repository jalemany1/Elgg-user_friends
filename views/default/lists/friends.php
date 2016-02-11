<?php

$entity = elgg_extract('entity', $vars);
$guid = (int) $entity->guid;

$base_url = elgg_normalize_url("friends/$entity->username") . '?' . parse_url(current_page_url(), PHP_URL_QUERY);

$list_class = (array) elgg_extract('list_class', $vars, array());
$list_class[] = 'elgg-list-friends';

$item_class = (array) elgg_extract('item_class', $vars, array());
$item_class[] = 'elgg-friend';

$options = (array) elgg_extract('options', $vars, array());

$list_options = array(
	'full_view' => true,
	'limit' => elgg_extract('limit', $vars, elgg_get_config('default_limit')) ? : 10,
	'list_class' => implode(' ', $list_class),
	'item_class' => implode(' ', $item_class),
	'no_results' => elgg_echo('friends:none'),
	'pagination' => elgg_is_active_plugin('hypeLists') || !elgg_in_context('widgets'),
	'pagination_type' => 'default',
	'base_url' => $base_url,
	'list_id' => "friends-$guid",
	'item_view' => 'user/format/friend',
	'auto_refresh' => false,
	'friend' => $entity,
	'data-selector-delete' => '.elgg-menu-friendship > li > a',
);

$getter_options = array(
	'types' => array('user'),
);

$options = array_merge_recursive($list_options, $options, $getter_options);

$dbprefix = elgg_get_config('dbprefix');
$options['wheres']['friend_relationship'] = "EXISTS (SELECT 1 FROM {$dbprefix}entity_relationships AS friend_relationship
	WHERE friend_relationship.guid_one = $guid AND friend_relationship.relationship = 'friend' AND friend_relationship.guid_two = e.guid)";

if (elgg_view_exists('lists/users')) {
	echo elgg_view('lists/users', array(
		'options' => $options,
		'callback' => 'elgg_list_entities',
	));
} else {
	$dbprefix = elgg_get_config('dbprefix');
	$options['joins'][] = "JOIN {$dbprefix}users_entity ue ON ue.guid = e.guid";
	$options['order_by'] = 'ue.name ASC';
	
	echo elgg_list_entities_from_relationship($options);
}