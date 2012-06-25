<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo LANGUAGE?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php  
if (is_object($c)) {
	$v = View::getInstance();
	$v->disableEditing();
 	Loader::element('header_required');
} else { 
	print Loader::helper('html')->javascript('jquery.js');
	$this->outputHeaderItems();
}
?>

<!-- insert CSS for Default Concrete Theme //-->
<style type="text/css">@import "<?php echo ASSETS_URL_CSS?>/ccm.default.theme.css";</style>
<style type="text/css">@import "<?php echo ASSETS_URL_CSS?>/ccm.install.css";</style>
<style type="text/css">@import "<?php echo ASSETS_URL_CSS?>/ccm.app.css";</style>
</head>
<body>
<div class="ccm-ui">

<div id="ccm-logo"><?php echo Loader::helper('concrete/interface')->getToolbarLogoSRC()?></div>




<div class="container">

<?php  Loader::element('system_errors', array('format' => 'block', 'error' => $error)); ?>
<?php  print $innerContent ?>

</div>
</div>

</body>
</html>
