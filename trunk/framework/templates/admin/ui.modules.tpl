<script type="text/javascript" src="/framework/static/js/tabs.js"></script>
<script type="text/javascript" src="/framework/admin/js/user.js"></script>
<script type="text/javascript" src="/framework/admin/js/group.js"></script>
<script type="text/javascript" src="/framework/admin/js/module.js"></script>
<link rel="stylesheet" type="text/css" href="/framework/static/css/tabs.css" />
<div style="padding:20px">
	<div id="my-container" class="tab-container">
		<ul class="tabs">
			<li target="for_tab1"><a href="#">Users</a></li>
			<li target="for_tab2"><a href="#">Groups</a></li>
			<li target="for_tab3"><a href="#">Modules</a></li>
		</ul>
		<div class="tab-content">
			<div class="content" style="padding:0px" id="for_tab1">
				<a class="button fright mtop20" href="#" 
					onclick="user.create();this.blur();"><span>Create User</span></a>
				<br clear="right"/><br/>
				
				<div class="decorate-first">
					<?= $users; ?>
				</div>
			</div>
			
			<div class="content" style="padding:0px" id="for_tab2">
				<a class="button fright mtop20" href="#" 
					onclick="group.create();this.blur();"><span>Create Group</span></a>
				<br clear="right"/><br/>
				<div class="decorate-first">
					<?= $groups; ?>
				</div>
			</div>
			<div class="content" style="padding:0px" id="for_tab3">
				<a class="button fright mtop20" href="#" 
					onclick="admin.module.create();this.blur();"><span>Create Module</span></a>
				<br clear="right"/><br/>
				<div class="decorate-first">
					<?= $modules; ?>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var s = new Tab('my-container');
			s.activate(s.tabs[0]);
		</script>
	</div>
</div>