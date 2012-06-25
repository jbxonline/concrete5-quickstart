<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$ih = Loader::helper('validation/numbers');
$dh = Loader::helper('concrete/dashboard');
$ish = Loader::helper('concrete/interface');
$canAdd = false;

if ($ih->integer($_REQUEST['cID'])) {
	$c = Page::getByID($_REQUEST['cID']);
	if (is_object($c) && (!$c->isError())) { 
		$cp = new Permissions($c);
		if ($dh->inDashboard($c)) {
			if ($cp->canRead()) {
				$canAdd = true;
			}
		} else {
			if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage() || $cp->canApproveCollection()) { // we get the bar
				$canAdd = true;
			}
		}
	}
}

if ($canAdd) {
	$u = new User();
	$r = new stdClass;
	if (Loader::helper('validation/token')->validate('access_quick_nav', $_REQUEST['token'])) {
		$quicknav = unserialize($u->config('QUICK_NAV_BOOKMARKS'));
		if (!is_array($quicknav)) {
			$quicknav = array();
		}
		if (!in_array($c->getCollectionID(), $quicknav)) {
			$quicknav[] = $c->getCollectionID();
			$task = 'add';
			$r->link = $ish->getQuickNavigationLinkHTML($c);
		} else {
			$tmpquicknav = $quicknav;
			$quicknav = array();
			foreach($tmpquicknav as $qid) {
				if ($qid != $c->getCollectionID()) {
					$quicknav[] = $qid;
				}
			}
			$task = 'remove';
		}
		$u->saveConfig('QUICK_NAV_BOOKMARKS', serialize($quicknav));
		$r->success = true;
		$r->result = $task;
		print Loader::helper('json')->encode($r);
		exit;
	}
}