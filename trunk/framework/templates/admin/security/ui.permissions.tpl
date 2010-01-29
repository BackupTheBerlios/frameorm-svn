<div class="h30">
	<span class="fleft w120"><?= $name; ?></span>
	<select class="fleft w200" name="<?= $type; ?>" id="<?= $id; ?>">
		<?= $options; ?>
	</select>
	<a class="mleft20 button fleft" href="#" 
		onclick="admin.module.remove(this);this.blur();"><span>remove</span></a>
</div>
