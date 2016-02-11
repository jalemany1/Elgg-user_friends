<?php

$a = get_input('a');
$u = (int) get_input('u');
$f = (int) get_input('f');

$hmac = elgg_build_hmac(array(
	'a' => $a,
	'u' => $u,
	'f' => $f,
		));

if (!$hmac->matchesToken(get_input('m'))) {
	register_error(elgg_echo('user:friends:confirm_error'));
	forward('', '403');
}

$ia = elgg_set_ignore_access(true);

$page_owner = elgg_extract('entity', $vars, elgg_get_page_owner_entity());

$friend = get_entity($u); // user who requested friendship
$user = get_entity($f); // user who was requested

if (!$user || !$friend) {
	register_error(elgg_echo('user:friends:confirm_error'));
	forward('', '403');
}

switch ($a) {
	case 'decline' :
		if (user_friends_decline_friend_request($user, $friend)) {
			system_message(elgg_echo('friend_request:decline:success'));
		} else {
			register_error(elgg_echo('friend_request:decline:fail'));
		}
		break;

	case 'approve' :
		if (user_friends_approve_friend_request($user, $friend)) {
			system_message(elgg_echo('friend_request:approve:successful', [$friend->getDisplayName()]));
		} else {
			register_error(elgg_echo('friend_request:approve:fail', [$friend->getDisplayName()]));
		}
		break;
}

forward("friends/$friend->username");
