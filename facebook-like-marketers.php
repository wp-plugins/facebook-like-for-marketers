<?php

/*
 * Plugin Name:   Facebook Like for Marketers Plugin
 * Version:       1.0.0
 * Plugin URI:    http://www.danielwatrous.com/facebook-like-plugin-wordrpess
 * Description:   This plugin allows you to very easily add the facebook Like button to you WordPress blog either at the end of each post or as a sidebar widget.
 * Author:        Daniel Watrous
 * Author URI:    http://www.danielwatrous.com/
 */

// Template for like button with options for replacement
$like_url_template = <<<DDD
<iframe src="http://www.facebook.com/plugins/like.php?href=FBL_LIKED_URL&amp;layout=FBL_LAYOUT&amp;show_faces=FBL_SHOW_FACES&amp;width=FBL_WIDTH&amp;action=FBL_ACTION_TERM&amp;colorscheme=FBL_COLORS" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:FBL_WIDTHpx; height:FBL_HEIGHTpx"></iframe>
DDD;

/***
These are the customizable fields
FBL_LIKED_URL
FBL_LAYOUT
FBL_SHOW_FACES
FBL_WIDTH
FBL_HEIGHT
FBL_ACTION_TERM
FBL_COLORS
***/

// Make it work with WordPress before version 2.6
if (!defined('WP_CONTENT_URL')) {
   define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
}

// Footer link option
function facebook_like_footer_attribution() {
	if (get_option("facebook_like_attribution")) echo "<div style=\"text-align: center; font-size: 9pt;\">Powered by <a href=\"http://www.danielwatrous.com/facebook-like-plugin-wordrpess\">Facebook Like Button plugin for WordPress</a><div>";
}

function build_post_like_url () {
	if (is_single() || is_page()) {
		global $like_url_template;
		if (get_option("facebook_like_enabled")) {
			$post_like_url = $like_url_template;
			$post_like_url = str_replace ("FBL_LIKED_URL", urlencode(get_permalink()), $post_like_url);
			$post_like_url = str_replace ("FBL_LAYOUT", get_option('facebook_like_layout'), $post_like_url);
			$post_like_url = str_replace ("FBL_SHOW_FACES", get_option('facebook_like_show_faces'), $post_like_url);
			$post_like_url = str_replace ("FBL_WIDTH", get_option('facebook_like_width'), $post_like_url);
			$post_like_url = str_replace ("FBL_HEIGHT", get_option('facebook_like_height'), $post_like_url);
			$post_like_url = str_replace ("FBL_ACTION_TERM", get_option('facebook_like_action_term'), $post_like_url);
			$post_like_url = str_replace ("FBL_COLORS", get_option('facebook_like_colors'), $post_like_url);
		}
		return $post_like_url;
	} else return '';
}

// Main filter to add like link to bottom of each WordPress post/page
function facebook_like_filter($buffer) {
	return $buffer . build_post_like_url();
}

if (function_exists('add_action')) {
   // Add in the body
   add_filter('the_content', 'facebook_like_filter');

   // Add in the footer
   add_action('wp_footer', 'facebook_like_footer_attribution');
}

function facebook_like_activate() {
	if (!get_option('facebook_like_enabled')) add_option('facebook_like_enabled', 'on');
	if (!get_option('facebook_like_layout')) add_option('facebook_like_layout', 'standard');
	if (!get_option('facebook_like_show_faces')) add_option('facebook_like_show_faces', 'true');
	if (!get_option('facebook_like_width')) add_option('facebook_like_width', '450');
	if (!get_option('facebook_like_height')) add_option('facebook_like_height', '450');
	if (!get_option('facebook_like_action_term')) add_option('facebook_like_action_term', 'like');
	if (!get_option('facebook_like_colors')) add_option('facebook_like_colors', 'light');
	if (!get_option('facebook_like_attribution')) add_option('facebook_like_attribution', 'true');
}

register_activation_hook( __FILE__, 'facebook_like_activate' );

/***************************/
/*   SETTINGS MENU BELOW   */
/***************************/

