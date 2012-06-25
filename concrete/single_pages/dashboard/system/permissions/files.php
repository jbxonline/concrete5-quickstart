<?php 
	
	Loader::model('file_set');
	$fs = FileSet::getGlobal();
	$gl = new GroupList($fs);
	$ul = new UserInfoList($fs);
	$uArray = $ul->getUserInfoList();
	?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Manager Permissions'), false, 'span10 offset3', false)?>


	<form method="post" id="file-access-permissions" action="<?php echo $this->url('/dashboard/system/permissions/files', 'save_global_permissions')?>">
	<div class="ccm-pane-options clearfix">
		<a class="btn ccm-button-right dialog-launch ug-selector" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_dialog?mode=choose_multiple" dialog-modal="false" dialog-width="90%" dialog-title="<?php echo t('Add User')?>"  dialog-height="70%"><?php echo t('Add User')?></a>
		<a class="btn ccm-button-right dialog-launch ug-selector" style="margin-right: 5px" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/select_group" dialog-modal="false" dialog-title="<?php echo t('Add Group')?>"><?php echo t('Add Group')?></a>
	</div>
	<div class="ccm-pane-body">
		<?php echo $validation_token->output('file_permissions');?>


		<p>
		<?php echo t('Add users or groups to determine access to the file manager.');?>
		</p>
		
		<div class="ccm-spacer">&nbsp;</div><br/>
		
		<div id="ccm-file-permissions-entities-wrapper" class="ccm-permissions-entities-wrapper">			
		<div id="ccm-file-permissions-entity-base" class="ccm-permissions-entity-base">
		
			<?php  print $this->controller->getFileAccessRow('GLOBAL'); ?>
			
			
		</div>
		
		
		<?php  $gArray = $gl->getGroupList();
		foreach($gArray as $g) { ?>
			
			<?php  print $this->controller->getFileAccessRow('GLOBAL', 'gID_' . $g->getGroupID(), $g->getGroupName(), $g->getFileSearchLevel(), $g->getFileReadLevel(), $g->getFileWriteLevel(), $g->getFileAdminLevel(), $g->getFileAddLevel(), $g->getAllowedFileExtensions()); ?>
		
		<?php  } ?>
		<?php  foreach($uArray as $ui) { ?>
			
			<?php  print $this->controller->getFileAccessRow('GLOBAL', 'uID_' . $ui->getUserID(), $ui->getUserName(), $ui->getFileSearchLevel(), $ui->getFileReadLevel(), $ui->getFileWriteLevel(), $ui->getFileAdminLevel(), $ui->getFileAddLevel(), $ui->getAllowedFileExtensions()); ?>
		
		<?php  } ?>
		</div>
		
	</div>
	<div class="ccm-pane-footer">
		<?php  print $concrete_interface->submit(t('Save'), 'file-access-permissions', 'right', 'primary'); ?>
	</div>
	</form>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

	<script type="text/javascript">
	$(function() {	
		ccm_triggerSelectUser = function(uID, uName) {
			ccm_alSelectPermissionsEntity('uID', uID, uName);
		}
		
		ccm_triggerSelectGroup = function (gID, gName) {
			ccm_alSelectPermissionsEntity('gID', gID, gName);
		}
		
		$(".ug-selector").dialog();	
		ccm_alActivateFilePermissionsSelector();	
	});
	
	</script>