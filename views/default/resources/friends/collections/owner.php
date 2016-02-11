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

$title = elgg_echo('friends:collections');
elgg_register_title_button('collections', 'add');

$content = elgg_view_access_collections(elgg_get_logged_in_user_guid());

$filter = elgg_view('filters/friends', array(
	'filter_context' => 'collections',
	'entity' => $user,
));

$body = elgg_view_layout('content', array(
	'filter' => $filter,
	'content' => $content,
	'title' => $title,
	'context' => 'collections',
));

echo elgg_view_page($title, $body);
