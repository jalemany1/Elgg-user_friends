<?php

$user = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
if (!$user) {
	forward('', '404');
}

if (!user_friends_can_view_friends($user)) {
	register_error(elgg_echo('user:friends:no_access'));
	forward('friends');
}

elgg_push_breadcrumb(elgg_echo('friends'), 'friends');
elgg_push_breadcrumb($user->getDisplayName(), "friends/{$user->username}");

if ($user->guid == elgg_get_logged_in_user_guid()) {
	$title = elgg_echo('friends:yours');
} else {
	$title = elgg_echo('friends:owned', array($user->getDisplayName()));
}

$filter = elgg_view('filters/friends', array(
	'filter_context' => 'index',
	'entity' => $user,
		));
$content = elgg_view('lists/friends', array(
	'entity' => $user,
		));

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => $filter,
		));

echo elgg_view_page($title, $layout);
