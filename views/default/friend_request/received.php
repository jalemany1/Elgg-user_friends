<?php

$user = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
$options = [
	'type' => 'user',
	'relationship' => 'friendrequest',
	'relationship_guid' => (int) $user->guid,
	'inverse_relationship' => true,
	'offset_key' => 'offset_sent',
	'no_results' => elgg_echo('friend_request:received:none'),
	'item_view' => 'user/format/friend',
	'friend' => $user,
];
$content = elgg_list_entities_from_relationship($options);
echo elgg_view_module('info', elgg_echo('friend_request:received:title'), $content);