<form action="<?= $action; ?>">
	<div class="h30">
		<span class="fleft w120">Όνομα :</span> 
		<input class="fleft" value="<?= $user->name; ?>" type="text" name="name"/>
		<br clear="all"/>
	</div>
	<div class="h30">
		<span class="fleft w120">Επώνυμο :</span> 
		<input class="fleft" value="<?= $user->surname; ?>" type="text" name="surname"/>
		<br clear="all"/>
	</div>
	<div class="h30">
		<span class="fleft w120">Όνομα χρήστη :</span> 
		<input class="fleft" value="<?= $user->username; ?>" type="text" name="username"/>
		<br clear="all"/>
	</div>
	<div class="h30">
		<span class="fleft w120">Κωδικός :</span> 
		<input class="fleft" value="<?= $user->pass; ?>" type="password" name="pass"/>
		<br clear="all"/>
	</div>
	<div>
		<span class="fleft w120">Ομάδες :</span> 
		<select name="groups" multiple="true" style="width:70%;height:60px">
			<?= $groups; ?>
		</select>
		<br clear="all"/>
	</div>
	<div class="h30">
		<a class="button fright mtop20" href="#" 
					onclick="user.save(this);this.blur();"><span>Αποθήκευση</span></a>
		<br clear="all"/>
	</div>
	<input type="hidden" name="id" value="<?= $user->id; ?>"/>
</form>