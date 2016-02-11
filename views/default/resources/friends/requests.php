<?php

$user = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
if (!$user) {
	forward('', '404');
}

if (!$user->canEdit()) {
	forward('', '403');
}

elgg_push_breadcrumb(elgg_echo('friends'), "friends");
elgg_push_breadcrumb($user->getDisplayName(), "friends/{$user->username}");

$title = elgg_echo('friend_request:title', array($user->getDisplayName()));
$filter = elgg_view('filters/friends', array(
	'filter_context' => 'requests',
	'entity' => $user,
));

$content = elgg_view('friend_request/received');
$content .= elgg_view('friend_request/sent');

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => $filter,
));

echo elgg_view_page($title, $layout);