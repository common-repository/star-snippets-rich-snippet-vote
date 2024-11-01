<?php
/*
Plugin Name: Star Snippets - Rich Snippet Vote
Plugin URI: http://www.star-snippets.com
Description: Provides rich snippet rating/voting functionality for every content of a page. You can customize size, orientation and colors of the rating/voting.
Version: 1.3
Author: 1sr
Author URI: http://www.1sr.de
Update Server: http://www.star-snippets.com/downloads/wp/
*/

/*  Copyright 2013 1sr  (email : info@1sr.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class StarSnippets
{
	// Constructor
	function __construct() {		
		add_action('wp_footer', array(&$this, 'star_snippets_content'));
		add_filter('the_content', array(&$this, 'star_snippets_replacement'));
		add_action('admin_menu', array(&$this, 'my_plugin_menu'));
	}
	
	// PHP4 Constructor
	function StarSnippets() {
		$this->__construct();
	}
	
	function my_plugin_menu() {
		add_options_page('Star Snippets Options', 'Star Snippets', 'manage_options', 'star-snippets-plugin-options', array(&$this, 'star_snippets_plugin_options'));
	}

	function star_snippets_plugin_options() {
		if (!current_user_can('manage_options' )) {
			wp_die( __('You do not have sufficient permissions to access this page.'));
		}
		
		// variables for the field and option names
		$hidden_field_name = 'star_snippets_submit_hidden';
		
		$snippet_opt_name = 'star_snippets_snippet';		
		$snippet_data_field_name = 'star_snippets_snippet';
		
		$size_opt_name = 'star_snippets_size';		
		$size_data_field_name = 'star_snippets_size';
		
		$orientation_opt_name = 'star_snippets_orientation';		
		$orientation_data_field_name = 'star_snippets_orientation';
		
		$empty_color_opt_name = 'star_snippets_empty_color';		
		$empty_color_data_field_name = 'star_snippets_empty_color';
		
		$fill_color_opt_name = 'star_snippets_fill_color';		
		$fill_color_data_field_name = 'star_snippets_fill_color';
		
		$hover_color_opt_name = 'star_snippets_hover_color';		
		$hover_color_data_field_name = 'star_snippets_hover_color';
		
		$show_rating_opt_name = 'star_snippets_show_rating';		
		$show_rating_data_field_name = 'star_snippets_show_rating';
		
		$request_function_opt_name = 'star_snippets_request_function';		
		$request_function_data_field_name = 'star_snippets_request_function';
		
		$request_timeout_opt_name = 'star_snippets_request_timeout';
		$request_timeout_data_field_name = 'star_snippets_request_timeout';
		
		$provide_snippets_opt_name = 'star_snippets_provide_snippets';		
		$provide_snippets_data_field_name = 'star_snippets_provide_snippets';

		// Read in existing option value from database
		$snippet_opt_value = (get_option($snippet_opt_name) != "") ? get_option($snippet_opt_name): "{star-snippet}";
		
		$size_opt_value = (get_option($size_opt_name) != "") ? get_option($size_opt_name): "m";
		
		$orientation_opt_value = (get_option($orientation_opt_name) != "") ? get_option($orientation_opt_name): "horizontal";
		
		$empty_color_opt_value = (get_option($empty_color_opt_name) != "") ? get_option($empty_color_opt_name): "efefef";
		
		$fill_color_opt_value = (get_option($fill_color_opt_name) != "") ? get_option($fill_color_opt_name): "ffcc66";
		
		$hover_color_opt_value = (get_option($hover_color_opt_name) != "") ? get_option($hover_color_opt_name): "ee9900";
		
		$show_rating_opt_value = (get_option($show_rating_opt_name) != "") ? get_option($show_rating_opt_name): "true";
		
		$request_function_opt_value = (get_option($request_function_opt_name) != "") ? get_option($request_function_opt_name): "file_get_contents";
		
		$request_timeout_opt_value = (get_option($request_timeout_opt_name) != "") ? get_option($request_timeout_opt_name): 10;
		
		$provide_snippets_opt_value = (get_option($provide_snippets_opt_name) != "") ? get_option($provide_snippets_opt_name): "only_if_snippet_exists";

		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
			// Read their posted value
			$snippet_opt_value = $_POST[$snippet_data_field_name];
			$size_opt_value = $_POST[$size_data_field_name];
			$orientation_opt_value = $_POST[$orientation_data_field_name];
			$empty_color_opt_value = $_POST[$empty_color_data_field_name];
			$fill_color_opt_value = $_POST[$fill_color_data_field_name];
			$hover_color_opt_value = $_POST[$hover_color_data_field_name];
			$show_rating_opt_value = $_POST[$show_rating_data_field_name];
			$request_function_opt_value = $_POST[$request_function_data_field_name];
			$request_timeout_opt_value = $_POST[$request_timeout_data_field_name];
			$provide_snippets_opt_value = $_POST[$provide_snippets_data_field_name];

			// Save the posted value in the database
			update_option($snippet_opt_name, $snippet_opt_value);
			
			update_option($size_opt_name, $size_opt_value);
			
			update_option($orientation_opt_name, $orientation_opt_value);
			
			update_option($empty_color_opt_name, $empty_color_opt_value);
			
			update_option($fill_color_opt_name, $fill_color_opt_value);
			
			update_option($hover_color_opt_name, $hover_color_opt_value);
			
			update_option($show_rating_opt_name, $show_rating_opt_value);
			
			update_option($request_function_opt_name, $request_function_opt_value);
			
			update_option($request_timeout_opt_name, $request_timeout_opt_value);
			
			update_option($provide_snippets_opt_name, $provide_snippets_opt_value);

			// Put an settings updated message on the screen

			?>
			<div class="updated"><p><strong><?php _e('settings saved.', 'star-snippets' ); ?></strong></p></div>
			<?php

		}
		
		// Now display the settings editing screen

		echo '<div class="wrap">';

		// header

		echo "<h2>" . __('Star Snippets Options', 'star-snippets') . "</h2>";

		// settings form
		
		?>

		<form name="form" method="post" action="">
			<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
			<table class="form-table">

				<tr>
					<td><?php _e("Snippet:", 'star-snippets'); ?></td>
					<td>
						<input type="text" name="<?php echo $snippet_data_field_name; ?>" value="<?php echo $snippet_opt_value; ?>" size="20">
						<p class="description"><?php _e("Code snippet, which would be used in content.", 'star-snippets'); ?></p>
					</td>
				</tr>
				<tr>
					<td><?php _e("Size:", 'star-snippets'); ?></td>
					<td>
						<select name="<?php echo $size_data_field_name; ?>">
							<option value="xs"<?php echo ($size_opt_value == "xs") ? " selected" : ""; ?>>xs</option>
							<option value="s"<?php echo ($size_opt_value == "s") ? " selected" : ""; ?>>s</option>
							<option value="m"<?php echo ($size_opt_value == "m") ? " selected" : ""; ?>>m</option>
							<option value="l"<?php echo ($size_opt_value == "l") ? " selected" : ""; ?>>l</option>
							<option value="xl"<?php echo ($size_opt_value == "xl") ? " selected" : ""; ?>>xl</option>
						</select>
						<p class="description"><?php _e("Please choose a size!", 'star-snippets'); ?></p>
					</td>
				</tr>
				<tr>
					<td><?php _e("Orientation:", 'star-snippets'); ?></td>
					<td>
						<select name="<?php echo $orientation_data_field_name; ?>">
							<option value="horizontal"<?php echo ($orientation_opt_value == "horizontal") ? " selected" : ""; ?>>horizontal</option>
							<option value="vertical"<?php echo ($orientation_opt_value == "vertical") ? " selected" : ""; ?>>vertical</option>		
						</select>
						<p class="description"><?php _e("Please choose an orientation!", 'star-snippets'); ?></p>
					</td>
				</tr>
				<tr>
					<td><?php _e("Color of empty stars:", 'star-snippets'); ?></td>
					<td>
						<input type="text" name="<?php echo $empty_color_data_field_name; ?>" value="<?php echo $empty_color_opt_value; ?>" size="6">
						<p class="description"><?php _e("Please choose a color of the empty stars!", 'star-snippets'); ?></p>
					</td>
				</tr>
				<tr>
					<td><?php _e("Color of filled stars:", 'star-snippets'); ?></td>
					<td>
						<input type="text" name="<?php echo $fill_color_data_field_name; ?>" value="<?php echo $fill_color_opt_value; ?>" size="6">
						<p class="description"><?php _e("Please choose a color of the filled stars!", 'star-snippets'); ?></p>
					</td>
				</tr>
				<tr>
					<td><?php _e("Color of mouseover stars:", 'star-snippets'); ?></td>
					<td>
						<input type="text" name="<?php echo $hover_color_data_field_name; ?>" value="<?php echo $hover_color_opt_value; ?>" size="6">
						<p class="description"><?php _e("Please choose a color of the mouseover stars!", 'star-snippets'); ?></p>
					</td>
				</tr>
				<tr>
					<td><?php _e("Show Rating:", 'star-snippets'); ?></td>
					<td>
						<select name="<?php echo $show_rating_data_field_name; ?>">
							<option value="true"<?php echo ($show_rating_opt_value == "true") ? " selected" : ""; ?>>yes</option>
							<option value="false"<?php echo ($show_rating_opt_value == "false") ? " selected" : ""; ?>>no</option>		
						</select>
						<p class="description"><?php _e("Please choose if the Rating should be shown.", 'star-snippets'); ?></p>
					</td>
				</tr>
				<tr>
					<td><?php _e("Request function:", 'star-snippets'); ?></td>
					<td>
						<select name="<?php echo $request_function_data_field_name; ?>">
							<option value="file_get_contents"<?php echo ($request_function_opt_value == "file_get_contents") ? " selected" : ""; ?>>file_get_contents</option>
							<option value="curl_exec"<?php echo ($request_function_opt_value == "curl_exec") ? " selected" : ""; ?>>curl_exec</option>		
						</select>
						<p class="description"><?php _e("Because not every hoster supports all functions (modules), you can choose which function should be used.s", 'star-snippets'); ?></p>
					</td>
				</tr>
				<tr>
					<td><?php _e("Request timeout:", 'star-snippets'); ?></td>
					<td>
						<input type="text" name="<?php echo $request_timeout_data_field_name; ?>" value="<?php echo $request_timeout_opt_value; ?>" size="6">
						<p class="description"><?php _e("Please choose a timeout for the star-snippets request (in seoconds)!", 'star-snippets'); ?></p>
					</td>
				</tr>
				<tr>
					<td><?php _e("Provide rating:", 'star-snippets'); ?></td>
					<td>
						<select name="<?php echo $provide_snippets_data_field_name; ?>">
							<option value="only_if_snippet_exists"<?php echo ($provide_snippets_opt_value == "only_if_snippet_exists") ? " selected" : ""; ?>><?php _e("Only if a snippet was found in content element", 'star-snippets'); ?></option>
							<option value="content_element_top"<?php echo ($provide_snippets_opt_value == "content_element_top") ? " selected" : ""; ?>><?php _e("Top in every content element", 'star-snippets'); ?></option>
							<option value="content_element_bottom"<?php echo ($provide_snippets_opt_value == "content_element_bottom") ? " selected" : ""; ?>><?php _e("Bottom in every content element", 'star-snippets'); ?></option>
							<option value="content_element_top_and_bottom"<?php echo ($provide_snippets_opt_value == "content_element_top_and_bottom") ? " selected" : ""; ?>><?php _e("Top and bottom in every content element", 'star-snippets'); ?></option>
						</select>
						<p class="description"><?php _e("Specify where a rating should be integrated.", 'star-snippets'); ?></p>
					</td>
				</tr>
			</table>
			
			<hr />

			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>

		</form>
		</div>

		<?php
		
	}

	function star_snippets_replacement($content) {
	
		$snippet_opt_name = 'star_snippets_snippet';
		$timeout_opt_name = 'request_timeout';
		$size_opt_name = 'star_snippets_size';		
		$orientation_opt_name = 'star_snippets_orientation';		
		$empty_color_opt_name = 'star_snippets_empty_color';		
		$fill_color_opt_name = 'star_snippets_fill_color';		
		$hover_color_opt_name = 'star_snippets_hover_color';		
		$show_rating_opt_name = 'star_snippets_show_rating';
		$provide_snippets_opt_name = 'star_snippets_provide_snippets';
	
		$snippet = (get_option($snippet_opt_name) != "") ? get_option($snippet_opt_name): "{star-snippet}";
		
		$timeout = (get_option($request_timeout_opt_name) != "" && is_int(get_option($request_timeout_opt_name))) ? is_int(get_option($request_timeout_opt_name)) : 10;
		
		$size = (get_option($size_opt_name) != "") ? get_option($size_opt_name): "m";
		
		$orientation = (get_option($orientation_opt_name) != "") ? get_option($orientation_opt_name): "horizontal";
		
		$emptyColor = (get_option($empty_color_opt_name) != "") ? " emptyColor=\"".get_option($empty_color_opt_name)."\"" : "";
		
		$fillColor = (get_option($fill_color_opt_name) != "") ? " fillColor=\"".get_option($fill_color_opt_name)."\"" : "";
		
		$hoverColor = (get_option($hover_color_opt_name) != "") ? " hoverColor=\"".get_option($hover_color_opt_name)."\"" : "";
		
		$showRating = (get_option($show_rating_opt_name) == "true") ? "true" : "false";
		
		$provideSnippets = get_option($provide_snippets_opt_name);
		
		if($provideSnippets == "content_element_top")
		{
			$content = $snippet.$content;
		}
		else if($provideSnippets == "content_element_bottom")
		{
			$content = $content.$snippet;
		}
		else if($provideSnippets == "content_element_top_and_bottom")
		{
			$content = $snippet.$content.$snippet;
		}
		
		$starSnippetTag = "<star-snippet"
			." href=\"".get_permalink()."\""
			." size=\"".$size."\""
			." orientation=\"".$orientation."\""
			.$emptyColor
			.$fillColor
			.$hoverColor
			." showRating=\"".$showRating."\""
			.">"
			."</star-snippet>";
		
		return str_replace($snippet, $starSnippetTag, $content);
	}

	function star_snippets_content() {


		$domain = "http://www.star-snippets.com";
			//"http://localhost/starsnippets/public";
		
		//url Parameter sollte mit dem href Parameter des Javascript Codes übereinstimmen    
		$url = $domain."/rating?url=".get_permalink();
				
		$title = get_the_title();
		
		if(is_single() || is_page())
		{
			$description = strip_tags($post->post_content);
		}
		elseif(is_category())
		{
			$description = category_description();
		}
		else
		{
			$description = get_bloginfo('description');
		}

		$description = substr($description,0,150);
		
		if($title != "")
		{
		  $url .= "&name=".rawurlencode($title);
		}
		
		if($description != "")
		{
		  $url .= "&description=".rawurlencode($description);
		}
		
		$request_function_opt_name = 'star_snippets_request_function';	
		
		if(get_option($request_function_opt_name) == "file_get_contents")
		{
			try {
				$streamContext = stream_context_create(array('http' => array('timeout' => $timeout)));
	
				$response = file_get_contents($url, $streamContext);

				if (!$response)
				{}
				else
				{
					echo $response;
				}
			} catch (Exception $ex) {
			}
		}
		else
		{
			try {
				$session = curl_init($url);
				curl_setopt($session, CURLOPT_HEADER, false); //no header
				curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($session, CURLOPT_TIMEOUT, $timeout);
				$response = curl_exec($session);
				curl_close($session);

				if (!$response)
				{}
				else
				{
					echo $response;
				}
			} catch (Exception $ex) {
			}
		}
		
		$jsCode = "\n"
			."<script>\n"
				."(function() {\n"
					."function async_load(){\n"						
						."var s = document.createElement('script');\n"
						."s.type = 'text/javascript';\n"
						."s.async = true;\n"
						."s.src = '".$domain."/script';\n"
						."//s.src = '".$domain."/script?prevent_cache=' + new Date().getTime(); // prevent caching\n"
						."var x = document.getElementsByTagName('script')[0];\n"
						."x.parentNode.insertBefore(s, x);\n"
					."}\n"
					."if (window.attachEvent) {\n"
						."window.attachEvent('onload', async_load);\n"
					."} else {"
						."window.addEventListener('load', async_load, false);}\n"
				."})();\n"
			."</script>\n";
			
		echo $jsCode;
	}

}

$StarSnippets = new StarSnippets;

?>