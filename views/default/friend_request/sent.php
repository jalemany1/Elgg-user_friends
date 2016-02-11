<?php

if (!elgg_get_plugin_setting('allow_revoke', 'user_friends', true)) {
	return;
}

$user = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
$options = [
	'type' => 'user',
	'relationship' => 'friendrequest',
	'relationship_guid' => (int) $user->guid,
	'inverse_relationship' => false,
	'offset_key' => 'offset_sent',
	'no_results' => elgg_echo('friend_request:sent:none'),
	'item_view' => 'user/format/friend',
	'friend' => $user,
];

$content = elgg_list_entities_from_relationship($options);
echo elgg_view_module('info', elgg_echo('friend_request:sent:title'), $content);