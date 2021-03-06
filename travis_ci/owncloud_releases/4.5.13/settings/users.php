<?php
/**
 * Copyright (c) 2011, Robin Appelman <icewind1991@gmail.com>
 * This file is licensed under the Affero General Public License version 3 or later.
 * See the COPYING-README file.
 */

require_once '../lib/base.php';
OC_Util::checkSubAdminUser();

// We have some javascript foo!
OC_Util::addScript( 'settings', 'users' );
OC_Util::addScript( 'core', 'multiselect' );
OC_Util::addScript('core', 'jquery.inview');
OC_Util::addStyle( 'settings', 'settings' );
OC_App::setActiveNavigationEntry( 'core_users' );

$users = array();
$groups = array();

$isadmin = OC_Group::inGroup(OC_User::getUser(), 'admin')?true:false;
if($isadmin) {
	$accessiblegroups = OC_Group::getGroups();
	$accessibleusers = OC_User::getUsers('', 30);
	$subadmins = OC_SubAdmin::getAllSubAdmins();
}else{
	$accessiblegroups = OC_SubAdmin::getSubAdminsGroups(OC_User::getUser());
	$accessibleusers = OC_Group::usersInGroups($accessiblegroups, '', 30);
	$subadmins = false;
}

foreach($accessibleusers as $i) {
	$users[] = array(
		"name" => $i, 
		"groups" => join( ", ", /*array_intersect(*/OC_Group::getUserGroups($i)/*, OC_SubAdmin::getSubAdminsGroups(OC_User::getUser()))*/),
		'quota'=>OC_Preferences::getValue($i, 'files', 'quota', 'default'),
		'subadmin'=>implode(', ', OC_SubAdmin::getSubAdminsGroups($i)));
}

foreach( $accessiblegroups as $i ) {
	// Do some more work here soon
	$groups[] = array( "name" => $i );
}
$quotaPreset=OC_Appconfig::getValue('files', 'quota_preset', 'default,none,1 GB, 5 GB, 10 GB');
$quotaPreset=explode(',',$quotaPreset);
foreach($quotaPreset as &$preset) {
	$preset=trim($preset);
}

$defaultQuota=OC_Appconfig::getValue('files', 'default_quota', 'none');

$tmpl = new OC_Template( "settings", "users", "user" );
$tmpl->assign( "users", $users );
$tmpl->assign( "groups", $groups );
$tmpl->assign( 'isadmin', (int) $isadmin);
$tmpl->assign( 'subadmins', $subadmins);
$tmpl->assign( 'numofgroups', count($accessiblegroups));
$tmpl->assign( 'quota_preset', $quotaPreset);
$tmpl->assign( 'default_quota', $defaultQuota);
$tmpl->printPage();