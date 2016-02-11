<?php

if (!elgg_is_active_plugin('invitefriends')) {
	return;
}

if (!elgg_get_config('allow_registration')) {
	return;
}

$user = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
if (!$user) {
	forward('', '404');
}

if (!$user->canEdit()) {
	forward('', '403');
}


elgg_push_breadcrumb(elgg_echo('friends'), "friends");
elgg_push_breadcrumb($user->getDisplayName(), "friends/{$user->username}");

$title = elgg_echo('friends:invite');

$filter = elgg_view('filters/friends', array(
	'filter_context' => 'invite',
	'entity' => $user,
		));

$content = elgg_view('invitefriends/form');

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => $filter,
		));

echo elgg_view_page($title, $layout);