if (!function_exists('is_vector')) {
   function is_vector( &$array ) {
      if ( !is_array($array) || empty($array) ) {
         return -1;
      }
      $next = 0;
      foreach ( $array as $k => $v ) {
         if ( $k !== $next ) return false;
         $next++;
      }
      return true;
   }
}

// Add the link to the settings page in the settings sub-header
function facebook_like_menu_setup() {
   add_options_page('Facebook Like Button Settings', 'Facebook Like Button', 10, __FILE__, 'facebook_like_menu');
   // Add meta box... you almost always want "post" here
   add_meta_box('facebook_like_meta', 'Facebook Like for Marketers', "facebook_like_meta", "post");
}

// Actual function that handles the settings sub-page
function facebook_like_menu() {
	global $like_url_template;
   ?>

<script language=javascript type='text/javascript'>
function toggleLayer( whichLayer )
{
  var elem, vis;
  if( document.getElementById ) // this is the way the standards work
    elem = document.getElementById( whichLayer );
  else if( document.all ) // this is the way old msie versions work
      elem = document.all[whichLayer];
  else if( document.layers ) // this is the way nn4 works
    elem = document.layers[whichLayer];
  vis = elem.style;
  // if the style.display value is blank we try to figure it out here
  if(vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
    vis.display = (elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
  vis.display = (vis.display==''||vis.display=='block')?'none':'block';
}

function ReplaceContentInContainer(id,content) {
	var container = document.getElementById(id);
	container.innerHTML = content;
}

function updateButton () {
	settingsForm = document.forms['facebook_likesettings'];
	URLTemplate = '<?php echo $like_url_template; ?>';
	URLTemplate = URLTemplate.replace("FBL_LIKED_URL", "<?php echo urlencode(get_permalink()); ?>");
	URLTemplate = URLTemplate.replace("FBL_LAYOUT", settingsForm.elements['facebook_like_layout'].options[settingsForm.elements['facebook_like_layout'].selectedIndex].value);
	URLTemplate = URLTemplate.replace("FBL_SHOW_FACES", settingsForm.elements['facebook_like_show_faces'].options[settingsForm.elements['facebook_like_show_faces'].selectedIndex].value);
	URLTemplate = URLTemplate.replace(/FBL_WIDTH/g, settingsForm.elements['facebook_like_width'].value);
	URLTemplate = URLTemplate.replace(/FBL_HEIGHT/g, settingsForm.elements['facebook_like_height'].value);
	URLTemplate = URLTemplate.replace("FBL_ACTION_TERM", settingsForm.elements['facebook_like_action_term'].options[settingsForm.elements['facebook_like_action_term'].selectedIndex].value);
	URLTemplate = URLTemplate.replace("FBL_COLORS", settingsForm.elements['facebook_like_colors'].options[settingsForm.elements['facebook_like_colors'].selectedIndex].value);
	ReplaceContentInContainer('sample_button', URLTemplate);
}
</script>

<style>
div#footer_attribution
{
	display: none;
}
</style>

	<style>
	.facebooklabel {
		display: block;
		width: 150px;
		float: left;
		text-align: right;
		margin: 2px 3px 0px 0px;
	}
	</style>

	<div class="wrap" style="width: 700px;">
	<h2>Facebook Like Button Settings</h2>

	<p>I wrote the Facebook Like Button plugin so you could easily include the Like button on all of your posts and pages.  There's also a widget component if you want more control over placement.  The settings below allow you to customize your button on your blog.</p>

	<p>I also make use of the facebook og meta tags.  What that means is that for every post and page you can adjust the title, site_name and thumbnail image that will appear in a news feed everytime someone clicks the Like/Recommend button.</p>

	<p><STRONG>HELP!</STRONG>  I made some HD videos demonstrating how to customize the plugin.  <a target="_blank" href="http://www.danielwatrous.com/facebook-like-plugin-wordrpess">Go there now.</a></p>

	<p>Best regards,<br>Daniel Watrous</p>

	<?php
	 /* These three items below must stay if you want to be able to easily save
		data in your settings pages. */
	?>
	<form name="facebook_likesettings" method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>
	<input type="hidden" name="action" value="update" />

	<?php
	/* You need to add each field in this area (separated by commas) that you want to update
	   every time you click "Save"
	*/
	?>
	<input type="hidden" name="page_options" value="facebook_like_enabled,facebook_like_layout,facebook_like_show_faces,facebook_like_width,facebook_like_action_term,facebook_like_colors,facebook_like_attribution,facebook_like_height,facebook_like_global_site_name" />

	<!-- ****************************************************** -->
	<h3>Settings</h3>

	<label>
	Enable plugin for all posts/pages: <?php facebook_like_checkbox("facebook_like_enabled", true); ?>
	<br />(You probably only want to disable the plugin for posts/pages if you're using the widget for a more customized approach.)
	</label>
	<br /><br />

	<label class="facebooklabel" for="facebook_like_global_site_name">Global site_name: </label><?php facebook_like_textbox("facebook_like_global_site_name", "", 50); ?>
	<br /><br />

	<label class="facebooklabel" for="facebook_like_height">Height: </label><?php facebook_like_textbox("facebook_like_height"); ?> (leave this blank for default height)
	<br /><br />

	<label class="facebooklabel" for="facebook_like_width">Width: </label><?php facebook_like_textbox("facebook_like_width", 450); ?>
	<br /><br />

	<label class="facebooklabel" for="facebook_like_show_faces">Show Faces: </label><?php facebook_like_dropdown("facebook_like_show_faces", array("true"=>"Yes", "false"=>"No"), 'true'); ?>  (Show profile pictures below the button)
	<br /><br />

	<label class="facebooklabel" for="facebook_like_layout">Layout: </label><?php facebook_like_dropdown("facebook_like_layout", array("standard","button_count"), "standard"); ?>
	<br /><br />

	<label class="facebooklabel" for="facebook_like_action_term">Verb to display: </label><?php facebook_like_dropdown("facebook_like_action_term", array("like","recommend"), "like"); ?>
	<br /><br />

	<label class="facebooklabel" for="facebook_like_colors">Color scheme: </label><?php facebook_like_dropdown("facebook_like_colors", array("light","dark","evil"), "light"); ?>
	<br /><br />

	<h3>Button Preview</h3>
	<div id="sample_button"><?php echo build_post_like_url(); ?></div>

	<!-- ****************************************************** -->
	<b>Optional settings</b> <a href="javascript:toggleLayer('footer_attribution');" title="Add a comment to this entry">+/-</a>

	<div id="footer_attribution">
	<br />
	<label>
	Show footer attribution: <?php facebook_like_checkbox("facebook_like_attribution"); ?>
	</label>
	<br /><br />
	</div>

	<?php
	 /* Keep the save button here, because people need to be able to click to
		save their changes! */
	?>
	<p><input type="submit" class="button" value="Update Settings" style="font-weight: bold;" /></p>
	</div>
	</form>


   <?php
}

// Display a meta box when people try to edit a single post/page...
function facebook_like_meta() {
	global $post;

	$facebook_like_title = get_post_meta($post->ID, 'facebook_like_title', true);
	$facebook_like_site_name = get_post_meta($post->ID, 'facebook_like_site_name', true);
	$facebook_like_image = get_post_meta($post->ID, 'facebook_like_image', true);
	?>
	<style>
	.facebooklabel {
		display: block;
		width: 150px;
		float: left;
		text-align: right;
		margin: 3px 3px 0px 0px;
	}
	</style>
	<p>If these values are left blank, appropriate values will be taken from the post values.</p>
	<p style="width: 500px;">
		<label class="facebooklabel" for="facebook_like_title">Title: </label><input type="text" id="facebook_like_title" name="facebook_like_title" value="<?php echo $facebook_like_title ?>" size="45"><br/>
		<label class="facebooklabel" for="facebook_like_site_name">Site Name: </label><input type="text" id="facebook_like_site_name" name="facebook_like_site_name" value="<?php echo $facebook_like_site_name ?>" size="45"><br/>
		<label class="facebooklabel" for="facebook_like_image">Image (include http://): </label><input type="text" id="facebook_like_image" name="facebook_like_image" value="<?php echo $facebook_like_image ?>" size="45"><br/>
	</p>

   <?php
}

// Save the meta fields...
function facebook_like_save_settings($postID, $post=NULL) {
   global $wpdb;

   if ($post == NULL) { return; }
   if (function_exists("wp_is_post_autosave") && wp_is_post_autosave($postID)) { return; }
   if (function_exists("wp_is_post_revision") && ($postRevision = wp_is_post_revision($postID))) {
      $postID = $postRevision;
   }

   // Save variables
   if (isset($_POST["facebook_like_title"])) {
      $variable = $_POST["facebook_like_title"];
      facebook_like_save($postID, "facebook_like_title", $variable);
   }
   if (isset($_POST["facebook_like_site_name"])) {
      $variable = $_POST["facebook_like_site_name"];
      facebook_like_save($postID, "facebook_like_site_name", $variable);
   }
   if (isset($_POST["facebook_like_image"])) {
      $variable = $_POST["facebook_like_image"];
      facebook_like_save($postID, "facebook_like_image", $variable);
   }
}

// Save the custom field for this post
function facebook_like_save($postID, $name, $value=null) {
   if ($value != null) {
      // Try to update the custom field or add it
      if (!update_post_meta($postID, $name, $value)) {
         add_post_meta($postID, $name, $value, true);
      }
   }
   else {
      // Delete the custom field if it's null
      delete_post_meta($postID, $name);
   }
}

// show the metatags in post
function facebook_like_show_tags () {
	global $post;

	if (is_single() || is_page()) {
		// Get the values that might be stored in the database
		$facebook_like_title = get_post_meta($post->ID, 'facebook_like_title', true);
		if ($facebook_like_title == '') echo '<meta property="og:title" content="'.get_the_title($post->ID).'"/>'."\n";
		else echo '<meta property="og:title" content="'.$facebook_like_title.'"/>'."\n";

		$facebook_like_site_name = get_post_meta($post->ID, 'facebook_like_site_name', true);
		if ($facebook_like_site_name == '') $facebook_like_site_name = get_option('facebook_like_global_site_name');
		if ($facebook_like_site_name) echo '<meta property="og:site_name" content="'.$facebook_like_site_name.'"/>'."\n";

		$facebook_like_image = get_post_meta($post->ID, 'facebook_like_image', true);
		if ($facebook_like_image != '') echo '<meta property="og:image" content="'.$facebook_like_image.'"/>'."\n";
	}

}

add_action ('wp_head', 'facebook_like_show_tags');

function facebook_like_dropdown($name, $data, $option="") {
   if (get_option($name)) { $option = get_option($name); }

   ?>
   <select id="<?php echo $name ?>" name="<?php echo $name ?>" onChange="updateButton ()">
   <?php

   // If the array is a vector (0, 1, 2...)
   if (is_vector($data)) {
      foreach ($data as $item) {
         if ($item == $option) {
            echo '<option selected="selected">' . $item . "</option>\n";
         }
         else {
            echo "<option>$item</option>\n";
         }
      }
   }

   // If the array contains name-value pairs
   else {
      foreach ($data as $value => $text) {
         if ($value == $option) {
            echo '<option value="' . $value . '" selected="selected">' . $text . "</option>\n";
         }
         else {
            echo '<option value="' . $value . '">' . "$text</option>\n";
         }
      }
   }

   ?>
   </select>
   <?php
}

function facebook_like_textbox($name, $value="", $size=15) {
   if (get_option($name)) { $value = get_option($name); }

   ?>
   <input type="text" id="<?php echo $name ?>" name="<?php echo $name ?>" size="<?php echo $size ?>" value="<?php echo $value ?>" onChange="updateButton ()"/>
   <?php
}

function facebook_like_radio($name, $values=array(), $selected=false, $include_break = false) {
   if (get_option($name)) { $selected = get_option($name); }
	foreach ($values as $option_name => $option_value) {
   ?>
   <?php echo $option_name; ?> <input type="radio" name="<?php echo $name ?>" value="<?php echo $option_value ?>" onChange="updateButton ()" <?php echo ($option_value==$selected) ? "checked":""; ?> />
   <?php echo ($include_break) ? "<br />":""; ?>
   <?php
	}
}

function facebook_like_colorpickertextbox($name, $value="", $size=15) {
   if (get_option($name)) { $value = get_option($name); }

   ?>
   <input type="text" class="color" name="<?php echo $name ?>" size="<?php echo $size ?>" value="<?php echo $value ?>" onChange="updateButton ()" />
   <?php
}

function facebook_like_textarea($name, $value="") {
   if (get_option($name)) { $value = get_option($name); }

   ?>
   <textarea name="<?php echo $name ?>" cols="80" rows="8" onChange="updateButton ()"><?php echo $value ?></textarea>
   <?php
}

/*
   Checkbox example: If the option is set, draw the checkbox as checked="checked" ...
   otherwise, just draw the regular checkbox.
*/
function facebook_like_checkbox($name) {
   ?>
   <?php if (get_option($name)): ?>
   <input type="checkbox" name="<?php echo $name ?>" onChange="updateButton ()" checked="checked" />
   <?php else: ?>
   <input type="checkbox" name="<?php echo $name ?>" onChange="updateButton ()" />
   <?php endif; ?>
   <?php
}

// Save settings on meta box
add_filter("wp_insert_post", "facebook_like_save_settings", 10, 2);

add_action('admin_menu', 'facebook_like_menu_setup');


/********************/
/*   WIDGET BELOW   */
/********************/

// make plugin function
function facebook_like_register_widgets() {
   register_sidebar_widget('Facebook Like Button', 'facebook_like_widget');
   // TODO change widget description

   // Comment this line out if you DON'T want to provide widget preferences
   register_widget_control('Facebook Like Button', 'facebook_like_widget_control');
}

// Example: Use custom fields to change the name of the widget
// Hint: Keep the title blank if you don't want a title in the sidebar
if (get_option("facebook_like_widget_title")) {
   $facebook_like_widget_title = get_option("facebook_like_widget_title");
} else {
	$facebook_like_widget_title = "Like this";
}
if (get_option("facebook_like_widget_width")) {
   $facebook_like_widget_width = get_option("facebook_like_widget_width");
} else {
	$facebook_like_widget_width = "300";
}
if (get_option("facebook_like_widget_height")) {
   $facebook_like_widget_height = get_option("facebook_like_widget_height");
} else {
	$facebook_like_widget_height = "300";
}
if (get_option("facebook_like_widget_layout")) {
	$facebook_like_widget_layout = get_option("facebook_like_widget_layout");
} else {
	$facebook_like_widget_layout = "standard";
}
if (get_option("facebook_like_widget_show_faces")) {
	$facebook_like_widget_show_faces = "true";
}
if (get_option("facebook_like_widget_action_term")) {
	$facebook_like_widget_action_term = get_option("facebook_like_widget_action_term");
} else {
	$facebook_like_widget_action_term = "like";
}
if (get_option("facebook_like_widget_colors")) {
	$facebook_like_widget_colors = get_option("facebook_like_widget_colors");
} else {
	$facebook_like_widget_colors = "light";
}

function facebook_like_widget($args) {
	global $like_url_template, $facebook_like_widget_title, $facebook_like_widget_width, $facebook_like_widget_height, $facebook_like_widget_layout, $facebook_like_widget_show_faces, $facebook_like_widget_action_term, $facebook_like_widget_colors;
	extract($args);

	// perform replacements based on user preferences
	$like_url = $like_url_template;
	$like_url = str_replace ("FBL_LIKED_URL", urlencode(get_permalink()), $like_url);
	$like_url = str_replace ("FBL_LAYOUT", get_option('facebook_like_widget_layout'), $like_url);
	$like_url = str_replace ("FBL_SHOW_FACES", get_option('facebook_like_widget_show_faces'), $like_url);
	$like_url = str_replace ("FBL_WIDTH", get_option('facebook_like_widget_width'), $like_url);
	$like_url = str_replace ("FBL_HEIGHT", get_option('facebook_like_widget_height'), $like_url);
	$like_url = str_replace ("FBL_ACTION_TERM", get_option('facebook_like_widget_action_term'), $like_url);
	$like_url = str_replace ("FBL_COLORS", get_option('facebook_like_widget_colors'), $like_url);

	// Output the actual widget...
	echo $before_widget;
	echo $before_title . $facebook_like_widget_title . $after_title;

	echo $like_url;

	echo $after_widget;
}

function facebook_like_widget_control() {
	global $like_url, $facebook_like_widget_title, $facebook_like_widget_width, $facebook_like_widget_height, $facebook_like_widget_layout, $facebook_like_widget_show_faces, $facebook_like_widget_action_term, $facebook_like_widget_colors;

	// Example on how to set custom fields in widgets...
	if (isset($_POST["facebook_like_widget_title"])) {
		update_option("facebook_like_widget_title", $_POST["facebook_like_widget_title"]);
	}
	if (isset($_POST["facebook_like_widget_width"])) {
		update_option("facebook_like_widget_width", $_POST["facebook_like_widget_width"]);
	}
	if (isset($_POST["facebook_like_widget_height"])) {
		update_option("facebook_like_widget_height", $_POST["facebook_like_widget_height"]);
	}
	if (isset($_POST['facebook_like_widget_layout'])) {
		update_option('facebook_like_widget_layout', $_POST['facebook_like_widget_layout']);
	}
	if (isset($_POST['facebook_like_widget_show_faces'])) update_option('facebook_like_widget_show_faces', "true");
	else update_option('facebook_like_widget_show_faces', "false");
	if (isset($_POST['facebook_like_widget_action_term'])) {
		update_option('facebook_like_widget_action_term', $_POST['facebook_like_widget_action_term']);
	}
	if (isset($_POST['facebook_like_widget_colors'])) {
		update_option('facebook_like_widget_colors', $_POST['facebook_like_widget_colors']);
	}

	// Output the code for administrators to make changes
	echo '<p><label>Title:</label> <input class="widefat" type="text" name="facebook_like_widget_title" value="' . get_option("facebook_like_widget_title") . '" /></p>';
	echo '<p><label>Width:</label> <input type="text" name="facebook_like_widget_width" value="' . get_option("facebook_like_widget_width") . '" /></p>';
	echo '<p><label>Height:</label> <input type="text" name="facebook_like_widget_height" value="' . get_option("facebook_like_widget_height") . '" /></p>';
	// below are all selects/checkboxes
	echo '<p><label>Show Faces:</label> <input type="checkbox" name="facebook_like_widget_show_faces" ' . ((get_option("facebook_like_widget_show_faces") == "true") ? "checked":"") . ' /></p>';
	echo '<p><label>Layout:</label> <select name="facebook_like_widget_layout"><option value="standard" ' . ((get_option("facebook_like_widget_layout") == "standard") ? "selected":"") . '>standard</option><option value="button_count" ' . ((get_option("facebook_like_widget_layout") == "button_count") ? "selected":"") . '>button_count</option></select></p>';
	echo '<p><label>Verb:</label> <select name="facebook_like_widget_action_term"><option value="like" ' . ((get_option("facebook_like_widget_action_term") == "like") ? "selected":"") . '>like</option><option value="recommend" ' . ((get_option("facebook_like_widget_action_term") == "recommend") ? "selected":"") . '>recommend</option></select></p>';
	echo '<p><label>Color scheme:</label>  <select name="facebook_like_widget_colors"><option value="light" ' . ((get_option("facebook_like_widget_colors") == "light") ? "selected":"") . '>light</option><option value="dark" ' . ((get_option("facebook_like_widget_colors") == "dark") ? "selected":"") . '>dark</option><option value="evil" ' . ((get_option("facebook_like_widget_colors") == "evil") ? "selected":"") . '>evil</option></select></p>';
}

if (function_exists('add_action')) {
   add_action('plugins_loaded', 'facebook_like_register_widgets');
}

?>