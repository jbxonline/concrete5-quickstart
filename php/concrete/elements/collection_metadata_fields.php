<?php  $form = Loader::helper('form'); ?>
<?php 
$attribs = array();

$requiredKeys = array();
$usedKeys = array();
if ($c->getCollectionTypeID() > 0 && !$c->isMasterCollection()) {
	$cto = CollectionType::getByID($c->getCollectionTypeID());
	$aks = $cto->getAvailableAttributeKeys();
	foreach($aks as $ak) {
		$requiredKeys[] = $ak->getAttributeKeyID();
	}
}
$setAttribs = $c->getSetCollectionAttributes();
foreach($setAttribs as $ak) {
	$usedKeys[] = $ak->getAttributeKeyID();
}
$usedKeysCombined = array_merge($requiredKeys, $usedKeys);

?>

<div class="row">
<div class="span4 columns">
	<h6><?php echo t("All Attributes")?></h6>
	<div class="ccm-block-type-search-wrapper ">

		<div class="ccm-block-type-search">
		<?php echo $form->text('ccmSearchAttributeListField', array('tabindex' => 1, 'autocomplete' => 'off', 'style' => 'width: 155px'))?>
		</div>
		
	</div>
	
	<?php 
	$category = AttributeKeyCategory::getByHandle('collection');
	$sets = $category->getAttributeSets();
	?>

	<ul id="ccm-page-attribute-list" class="icon-select-list">
	<?php  foreach($sets as $as) { ?>
		<li class="icon-select-list-header ccm-attribute-available"><span><?php echo $as->getAttributeSetName()?></span></li>
		<?php  
		$setattribs = $as->getAttributeKeys();
		foreach($setattribs as $ak) { ?>
			
			<li id="sak<?php echo $ak->getAttributeKeyID()?>" class="ccm-attribute-available <?php  if (in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>ccm-attribute-added<?php  } ?>"><a style="background-image: url('<?php echo $ak->getAttributeKeyIconSRC()?>')" href="javascript:void(0)" onclick="ccmShowAttributeKey(<?php echo $ak->getAttributeKeyID()?>)"><?php echo $ak->getAttributeKeyName()?></a></li>	
			
		<?php  
			$attribs[] = $ak;
		} 	
		
	} 

	$unsetattribs = $category->getUnassignedAttributeKeys();
	
	if (count($sets) > 0 && count($unsetattribs) > 0) { ?>
		<li class="icon-select-list-header"><span><?php echo t('Other')?></span></li>
	<?php  }
	
	foreach($unsetattribs as $ak) { ?>
		
		<li id="sak<?php echo $ak->getAttributeKeyID()?>" class="ccm-attribute-available <?php  if (in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>ccm-attribute-added<?php  } ?>"><a style="background-image: url('<?php echo $ak->getAttributeKeyIconSRC()?>')" href="javascript:void(0)" onclick="ccmShowAttributeKey(<?php echo $ak->getAttributeKeyID()?>)"><?php echo $ak->getAttributeKeyName()?></a></li>	
	
	<?php  
		$attribs[] = $ak;
	} 	
	
	?>
	</ul>
	
</div>
<div class="span7">
<h6><?php echo t("Selected Attributes")?></h6>
<div id="ccm-page-attributes-none" <?php  if (count($usedKeysCombined) > 0) { ?>style="display: none"<?php  } ?>>
<div style="padding-top: 140px; width: 400px; text-align: center"><h3>
	<?php  if ($c->isMasterCollection()) { ?>
		<?php echo t('No attributes assigned. Any attributes you set here will automatically be set on pages when they are created.')?>
	<?php  } else { ?>
		<?php echo t('No attributes assigned.')?>
	<?php  } ?></h3></div>
</div>

<?php  
	ob_start();

	foreach($attribs as $ak) {
		$caValue = $c->getAttributeValueObject($ak); ?>

	
		<div class="form-stacked">
		<div class="well" id="ak<?php echo $ak->getAttributeKeyID()?>" <?php  if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?> style="display: none" <?php  } ?>>
		<input type="hidden" class="ccm-meta-field-selected" id="ccm-meta-field-selected<?php echo $ak->getAttributeKeyID()?>" name="selectedAKIDs[]" value="<?php  if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>0<?php  } else { ?><?php echo $ak->getAttributeKeyID()?><?php  } ?>" />
		
			<a href="javascript:void(0)" class="ccm-meta-close" ccm-meta-name="<?php echo $ak->getAttributeKeyName()?>" id="ccm-remove-field-ak<?php echo $ak->getAttributeKeyID()?>" style="display:<?php echo (!in_array($ak->getAttributeKeyID(), $requiredKeys))?'block':'none'?>"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" alt="<?php echo t('remove')?>" /></a>

			<label><?php echo $ak->getAttributeKeyName()?></label>
			<?php echo $ak->render('form', $caValue); ?>
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



</div>
</div>

<script type="text/javascript">

$('input[name=ccmSearchAttributeListField]').focus(function() {
	$(this).css('color', '#666');
	if (!ccmLiveSearchActive) {
		$('#ccmSearchAttributeListField').liveUpdate('ccm-page-attribute-list', 'attributes');
		ccmLiveSearchActive = true;
	}
});

