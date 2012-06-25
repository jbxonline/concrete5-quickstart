<?php 
defined('C5_EXECUTE') or die("Access Denied.");
if ($cp->canAdminPage()) {
$gArray = array();
$gl = new GroupList($c, false, true);
$gArray = $gl->getGroupList();
?>

<div class="ccm-ui">
<form method="post" id="ccmPermissionsForm" name="ccmPermissionsForm" action="<?php echo $c->getCollectionAction()?>">
<input type="hidden" name="rel" value="<?php echo $_REQUEST['rel']?>" />

<div class="clearfix">
<h3><?php echo t('Who can view this page?')?></h3>

<ul class="inputs-list">

<?php 

foreach ($gArray as $g) {
?>

<li><label><input type="checkbox" name="readGID[]" value="<?php echo $g->getGroupID()?>" <?php  if ($g->canRead()) { ?> checked <?php  } ?> /> <?php echo t($g->getGroupName())?></label></li>

<?php  } ?>

</ul>
</div>

<div class="clearfix">

<h3><?php echo t('Who can edit this page?')?></h3>

<ul class="inputs-list">

<?php 

foreach ($gArray as $g) {
?>

<li><label><input type="checkbox" name="editGID[]" value="<?php echo $g->getGroupID()?>" <?php  if ($g->canWrite()) { ?> checked <?php  } ?> /> <?php echo t($g->getGroupName())?></label></li>

<?php  } ?>

</ul>
</div>

<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" onclick="$('form[name=ccmPermissionsForm]').submit()" class="ccm-button-right btn primary"><?php echo t('Save')?></a>
</div>	
<input type="hidden" name="update_permissions" value="1" class="accept">
<input type="hidden" name="processCollection" value="1">

<script type="text/javascript">
$(function() {
	$("#ccmPermissionsForm").ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var r = eval('(' + r + ')');
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();

			if (r != null && r.rel == 'SITEMAP') {
				ccmSitemapHighlightPageLabel(r.cID);
			}
			ccmAlert.hud(ccmi18n_sitemap.setPagePermissionsMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
		}
	});
});
</script>

<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
<?php  } ?>