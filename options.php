<?php

function itd_plugin_menu()
{
	add_options_page(__('Inline Text Direction Options', 'itd'), __('Inline Text Direction', 'itd'), 'administrator', 'itd-options', 'itd_plugin_options');
}

function itd_register_settings()
{
	// whitelist options
	register_setting( 'itd-options', 'presentation_type' );
	register_setting( 'itd-options', 'foreign_lang' );
	register_setting( 'itd-options', 'submit' );
}


function itd_plugin_options()
{
	$itd_options = get_option('itd-options');
	$langs = itd_get_preferences('langs');
	$presentations = itd_get_preferences('presentations');
	$updated = false;

	if( isset($_POST['submit']) )
	{
		$itd_options['presentation_type'] = $_POST["presentation_type"];
		$itd_options['foreign_lang'] = $_POST["foreign_lang"];
		update_option('itd-options', $itd_options );
		$updated = true;
	}

?>
<div class="wrap">
<?php
if ($updated)
{
	echo "<div id='message' class='updated fade'><p>";
	_e('Options saved.');
	echo "</p></div>";
}
?>

<h2><?php _e('Inline Text Direction Options', 'itd'); ?></h2>
<form method="post" action="">
<?php settings_fields( 'itd-options' ); ?>


<table class="form-table">

<h3><?php _e('How are you serving your HTML pages?', 'itd'); ?></h3>
<p><?php _e('If you do not know your HTML serving type, please choose', 'itd'); ?> "<?php _e('As XHTML served as text/html', 'itd'); ?>".</p>
<tr>
<th scope="row"><?php _e('Type', 'itd'); ?></th>
<td>
	<fieldset><legend class="screen-reader-text"><span><?php _e('Type', 'itd'); ?></span></legend>
	<?php foreach($presentations as $type => $value): ?>
		<label title="<?php echo $value; ?>"><input type='radio' name='presentation_type'<?php echo ($type == $itd_options['presentation_type']) ? ' checked="checked"' : '' ?> value='<?php echo $type; ?>' /> <?php echo $value; ?></label><br />
	<?php endforeach; ?>
	<p><a href="http://www.w3.org/International/geo/html-tech/tech-lang.html#ri20040429.092928424"><?php _e('More information about the different types of serving HTML pages.', 'itd'); ?></a></p>
	</fieldset>
</td>
</tr>
<tr>
<th scope="row"><label for="foreign_lang"><?php _e('Foreign language', 'itd'); ?></label></th>
<td>
	<select name="foreign_lang" id="foreign_lang">
	<?php foreach($langs as $type => $value): ?>
		<option value='<?php echo $type; ?>'<?php echo ($type == $itd_options['foreign_lang']) ? ' selected="selected"' : '' ?>><?php echo $value; ?></option>
	<?php endforeach; ?>
	</select>
</td>
</tr>

</table>

<p class="submit">
<input type="submit" class="button-primary" name="submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>

<?php
}
?>