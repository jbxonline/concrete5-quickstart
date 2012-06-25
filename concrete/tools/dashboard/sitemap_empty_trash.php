<?php 

defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard/sitemap');
if (!$dh->canRead()) {
	die(t("Access Denied."));
}

$trash = Page::getByPath(TRASH_PAGE_PATH);
$i = 0;
if (is_object($trash) && !$trash->isError()) {
	Loader::model('page_list');
	$pl = new PageList();
	$pl->filterByParentID($trash->getCollectionID());
	$pl->includeInactivePages();
	$pages = $pl->get();	
	foreach($pages as $pc) {
		$cp = new Permissions($pc);
		if ($cp->canDeleteCollection()) {
			$i++;
			$pc->delete();			
		}
	}
}

if ($i == 1) {
	$message = t('1 page deleted.');
} else {
	$message = t('%s pages deleted', $i);
}

$obj = new stdClass;
$obj->message = $message;
print Loader::helper('json')->encode($obj);