<?php
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge;" />
<head>
	<title><?php echo $title; ?></title>
<link href="<?= site_url('css/style.css') ?>" rel="stylesheet" type="text/css" />
<!--<link href="<?= site_url('css/style.css') ?>" rel="stylesheet" type="text/css" />-->
<!--<link href="jqgrid/css/ui.jqgrid.css" rel="stylesheet" type="text/css" />
<link href="jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css" />-->

<script type="text/javascript">
	var base_url = '<?=base_url();?>';
	var base_top = '<?=base_url();?>';
</script>

<?php //if($this->uri->segment(1) != 'auth') { ?>
	
	<script type="text/javascript" src="jquery/jquery.min.js"></script>
	<script type="text/javascript" src="js/download.js"></script>
	<script type="text/javascript" src="js/formatDate/date.format.js"></script>

	<link rel="stylesheet" href="jqwidgets/jqwidgets/styles/jqx.base.css" type="text/css" />
	<link rel="stylesheet" href="jqwidgets/jqwidgets/styles/jqx.darkblue.css" type="text/css" />
	<link rel="stylesheet" href="jqwidgets/jqwidgets/styles/jqx.metrodark.css" type="text/css" />

	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxcore.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxdata.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxbuttons.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxwindow.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxpanel.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxscrollbar.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxmenu.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxtabs.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxlistbox.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxdropdownlist.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.selection.js"></script> 
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.columnsresize.js"></script>
	<!--<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.columnsreorder.js"></script>-->
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.filter.js"></script> 
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.sort.js"></script> 
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.pager.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.edit.js"></script> 
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.grouping.js"></script> 
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxdata.export.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxgrid.export.js"></script> 
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxcheckbox.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxnumberinput.js"></script>
	<script type="text/javascript" src="jqwidgets/jqwidgets/jqxtooltip.js"></script>


<body class='default'>
<?php 


$all_group = $this->config->item('all','main');
$admin_group = $this->config->item('admin','main');


//get user info
$user = $this->ion_auth->user()->row();
$name = $user->first_name .' '. $user->last_name;

$group = $this->ion_auth->get_users_groups()->result();
foreach($group as $ukey => $urow){
	$groups[] = $urow->name;
}

//print_r($groups);

?>
<div id="wrapper">
	
	<div id="header">
		<div class="wrap">&nbsp;</div>
	</div>
	<div id="navigation">
		<!--<ul class="sf-menu sf-js-enabled sf-shadow">-->
		<ul class="menu">
			
				<li><a href="<?= site_url('/') ?>"><strong>Home</strong></a> 
				
				<li></li>
				<?php if ($this->ion_auth->in_group($admin_group)){ ?> <li><a href="<?= site_url('auth') ?>"><strong>User Management</strong></a></li>
				<?php } ?>
				<li><a href="http://ucf.qualtrics.com//SE/?SID=SV_4Iz9c9tU3Tve9Jb" target="_blanK"><strong>Feedback</strong></a></li>
				
				<span id="logged">Logged in as: <span class="logged"><?php echo $name; ?></span><span>  
				(<a href="<?= site_url('auth/logout') ?>"><strong>Logout</strong></a>)</span></span>
		
		</ul>
	</div>

	<div id="content">

	
