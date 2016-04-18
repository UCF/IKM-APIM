<!doctype html>
<html lang="en-US">
<head>

	<meta charset="utf-8">

	<title>Login</title>

	<!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Varela+Round">-->
	<link href="../css/login.css" rel="stylesheet" type="text/css" />

	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>
<p>

<body>
	<div id="header"></div>
	
	<div id="login">

		<h2><span class="fontawesome-lock"></span>Sign In</h2>

		<?php echo form_open("auth/login");?>

			<fieldset>
				
				  <p>
				    <?php echo lang('login_identity_label', 'identity');?>
				
				    <?php echo form_input($identity);?>
				  </p>

				  <p>
				    <?php echo lang('login_password_label', 'password');?>
				    <?php echo form_input($password);?>
				  </p>
				<p><?php echo form_submit('submit', lang('login_submit_btn'));?></p>
				
				<div id="infoMessage"><?php echo $message;?></div>

				<?php echo form_close();?>

			</fieldset>

		</form>

	</div> <!-- end login -->

</body>	
</html>
