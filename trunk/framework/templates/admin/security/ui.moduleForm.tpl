<form action="<?= $action; ?>">
	<div id="module-container" class="tab-container">
		<ul class="tabs">
			<li target="for_details"><a href="#">Module Details</a></li>
			<li target="for_permissions"><a href="#">Permissions</a></li>
		</ul>
		<div class="tab-content">
			
			<div class="content" id="for_details">
				<div class="h30 padding10">
					<span class="fleft w120">Όνομα :</span> 
					<input class="fleft" value="<?= $module->name; ?>" 
						type="text" name="name"/>
					<br clear="all"/>
				</div>
			</div>
			
			<div class="content" id="for_permissions">
				<div class="h30" id="perm-container">
					<a class="button fright" href="#" 
							onclick="admin.module.addUser(this);this.blur();"><span>Add User</span></a>
					<a class="button fright" href="#" 
							onclick="admin.module.addGroup(this);this.blur();"><span>Add Group</span></a>
					<br clear="all"/>
				</div>
				<br/>
				<?= $users; ?>

				<?= $groups; ?>
			</div>

		</div>
		<script type="text/javascript">
			
		</script>
	</div>
	<div class="h30">
		<a class="button fright mtop20" href="#" 
				onclick="admin.module.save(this);this.blur();"><span>Αποθήκευση</span></a>
		<br clear="all"/>
	</div>
</form>