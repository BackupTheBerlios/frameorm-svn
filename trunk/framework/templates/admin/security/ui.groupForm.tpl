<form action="<?= $action; ?>">
	<div class="h30">
		<span class="fleft w120">Όνομα :</span> 
		<input class="fleft" value="<?= $group->name; ?>" type="text" name="name"/>
		<br clear="right"/>
	</div>
	<div class="h30">
		<a class="button fright mtop20" href="#" 
					onclick="group.save(this);this.blur();"><span>Αποθήκευση</span></a>
		<br clear="all"/>
	</div>
	<input type="hidden" name="id" value="<?= $group->id; ?>"/>
</form>