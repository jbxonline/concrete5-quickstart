<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	$c = Page::getCurrentPage();
	$cp = new Permissions($c);
	if ($cp->canReadVersions()) {
		$stack = Stack::getByID($stID);	
	} else {
		$stack = Stack::getByID($stID, 'ACTIVE');
	}
	$ax = Area::get($stack, STACKS_AREA_NAME);
	$axp = new Permissions($ax);
	if ($axp->canRead()) {
		$ax->display($stack);
	}
