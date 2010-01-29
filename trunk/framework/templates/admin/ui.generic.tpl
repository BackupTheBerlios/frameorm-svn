<div id="<?= $id; ?>" onclick="<?= $onclick; ?>" 
	class="section-container">
	<span style="margin-left: 10px"><?= $name?></span>
	<div class="actions">
		<img src="/framework/admin/imgs/delete.png"
			title="Διαγραφή"
			onclick="<?= $ondelete; ?>"
			onmouseover="utils.rollImage(this);"
			onmouseout="utils.rollImage(this);"/>
		<img src="/framework/admin/imgs/edit.png"
			title="Επεξεργασία"
			onclick="<?= $onedit; ?>"
			onmouseover="utils.rollImage(this);"
			onmouseout="utils.rollImage(this);"/>
	</div>
</div>