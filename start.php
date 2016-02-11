<?php

/**
 * User Friends
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2015, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'user_friends_init');

/**
 * Initialize the plugin
 * @return void
 */
function user_friends_init() {

	elgg_extend_view('elgg.css', 'user/format/friend.css');

	elgg_register_page_handler('friends', 'user_friends_page_handler');

	elgg_register_plugin_hook_handler('route', 'friend_request', 'user_friends_route_friend_request');
	elgg_register_plugin_hook_handler('route', 'collections', 'user_friends_route_collections');

	elgg_unregister_plugin_hook_handler('register', 'menu:page', '\ColdTrick\FriendRequest\PageMenu::register');
	elgg_unregister_plugin_hook_handler('register', 'menu:topbar', '\ColdTrick\FriendRequest\TopbarMenu::register');
	elgg_unregister_event_handler('pagesetup', 'system', '_elgg_friends_page_setup');
	elgg_unregister_event_handler('pagesetup', 'system', '_elgg_setup_collections_menu');

	elgg_register_plugin_hook_handler('register', 'menu:friendship', 'user_friends_friendship_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:topbar', 'user_friends_topbar_menu_setup');

	if (elgg_is_active_plugin('invitefriends')) {
		elgg_register_plugin_hook_handler('route', 'invite', 'user_friends_route_invite');
		elgg_unregister_menu_item('page', 'invite');
	}

	elgg_register_plugin_hook_handler('view', 'widgets/friends/content', 'user_friends_friends_widget_access');
}

/**
 * Determines if $viewer has access to $user's friends list
 *
 * @param ElggUser $user   User whose friends are to be displayed
 * @param ElggUser $viewer Viewer
 * @return bool
 */
function user_friends_can_view_friends(ElggUser $user, ElggUser $viewer = null) {

	if (!isset($viewer)) {
		$viewer = elgg_get_logged_in_user_entity();
	}

	$permission = false;

	if ($viewer && elgg_check_access_overrides($viewer->guid)) {
		$permission = true;
	}

	$setting = elgg_get_plugin_user_setting('friend_list_visibility', $user->guid, 'user_friends');
	if (!isset($setting)) {
		$setting = elgg_get_plugin_setting('friend_list_visibility', 'user_friends', ACCESS_PUBLIC);
	}

	switch ((int) $setting) {
		case ACCESS_PRIVATE :
			$permission = $viewer && $user->canEdit($viewer->guid);
			break;

		case ACCESS_FRIENDS:
			$permission = $viewer && $user->isFriendsWith($viewer->guid);
			break;

		case ACCESS_LOGGED_IN :
			$permission = ($viewer);
			break;

		case ACCESS_PUBLIC :
			$permission = true;
			break;
	}

	$params = array(
		'viewer' => $viewer,
		'user' => $user,
	);

	return elgg_trigger_plugin_hook('permissions_check:view_friends_list', 'user', $params, $permission);
}

/**
 * User friends page handler
 *
 * @param array  $segments   URL segments
 * @param string $identifier Page ID
 * @return bool
 */
function user_friends_page_handler($segments, $identifier) {

	$username = array_shift($segments);
	$page = array_shift($segments);

	if (!$username) {
		$user = elgg_get_logged_in_user_entity();
	} else {
		$user = get_user_by_username($username);
	}

	if (!$page) {
		$page = 'index';
	}

	if (!$user) {
		forward('', '404');
	}

	elgg_set_page_owner_guid($user->guid);

	$resource = elgg_view_resource("friends/$page", array(
		'username' => $user->username,
		'entity' => $user,
		'segments' => $segments,
	));

	if ($resource) {
		echo $resource;
		return true;
	}

	return false;
}

/**
 * Routes friend request pages
 * 
 * @param string $hook   "route"
 * @param string $type   "friend_request"
 * @param array  $return Identifier and segments
 * @param array  $params Hook params
 * @return array
 */
function user_friends_route_friend_request($hook, $type, $return, $params) {

	$identifier = elgg_extract('identifier', $return);
	$segments = (array) elgg_extract('segments', $return, array());

	if ($identifier != 'friend_request') {
		return;
	}
	$username = array_shift($segments);

	if (!$username) {
		$user = elgg_get_logged_in_user_entity();
	} else {
		$user = get_user_by_username($username);
	}

	return array(
		'identifier' => 'friends',
		'segments' => array(
			$user->username,
			'requests'
		)
	);
}

/**
 * Routes friend request pages
 *
 * @param string $hook   "route"
 * @param string $type   "collections"
 * @param array  $return Identifier and segments
 * @param array  $params Hook params
 * @return array
 */
