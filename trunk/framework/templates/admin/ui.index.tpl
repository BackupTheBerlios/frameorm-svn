<div class="page border-gray">
	<div id="header">
		<div id="navigation">
			<span style="padding-left:10px;font-weight:bold"></span>
		</div>
		<div id="navbar"><?= $navbar; ?></div>
		<div id="logout">
			<a href="#" onclick="admin.logout()">logout <?= $user; ?></a>
		</div>
	</div>
	<div id="wrapper">
		<div id="left">
			<ul>
				<?= $sections; ?>
			</ul>
		</div>
		<div id="right">
			<?= $content; ?>
		</div>
	</div>
</div>