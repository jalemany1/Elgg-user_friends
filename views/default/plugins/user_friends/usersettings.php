<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity \ElggPlugin */

$user = elgg_extract('user', $vars);
/* @var $user \ElggUser */

echo elgg_view_input('select', array(
	'name' => 'params[friend_list_visibility]',
	'value' => $entity->getUserSetting('friend_list_visibility', $user->guid, $entity->friend_list_visibility),
	'options_values' => array(
		ACCESS_PRIVATE => elgg_echo('user:friends:visibility:private'),
		ACCESS_FRIENDS => elgg_echo('user:friends:visibility:friends'),
		ACCESS_LOGGED_IN => elgg_echo('user:friends:visibility:logged_in'),
		ACCESS_PUBLIC => elgg_echo('user:friends:visibility:public'),
	),
	'label' => elgg_echo('user:friends:friend_list_visibility'),
	'help' => elgg_echo('user:friends:friend_list_visibility:help'),
));