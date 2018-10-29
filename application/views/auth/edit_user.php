<?php
	$this->load->view("header");
?>
<h1><?php echo lang('edit_user_heading');?></h1>
<p><?php echo lang('edit_user_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<style type="text/css">
.checkboxes label {
    display: block;
    float: left;
    padding-right: 10px;
    white-space: nowrap;
}
.checkboxes input {
    vertical-align: middle;
}
.checkboxes label span {
    vertical-align: middle;
}

#grouplist ol {   
	display: block;
    list-style-type: decimal;
    -webkit-margin-before: 1em;
    -webkit-margin-after: 1em;
    -webkit-margin-start: 0px;
    -webkit-margin-end: 0px;
    -webkit-padding-start: 40px;
}  
#grouplist li {   
	display: list-item;
    text-align: -webkit-match-parent;
}  
</style>
<?php echo form_open(uri_string());?>

      <p>
            <?php echo lang('edit_user_username_label', 'username');?> <br />
            <?php echo form_input($username);?>
      </p>
      <p>
            <?php echo lang('edit_user_fname_label', 'first_name');?> <br />
            <?php echo form_input($first_name);?>
      </p>

      <p>
            <?php echo lang('edit_user_lname_label', 'last_name');?> <br />
            <?php echo form_input($last_name);?>
      </p>

      <p>
            <?php echo lang('edit_user_company_label', 'company');?> <br />
            <?php echo form_input($company);?>
      </p>

      <p>
            <?php echo lang('edit_user_phone_label', 'phone');?> <br />
            <?php echo form_input($phone);?>
      </p>

      <p>
            <?php echo lang('edit_user_password_label', 'password');?> <br />
            <?php echo form_input($password);?>
      </p>

      <p>
            <?php echo lang('edit_user_password_confirm_label', 'password_confirm');?><br />
            <?php echo form_input($password_confirm);?>
      </p>

      <?php if ($this->ion_auth->is_admin()): ?>
		
          <h3><?php echo lang('edit_user_groups_heading');?></h3>
          <br />
          <div id="grouplist">
          <ol>
          <?php foreach ($groups as $group):?>
               <li>
	              <label class="checkbox">
	              <?php
	                  $gID=$group['id'];
	                  $checked = null;
	                  $item = null;
	                  foreach($currentGroups as $grp) {
	                      if ($gID == $grp->id) {
	                          $checked= ' checked="checked"';
	                      break;
	                      }
	                  }
	              ?>
	              
	               <input type="checkbox" name="groups[]" value="<?php echo $group['id'];?>"<?php echo $checked;?>>
	               <?php echo htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');?>
	              </label>
	             </li>
              
          <?php endforeach?>
          </ol>
          
         
		  </div>
      <?php endif ?>

      <?php echo form_hidden('id', $user->id);?>
      <?php echo form_hidden($csrf); ?>

      <p><?php echo form_submit('submit', lang('edit_user_submit_btn'));?></p>

<?php echo form_close();?>

<?php
	$this->load->view("footer");
?>
