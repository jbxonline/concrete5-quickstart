<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

?>
	
<div class="ccm-ui">

<?php  if ($_REQUEST['ctID']) { ?>

	<form method="post" action="<?php echo $c->getCollectionAction()?>" id="ccmAddPage" onsubmit="jQuery.fn.dialog.showLoader()">		
	<input type="hidden" name="rel" value="<?php echo $_REQUEST['rel']?>" />
	<input type="hidden" name="ctID" value="<?php echo $_REQUEST['ctID']?>" />

	<div id="ccm-add-page-information">
		
		<h4><?php echo t('Standard Properties')?></h4>
		<?php  $form = Loader::helper('form'); ?>

		<div class="clearfix">
			<?php echo $form->label('cName', t('Name'))?>
			<div class="input"><input type="text" name="cName" value="" class="text span8" onKeyUp="makeAlias(this.value, 'cHandle')" ></div>
		</div>

		
		<div class="clearfix">
			<?php echo $form->label('cHandle', t('URL Slug'))?>
			<div class="input"><input type="text" name="cHandle" class="span8" value="" id="cHandle"></div>
		</div>
		
		<div class="clearfix">		
			<?php echo $form->label('cDatePublic', t('Public Date/Time'))?>
			<div class="input">
			<?php 
			$dt = Loader::helper('form/date_time');
			echo $dt->datetime('cDatePublic' );
			?> 
			</div>
		</div>		
		
		<div class="clearfix">
			<?php echo $form->label('cDescription', t('Description'))?>
			<div class="input">
			<textarea name="cDescription" rows="4" class="span8"></textarea>
			</div>
		</div>	
		<?php 
		$attribs = $ct->getAvailableAttributeKeys();
		$mc = $ct->getMasterTemplate();
		?>

	<?php  if (count($attribs) > 0) { ?>
		<h4><?php echo t('Custom Attributes')?></h4>
		

	<?php 	
	ob_start();

	foreach($attribs as $ak) { 
	
		if (is_object($mc)) { 
			$caValue = $mc->getAttributeValueObject($ak);
		}		
		?>
	
	
		<div class="clearfix">
			<label><?php echo $ak->getAttributeKeyName()?></label>
			<div class="input">
			<?php echo $ak->render('composer', $caValue); ?>
			</div>
		</div>
		
	<?php  } 
	$contents = ob_get_contents();
	ob_end_clean(); ?>	
	
	<script type="text/javascript">
	<?php  
	$v = View::getInstance();
	$headerItems = $v->getHeaderItems();
	foreach($headerItems as $item) {
		if ($item instanceof CSSOutputObject) {
			$type = 'CSS';
		} else {
			$type = 'JAVASCRIPT';
		} ?>
		 ccm_addHeaderItem("<?php echo $item->file?>", '<?php echo $type?>');
		<?php  
	} 
	?>
	</script>
	
	<?php  print $contents; ?>
		
		<?php  } ?>
		
	</div>
	
	

	<div class="dialog-buttons">
		<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn"><?php echo t('Cancel')?></a>
		<input type="submit" onclick="$('#ccmAddPage').submit()" class="btn primary ccm-button-right" value="<?php echo t('Add Page')?>" />
	</div>	
	
	<input type="hidden" name="add" value="1" />
	<input type="hidden" name="processCollection" value="1">
	
	</form>

<script type="text/javascript">
	
	$(function() {
		var height = $("#ccm-add-page-information").height();
		var dlog = $("#ccm-add-page-information").closest('.ui-dialog-content');
		if (height > 256) {
			height = height + 160;
			if (height < 650) { 
				dlog.dialog('option', 'height', height);
			} else {
				dlog.dialog('option', 'height', '650');
			}
			dlog.dialog('option','position','center');
		} 
	});
	function makeAlias(value, formInputID) {
		alias = value.replace(/[&]/gi, "and");
		alias = alias.replace(/[\s|.]+/gi, "<?php echo PAGE_PATH_SEPARATOR?>");
		
		// thanks fernandos
        alias = alias.replace(/[\u00C4\u00E4]/gi, "ae");            // ��    
        alias = alias.replace(/[\u00D6\u00F6]/gi, "oe");            // ��    
        alias = alias.replace(/[\u00DF]/gi, "ss");                  // �    
        alias = alias.replace(/[\u00DC\u00FC]/gi, "ue");            // ��
        alias = alias.replace(/[\u00C6\u00E6]/gi, "ae");            // �� 
        alias = alias.replace(/[\u00D8\u00F8]/gi, "oe");            // � 
        alias = alias.replace(/[\u00C5\u00E5]/gi, "aa");            // ��    
        alias = alias.replace(/[\u00E8\u00C8\u00E9\u00C9]/gi, "e"); // ���� 
		
		alias = alias.replace(/[^0-9A-Za-z]/gi, "<?php echo PAGE_PATH_SEPARATOR?>");
		alias = alias.replace(/<?php echo PAGE_PATH_SEPARATOR?>+/gi, '<?php echo PAGE_PATH_SEPARATOR?>');
		if (alias.charAt(alias.length-1) == '<?php echo PAGE_PATH_SEPARATOR?>') {
			alias = alias.substring(0,alias.length-1);
		}
		if (alias.charAt(0) == '<?php echo PAGE_PATH_SEPARATOR?>') {
			alias = alias.substring(1,alias.length);
		}
		alias = alias.toLowerCase();
		
		formObj = document.getElementById(formInputID);
		formObj.value = alias;
	} 	
</script>



<?php  } else {


$ctArray = CollectionType::getList($c->getAllowedSubCollections());
$cp = new Permissions($c);

$cnt = 0;
for ($i = 0; $i < count($ctArray); $i++) {
	$ct = $ctArray[$i];
	if ($cp->canAddSubCollection($ct)) { 
		$cnt++;
	}
}

?>
		<div id="ccm-choose-pg-type">
			<h4 id="ccm-choose-pg-type-title"><?php echo t('Choose a Page Type')?></h4>
			<ul id="ccm-select-page-type">
				<?php  
				foreach($ctArray as $ct) { 
					if ($cp->canAddSubCollection($ct)) { 
					$requiredKeys=array();
					$aks = $ct->getAvailableAttributeKeys();
					foreach($aks as $ak)
						$requiredKeys[] = intval($ak->getAttributeKeyID());
						
					$usedKeysCombined=array();
					$usedKeys=array();
					$setAttribs = $c->getSetCollectionAttributes();
					foreach($setAttribs as $ak) 
						$usedKeys[] = $ak->getAttributeKeyID(); 
					$usedKeysCombined = array_merge($requiredKeys, $usedKeys);
					?>
					
					<?php  $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>
			
					<li class="<?php echo $class?>"><a class="dialog-launch" dialog-width="600" dialog-title="<?php echo t('Add %s', Loader::helper('text')->entities($ct->getCollectionTypeName()))?>" dialog-height="310" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?cID=<?php echo $_REQUEST['cID']?>&ctask=add&rel=<?php echo $_REQUEST['rel']?>&ctID=<?php echo $ct->getCollectionTypeID()?>"><?php echo  $ct->getCollectionTypeIconImage(); ?></a>
					<span id="pgTypeName<?php echo $ct->getCollectionTypeID()?>"><?php echo $ct->getCollectionTypeName()?></span>
					</li> 
				
				<?php  } 
				
				}?>
			
			</ul>
	</div>
	
<?php  } ?>

</div>