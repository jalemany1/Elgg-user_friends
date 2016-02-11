<?php

$user = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
$options = [
	'type' => 'user',
	'relationship' => 'friendrequest',
	'relationship_guid' => (int) $user->guid,
	'inverse_relationship' => true,
	'offset_key' => 'offset_sent',
	'no_results' => elgg_echo('friend_request:received:none'),
	'pagination' => elgg_is_active_plugin('hypeLists') || !elgg_in_context('widgets'),
	'pagination_type' => 'default',
	'list_id' => "friend-requests-received-{$user->guid}",
	'item_view' => 'user/format/friend',
	'auto_refresh' => false,
	'friend' => $user,
	'data-selector-delete' => '.elgg-menu-friendship > li > a',
];
$content = elgg_list_entities_from_relationship($options);
echo elgg_view_module('info', elgg_echo('friend_request:received:title'), $content);
