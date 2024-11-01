<?php
/*
Plugin Name: WordPress Theme Showcase Plugin
Plugin URI: http://webdevstudios.com/support/wordpress-plugins/
Description: Display themes located in wp-content/themes in a showcase gallery with theme screenshots and preview links.  Enter <strong>[showcase]</strong> in a post or page or call the php function <strong>wp_theme_preview()</strong> to display your theme screenshots and test run links.
Version: 1.7
Author: Brad Williams
Author URI: http://strangework.com/

Based on Preview Theme
Original Author: Ryan Boren
Original Author URI: http://boren.nu/
*/

/*

Example WordPress Theme Preview URI:
http://blog.wp/index.php?preview_theme=WordPress%20Default

*/

// Define current version
define( 'TS_VERSION', '1.7' );

//  Set this to the user level required to preview themes.
$preview_theme_user_level = 0;
// Set this to the name of the GET variable you want to use.
$preview_theme_query_arg = 'preview_theme';

//hook for adding admin menus
add_action('admin_menu', 'ts_menu');

function preview_theme_stylesheet($stylesheet) {
    global $user_level, $preview_theme_user_level, $preview_theme_query_arg;

    get_currentuserinfo();

    if ($user_level  < $preview_theme_user_level) {
        return $stylesheet;
    }

	If (isset($_GET[$preview_theme_query_arg])) {
	    $theme = $_GET[$preview_theme_query_arg];
	}

    if (empty($theme)) {
        return $stylesheet;
    }

    $theme = get_theme($theme);

    if (empty($theme)) {
        return $stylesheet;
    }

    return $theme['Stylesheet'];
}

function preview_theme_template($template) {
    global $user_level, $preview_theme_user_level, $preview_theme_query_arg;

    get_currentuserinfo();

    if ($user_level  < $preview_theme_user_level) {
        return $template;
    }

	If (isset($_GET[$preview_theme_query_arg])) {
	    $theme = $_GET[$preview_theme_query_arg];
	}

    if (empty($theme)) {
        return $template;
    }

    $theme = get_theme($theme);

    if (empty($theme)) {
        return $template;
    }

    return $theme['Template'];
}

function wp_theme_preview($content) {
If (!is_admin()) {
		//get themes
		$themes = get_themes();
	
		//get options
		$options = get_option('ts_themes');
		$disp_preview = get_option('ts_disp_preview');
		$disp_desc = get_option('ts_disp_desc');
		$form = '';
	
		if ( 1 < count($themes) ) {
		
			$theme_names = array_keys($themes);
			natcasesort($theme_names);
	
			foreach ($theme_names as $theme_name) {
				$template = $themes[$theme_name]['Template'];
				$stylesheet = $themes[$theme_name]['Stylesheet'];
				$title = $themes[$theme_name]['Title'];
				$version = $themes[$theme_name]['Version'];
				$description = $themes[$theme_name]['Description'];
				$author = $themes[$theme_name]['Author'];
				$screenshot = $themes[$theme_name]['Screenshot'];
				$stylesheet_dir = $themes[$theme_name]['Stylesheet Dir'];
				$template_dir = $themes[$theme_name]['Template Dir'];
				$tags = $themes[$theme_name]['Tags'];

				$screenshot_url = $themes[$theme_name]['Theme Root URI'] .'/' .$template .'/' .$screenshot;

				If (is_array($options)) {
					If (in_array($theme_name, $options)) {
						if ( $screenshot ) :
	
							//check if preview is displayed
							If ($disp_preview) {
								$form .= '<h3><a href="' . get_bloginfo('url') . '/?preview_theme=' . $theme_name . '" target="_blank">' .  $title . '</a></h3>';
								$form .= '<a href="' . get_bloginfo('url') . '/?preview_theme=' . $theme_name . '" target="_blank"><img src= "' . $screenshot_url . '" alt="" /></a>';
								$form .= '<p><a href="' . get_bloginfo('url') . '/?preview_theme=' . $theme_name . '" target="_blank">Preview Theme</a></p>';
							}Else{
								$form .= '<h3>' .  $title . '</h3>';
								$form .= '<img src= "' . $screenshot_url . '" alt="" />';
							}
							
							//check if description is displayed
							If ($disp_desc) {
								$form .= '<p>Description: '. $description . '</p>';
							}
							$form .= '<p></p>';
			
						endif;
					}
				}Else{
							//check if preview is displayed
							If ($disp_preview) {
								$form .= '<h3><a href="' . get_bloginfo('url') . '/?preview_theme=' . $theme_name . '" target="_blank">' .  $title . '</a></h3>';
								$form .= '<a href="' . get_bloginfo('url') . '/?preview_theme=' . $theme_name . '" target="_blank"><img src= "' . $screenshot_url . '" alt="" /></a>';
								$form .= '<p><a href="' . get_bloginfo('url') . '/?preview_theme=' . $theme_name . '" target="_blank">Preview Theme</a></p>';
							}Else{
								$form .= '<h3>' .  $title . '</h3>';
								$form .= '<img src= "' . $screenshot_url . '" alt="" />';
							}
							
							//check if description is displayed
							If ($disp_desc) {
								$form .= '<p>Description: '. $description . '</p>';
							}
							$form .= '<p></p>';
				}
	
			}
	
		}
		
		If ($content) {
			return str_replace('[showcase]', $form, $content);
		}Else{
			echo $form;
		}
	}
}