var ccmLiveSearchActive = false;
ccmBlockTypeSearchResultsSelect = function(which, e) {

	e.preventDefault();
	e.stopPropagation();
//	$("input[name=ccmBlockTypeSearch]").blur();

	// find the currently selected item
	var obj = $("li.ccm-item-selected");
	var foundblock = false;
	if (obj.length == 0) {
		$($("#ccm-page-attribute-list li.ccm-attribute-available:not(.icon-select-list-header)")[0]).addClass('ccm-item-selected');
	} else {
		if (which == 'next') {
			var nextObj = obj.nextAll('li.ccm-attribute-available:not(.icon-select-list-header)');
			if (nextObj.length > 0) {
				obj.removeClass('ccm-item-selected');
				$(nextObj[0]).addClass('ccm-item-selected');
			}
		} else if (which == 'previous') {
			var prevObj = obj.prevAll('li.ccm-attribute-available:not(.icon-select-list-header)');
			if (prevObj.length > 0) {
				obj.removeClass('ccm-item-selected');
				$(prevObj[0]).addClass('ccm-item-selected');
			}
		}
		
	}	

	var currObj = $("li.ccm-item-selected");

	var currPos = currObj.position();
	var currDialog = currObj.parents('div.ui-dialog-content');
	var docViewTop = currDialog.scrollTop();
	var docViewBottom = docViewTop + currDialog.innerHeight();

	var elemTop = currObj.position().top;
	var elemBottom = elemTop + docViewTop + currObj.innerHeight();

	if ((docViewBottom - elemBottom) < 0) {
		currDialog.get(0).scrollTop += currDialog.get(0).scrollTop + currObj.height();
	} else if (elemTop < 0) {
		currDialog.get(0).scrollTop -= currDialog.get(0).scrollTop + currObj.height();
	}


	return true;
	
}

ccmPageAttributesDoMapKeys = function(e) {

	if (e.keyCode == 40) {
		ccmBlockTypeSearchResultsSelect('next', e);
	} else if (e.keyCode == 38) {
		ccmBlockTypeSearchResultsSelect('previous', e);
	} else if (e.keyCode == 13) {
		var obj = $("li.ccm-item-selected");
		if (obj.length > 0) {
			obj.find('a').click();
		}
	}
}
ccmPageAttributesMapKeys = function() {
	$(window).bind('keydown.attribs', ccmPageAttributesDoMapKeys);
}

ccmShowAttributeKey = function(akID) {

	$("#ccm-page-attributes-none").hide();
	$("#sak" + akID).addClass('ccm-attribute-added');
	$("#ak" + akID).find('.ccm-meta-field-selected').val(akID);
	$("#ak" + akID).fadeIn(300, 'easeOutExpo');
}

var ccmPathHelper={
	add:function(field){
		var parent = $(field).parent();
		var clone = parent.clone();
		clone.children().each(function() {
			if (this.id != undefined  && (i = this.id.search("-add-")) != -1) {
				this.id = this.id.substr(0, i) + "-add-" + (parseInt(this.id.substr(i+5)) + 1);
			}
			if (this.name != undefined && (i = this.name.search("-add-")) != -1) {
				this.name = this.name.substr(0, i) + "-add-" + (parseInt(this.name.substr(i+5)) + 1);
			}
			if (this.type == "text") {
				this.value = "";
			}
		});
    	$(field).replaceWith('<a href="javascript:void(0)" class="ccm-meta-path-del"><?php  echo t('Remove Path')?></a>');
		clone.appendTo(parent.parent());

		$("a.ccm-meta-path-add,a.ccm-meta-path.del").unbind('click');
		$("a.ccm-meta-path-add").click(function(ev) { ccmPathHelper.add(ev.target) });
		$("a.ccm-meta-path-del").click(function(ev) { ccmPathHelper.del(ev.target) });
	},
	del:function(field){
		$(field).parent().remove();
	}
}
$(function() {
	$(window).css('overflow', 'hidden');
	$(window).unbind('keydown.attribs');
	ccmPageAttributesMapKeys();

	$("a.ccm-meta-close").click(function() {
		var thisField = $(this).attr('id').substring(19);
		var thisName = $(this).attr('ccm-meta-name');
		$("#ccm-meta-field-selected" + thisField).val(0);
		// add it back to the select menu
		$("#sak" + thisField).removeClass('ccm-attribute-added');
		$("#ak" + thisField).fadeOut(80, 'easeOutExpo', function() {
			if ($('li.ccm-attribute-added').length == 0) {
				$("#ccm-page-attributes-none").show();
			}
		});
		
	});

	$("a.ccm-meta-path-add").click(function(ev) { ccmPathHelper.add(ev.target) });
	$("a.ccm-meta-path-del").click(function(ev) { ccmPathHelper.del(ev.target) });

	$("#cHandle").blur(function() {
		var oldCHandle = $("#oldCHandle").val();
		$(".ccm-meta-path input").each(function() {
			if ($(this).val() == "") {
				$(this).val(oldCHandle);
			}
		});
	});

});


</script>

<style type="text/css">
#ccm-properties-custom-tab input.ccm-input-text {
	width: 350px;
}
#ccm-properties-custom-tab textarea.ccm-input-textarea {
	width: 350px;
}

</style>

