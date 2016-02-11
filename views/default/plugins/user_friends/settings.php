<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_input('select', array(
	'name' => 'params[friend_list_visibility]',
	'value' => isset($entity->friend_list_visibility) ? $entity->friend_list_visibility : ACCESS_PUBLIC,
	'options_values' => array(
		ACCESS_PRIVATE => elgg_echo('user:friends:visibility:private'),
		ACCESS_FRIENDS => elgg_echo('user:friends:visibility:friends'),
		ACCESS_LOGGED_IN => elgg_echo('user:friends:visibility:logged_in'),
		ACCESS_PUBLIC => elgg_echo('user:friends:visibility:public'),
	),
	'label' => elgg_echo('user:friends:friend_list_visibility'),
	'help' => elgg_echo('user:friends:friend_list_visibility:help'),
));

echo elgg_view_input('select', array(
	'name' => 'params[show_collections]',
	'value' => isset($entity->show_collections) ? $entity->show_collections : true,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
	'label' => elgg_echo('user:friends:show_collections'),
	'help' => elgg_echo('user:friends:show_collections:help'),
));

echo elgg_view_input('select', array(
	'name' => 'params[allow_revoke]',
	'value' => isset($entity->allow_revoke) ? $entity->allow_revoke : true,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
	'label' => elgg_echo('user:friends:allow_revoke'),
	'help' => elgg_echo('user:friends:allow_revoke:help'),
));