function ts_menu() {
  add_options_page('Theme Showcase Options', 'Theme Showcase', 8, __FILE__, 'ts_options');
}

//Function to save the plugin settings
function update_options()
{
	check_admin_referer('ts_check');
	
	//set variables
	If (isset($_POST['theme'])) {
		$themes = $_POST['theme'];
	}Else{
		$themes = '';
	}
	
	If (isset($_POST['disp_preview'])) {
		$disp_preview = $_POST['disp_preview'];
	}Else{
		$disp_preview = '';
	}
	
	If (isset($_POST['disp_desc'])) {
		$disp_desc = $_POST['disp_desc'];
	}Else{
		$disp_desc = '';
	}
	
	//save options
	update_option('ts_themes', $themes);
	update_option('ts_disp_preview', $disp_preview);
	update_option('ts_disp_desc', $disp_desc);
}

function ts_options() {

	if ( isset($_POST['update_ts_options'])
		&& $_POST['update_ts_options']
		)
	{
		update_options();

		echo "<div class=\"updated\">\n"
			. "<p>"
				. "<strong>"
				. __('Settings saved.')
				. "</strong>"
			. "</p>\n"
			. "</div>\n";
	}
	

?>
<H1>Theme Showcase Options</H1>
<?php
	//load themes
	$themes = get_themes();
	
	//load options
	$options = get_option('ts_themes');
	$disp_theme = get_option('ts_disp_preview');
	$disp_desc = get_option('ts_disp_desc');
	
	if ( 1 < count($themes) ) {

		$theme_names = array_keys($themes);
		natcasesort($theme_names);

		echo '<div class="theme-showcase"';
		echo '<p><strong>Themes to Display</strong></p>';
		echo '<form method="post">';
		if ( function_exists('wp_nonce_field') ) wp_nonce_field('ts_check');
		echo '<input type="hidden" name="update_ts_options" value="1">';
		echo '<div>';
		foreach ($theme_names as $theme_name) {

		If (is_array($options)) {
			If (in_array($theme_name, $options)) {
				$theme_checked = "CHECKED";	
			}Else{
				$theme_checked = "";	
			}
		}Else{
			$theme_checked = "";	
		}
			
			echo '<input type="checkbox" name="theme[]" value="'.$theme_name.'" '.$theme_checked.'> ' .$theme_name.'<BR>';

		}
		
		If ($disp_theme) {
			$disp_preview = "CHECKED";	
		}Else{
			$disp_preview = "";	
		}

		If ($disp_desc) {
			$disp_desc = "CHECKED";	
		}Else{
			$disp_desc = "";	
		}
				
		echo '* if no themes are selected ALL themes will be displayed';
		echo '<hr>';
		echo '<p><strong>Additional Options</strong></p>';
		echo '<p><input type="checkbox" name="disp_preview" '.esc_attr($disp_preview).'>&nbsp;Show Preview Link?';
		echo '<p><input type="checkbox" name="disp_desc" '.esc_attr($disp_desc).'>&nbsp;Show Description?';
		echo '<p class="submit">'
		. '<input type="submit"'
			. ' value="' . attribute_escape(__('Save Changes')) . '"'
			. ' /></p>';
		echo '</div>';
		echo '</form>';
		

	}

	echo '<p><a target="_blank" href="http://webdevstudios.com/support/forum/wordpress-theme-showcase-plugin/">Theme Showcase Plugin</a> v' .TS_VERSION .' - <a href="http://webdevstudios.com/support/forum/wordpress-theme-showcase-plugin/" target="_blank">Please Report Bugs</a> &middot; Follow us on Twitter: <a href="http://twitter.com/williamsba" target="_blank">Brad</a> &middot; <a href="http://twitter.com/webdevstudios" target="_blank">WDS</a></p>';
	echo '</div>';
	echo '</div>';
}

add_filter('stylesheet', 'preview_theme_stylesheet');
add_filter('template', 'preview_theme_template');
add_filter('the_content', 'wp_theme_preview');

register_activation_hook(__FILE__,'ts_install');

function ts_install() {
	//set default options when installed
	update_option('ts_disp_preview', 'on');
	update_option('ts_disp_desc', 'on');
}
?>