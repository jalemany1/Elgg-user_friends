<?php

$friend_guid = (int) get_input('guid');
$friend = get_user($friend_guid);
if (empty($friend)) {
	register_error(elgg_echo('error:missing_data'));
	forward(REFERER);
}

$user = elgg_get_logged_in_user_entity();

if (user_friends_approve_friend_request($user, $friend)) {
	system_message(elgg_echo('friend_request:approve:successful', [$friend->getDisplayName()]));
} else {
	register_error(elgg_echo('friend_request:approve:fail', [$friend->getDisplayName()]));
}

forward(REFERER);