function user_friends_route_collections($hook, $type, $return, $params) {

	$identifier = elgg_extract('identifier', $return);
	$segments = (array) elgg_extract('segments', $return, array());

	if ($identifier != 'collections') {
		return;
	}
	$page = array_shift($segments);
	if ($page == 'owner') {
		$username = array_shift($segments);
	} else if ($page == 'add') {
		$owner_guid = array_shift($segments);
		$owner = get_entity($owner_guid);
		$username = $owner->username;
	}

	if (!$username) {
		$user = elgg_get_logged_in_user_entity();
	} else {
		$user = get_user_by_username($username);
	}

	return array(
		'identifier' => 'friends',
		'segments' => array(
			$user->username,
			'collections',
			$page,
		)
	);
}

/**
 * Routes friend request pages
 *
 * @param string $hook   "route"
 * @param string $type   "invite"
 * @param array  $return Identifier and segments
 * @param array  $params Hook params
 * @return array
 */
function user_friends_route_invite($hook, $type, $return, $params) {

	$identifier = elgg_extract('identifier', $return);

	if ($identifier != 'invite') {
		return;
	}

	$user = elgg_get_logged_in_user_entity();

	return array(
		'identifier' => 'friends',
		'segments' => array(
			$user->username,
			'invite',
		)
	);
}

/**
 * Setup friendship menu
 *
 * @param string         $hook   "register"
 * @param string         $type   "menu:friendship"
 * @param ElggMenuItem[] $return Menu
 * @param array          $params Hook params
 * @return ElggMenuItem[]
 */
function user_friends_friendship_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	$viewer = elgg_get_logged_in_user_entity();

	if (check_entity_relationship($viewer->guid, 'friend', $entity->guid)) {
		$return[] = ElggMenuItem::factory([
					'name' => 'friend:remove',
					'href' => "action/friends/remove?friend={$entity->guid}",
					'text' => elgg_echo('friend:remove'),
					'confirm' => true,
		]);
	} else if (check_entity_relationship($entity->guid, 'friendrequest', $viewer->guid)) {
// received request
		$return[] = ElggMenuItem::factory([
					'name' => 'friend:approve',
					'href' => "action/friend_request/approve?guid={$entity->guid}",
					'text' => elgg_echo('friend_request:approve'),
					'is_action' => true,
		]);
		$return[] = ElggMenuItem::factory([
					'name' => 'friend:decline',
					'href' => "action/friend_request/decline?guid={$entity->guid}",
					'text' => elgg_echo('friend_request:decline'),
					'confirm' => true,
		]);
	} else if (check_entity_relationship($viewer->guid, 'friendrequest', $entity->guid)) {
// sent request
		$return[] = ElggMenuItem::factory([
					'name' => 'friend:revoke',
					'href' => "action/friend_request/revoke?guid={$entity->guid}",
					'text' => elgg_echo('friend_request:revoke'),
					'confirm' => true,
		]);
	}

	return $return;
}

/**
 * Setup topbar menu
 *
 * @param string         $hook   "register"
 * @param string         $type   "menu:topbar"
 * @param ElggMenuItem[] $return Menu
 * @param array          $params Hook params
 * @return ElggMenuItem[]
 */
function user_friends_topbar_menu_setup($hook, $type, $return, $params) {

	$user = elgg_get_logged_in_user_entity();
	if (!$user) {
		return;
	}

	$item = ElggMenuItem::factory(array(
				'name' => 'friends',
				'href' => "friends/{$user->username}",
				'text' => elgg_view_icon('users'),
				'title' => elgg_echo('friends'),
				'priority' => 300,
	));

	// count friend requests
	$count = elgg_get_entities_from_relationship([
		'type' => 'user',
		'count' => true,
		'relationship' => 'friendrequest',
		'relationship_guid' => $user->guid,
		'inverse_relationship' => true,
	]);

	if ($count) {
		if ($count > 99) {
			$count = '99+';
		}

		if (elgg_is_active_plugin('menus_api')) {
			$item->setData('indicator', $count);
		} else {
			$text = $item->getText();
			$indicator = elgg_format_element('span', ['class' => 'friend-request-new'], $count);
			$item->setText($text . $indicator);
		}
		$item->setHref("friends/$user->username/requests");
	}

	$return[] = $item;
	return $return;
}

/**
 * Prevents the widget from showing friends if friend visibility criteria is not met
 * 
 * @param string $hook   "view"
 * @param string $type   "widgets/friends/content"
 * @param string $return View
 * @param array  $params Hook params
 * @return string
 */
function user_friends_friends_widget_access($hook, $type, $return, $params) {

	$vars = elgg_extract('vars', $params);
	$entity = elgg_extract('entity', $vars);
	if (!$entity instanceof ElggWidget) {
		return;
	}

	$owner = $entity->getOwnerEntity();
	if (!user_friends_can_view_friends($owner)) {
		return elgg_format_element('p', ['class' => 'elgg-no-results'], elgg_echo('user:friends:no_access'));
	}
}