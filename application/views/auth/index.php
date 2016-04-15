<?php
	$this->load->view("header");
?>
<h1><?php echo lang('index_heading');?></h1>
<p><?php echo lang('index_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>
<div id="infoMessage">Click on "Edit" under action to edit a user.  **If you have added a new user, you will have to edit their group from here. To delete a user, click the delete link but be careful as this does not warn you first.  </div>
<table id="hor-minimalist-b">
	<tr>
		<th scope="col"><?php echo lang('index_username_th');?></th>		
		<th scope="col"><?php echo lang('index_fname_th');?></th>
		<th scope="col"><?php echo lang('index_lname_th');?></th>
		<th scope="col"><?php echo lang('index_email_th');?></th>
		<th scope="col"><?php echo lang('index_groups_th');?></th>
		<!--<th scope="col"><?php echo lang('index_status_th');?></th>-->
		<th scope="col"><?php echo lang('index_action_th');?></th>
		<th scope="col"><?php echo lang('index_delete_th');?></th>
	</tr>
	<?php foreach ($users as $user):?>
		<tr>
	    <td><?php echo htmlspecialchars($user->username,ENT_QUOTES,'UTF-8');?></td>
            <td><?php echo htmlspecialchars($user->first_name,ENT_QUOTES,'UTF-8');?></td>
            <td><?php echo htmlspecialchars($user->last_name,ENT_QUOTES,'UTF-8');?></td>
            <td><a href="mailto:<?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?>"><?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></a></td>
			<td>
				<?php foreach ($user->groups as $group):?>
					<?php echo anchor("auth/edit_group/".$group->id, htmlspecialchars($group->name,ENT_QUOTES,'UTF-8')) ;?><br />
                <?php endforeach?>
			</td>
			<!--<td><?php echo ($user->active) ? anchor("auth/deactivate/".$user->id, lang('index_active_link')) : anchor("auth/activate/". $user->id, lang('index_inactive_link'));?></td>-->
			<td><?php echo anchor("auth/edit_user/".$user->id, 'Edit') ;?></td>
			<td><?php echo ($user->active) ? anchor("auth/delete_user/".$user->id, lang('index_delete_link')) : anchor("auth/delete_user/". $user->id, lang('index_delete_link'));?></td>
		</tr>
	<?php endforeach;?>
</table>

<p><?php echo anchor('auth/create_user', lang('index_create_user_link'))?> | <?php echo anchor('auth/create_group', lang('index_create_group_link'))?></p>


<?php

	$this->load->view("footer");
?>
