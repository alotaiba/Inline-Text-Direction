<?php
/*
Plugin Name: Inline Text Direction
Plugin URI: http://www.almashroo.com/projects/wordpress/plugins/inline-text-direction/en/
Description: This plugin will add two new buttons on both editors (Visual and HTML), which will enable you to change the direction of inline text from LTR to RTL, or vice versa.
Version: 1.0
Author: Almashroo Development Team
Author URI: http://www.almashroo.com
Text Domain: itd

Copyright 2009  Almashroo  (email : contact@almashroo.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
define( 'ITD_VERSION', '1.0' );
define( 'ITD_FOLDER', plugin_basename( dirname( __FILE__ )) );
define( 'ITD_ABSPATH', str_replace("\\","/", WP_PLUGIN_DIR . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' ));
define( 'ITD_URLPATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
load_plugin_textdomain( 'itd', false, ITD_FOLDER . '/lang' );

require( ITD_ABSPATH . 'options.php' );

// Make a button on HTML editor
function itd_quicktags()
{
	wp_enqueue_script(
		'itd_quicktags',
		ITD_URLPATH . 'js/itd-quicktags.js',
		array('quicktags'),
		ITD_VERSION
	);
}

function itd_tinymceCSS()
{
	$css_file = ITD_URLPATH . 'css/inlinedirection_tinymce.css';
	return $css_file;
}

// Register the filters to add the tinyMCE buttons (if all goes well with the user permissions)
function itd_addbuttons()
{
	// Don't bother doing this stuff if the current user lacks permissions
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;

	// Add only in Rich Editor mode
	if ( get_user_option('rich_editing') == 'true')
	{
		add_filter("mce_external_plugins", "itd_add_tinymce_plugin");
		add_filter('mce_buttons', 'itd_register_button');
	}
}

// Make a button on tinyMCE editor
function itd_register_button($buttons)
{
	array_push($buttons, "separator", "inlinedirection");
	return $buttons;
}

// Add tinyMCE plugin
function itd_add_tinymce_plugin($plugin_array)
{
	$plugin_array['inlinedirection'] = ITD_URLPATH . 'js/tinymce_plugin/editor_plugin.js';
	return $plugin_array;
}

// Need to clear out cached tinyMCE
function itd_refresh_mce($version)
{
	return $version . '-itd-' . ITD_VERSION;
}

//Sotring and returning preferences (Used in the options page)
function itd_get_preferences($type)
{
	$langs = array(
		'en' => __('English', 'itd'),
		'ar' => __('Arabic', 'itd')
	);
	$presentations = array(
		'html' => __('As HTML', 'itd'),
		'text/html' => __('As XHTML served as text/html', 'itd'),
		'application/xhtml+xml' => __('As XHTML served as XML application/xhtml+xml', 'itd')
	);

	switch ($type)
	{
		case 'langs':
			return $langs;
		break;
		case 'presentations':
			return $presentations;
		break;
		default:
			return false;
		break;
	}
}

//Initiaing and storing the default options
function itd_default_options()
{
	$defaults = array(
		'presentation_type' => 'text/html',
		'foreign_lang' => 'en'
	);
	// Set defaults if no values exist
	if (!get_option('itd-options'))
	{
		add_option('itd-options', $defaults);
	}
	else
	{
		$itd_options = get_option('itd-options');
		// Check to see if all defaults exists in option table's record, and assign values to empty keys
		foreach($defaults as $key => $val)
		{
			if (!$itd_options[$key])
			{
				$itd_options[$key] = $val;
				$new = true;
			}
		}
		if ($new)
		{
			update_option('itd-options', $itd_options);
		}
	}
}

function itd_register_buttons_actions()
{
	$itd_options = get_option('itd-options');
	$attributes = '';

	$lang_dir = array(
		//RTL Languages
		'ar' => 'rtl',
		//LTR Languages
		'en' => 'ltr'
	);

	$itd_element_default_attributes = array(
		'html' => array(
				'lang' => $itd_options['foreign_lang'],
				'dir' => $lang_dir[$itd_options['foreign_lang']]
			),
		'text/html' => array(
				'xml:lang' => $itd_options['foreign_lang'],
				'lang' => $itd_options['foreign_lang'],
				'dir' => $lang_dir[$itd_options['foreign_lang']]
			),
		'application/xhtml+xml' => array(
				'xml:lang' => $itd_options['foreign_lang'],
				'dir' => $lang_dir[$itd_options['foreign_lang']]
			)
		);

	$itd_element_attributes = $itd_element_default_attributes[$itd_options['presentation_type']];

	foreach ($itd_element_attributes as $attr => $value)
	{
		$attributes .= $attr . '="' . $value . '" ';
	}

?>
<script type="text/javascript">
//<![CDATA[
function itd_get_attributes()
{
	return '<?php echo $attributes; ?>';
}
//]]>
</script>
<?php
}

function itd_init()
{
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;
	itd_addbuttons();
	itd_default_options();
}

function itd_print_scripts()
{
	itd_quicktags();
	itd_register_buttons_actions();
}


// Adding Actions & Filters
add_action('admin_print_scripts', 'itd_print_scripts');
add_action('admin_init', 'itd_init');

add_filter('tiny_mce_version', 'itd_refresh_mce');
add_filter('mce_css', 'itd_tinymceCSS');

add_action('edit_form_advanced', 'itd_register_buttons_actions');
add_action('edit_page_form', 'itd_register_buttons_actions');

if ( is_admin() )
{
	add_action('admin_menu', 'itd_plugin_menu');
	add_action('admin_init', 'itd_register_settings' );
}