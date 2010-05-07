<?php
/**
 * Force apply terms and conditions
 *
 * @category      Wordpress Plugins
 * @package       Plugins
 * @author        Ramon Fincken
 * @copyright     Yes, Open source
 * @version       v 1.0
*/
if (!defined('ABSPATH'))
   die("Aren't you supposed to come here via WP-Admin?");

// We need DB connection
global $wpdb;

/**
 * If submiting the form
 */
if (isset ($_POST['submitbutton'])) {
   if (intval ($_POST['force_apply_post_id']) == 0) {
      echo '<div id="message" class="error">No page selected, create a post and then select it here</div>';
   } else {
      //Is magic quotes on?
      if (get_magic_quotes_gpc()) {
         // Yes? Strip the added slashes
         $_POST = array_map('stripslashes', $_POST);
      }

    update_option( 'plugin_force_apply_text', $_POST['force_apply_text']);
    update_option( 'plugin_force_apply_yes_text', $_POST['force_apply_yes_text']);
    update_option( 'plugin_force_apply_no_text', $_POST['force_apply_no_text']);
    update_option( 'plugin_force_apply_no_url', $_POST['force_apply_no_url']);
    update_option( 'plugin_force_apply_postid', intval($_POST['force_apply_post_id']));

      echo '<div id="message" class="updated fade">Your settings have been saved</div>';
   }
}

// Get values
$force_apply_text = get_option( 'plugin_force_apply_text', 'Do you apply to these rules and conditions?' );
$force_apply_yes_text = get_option( 'plugin_force_apply_yes_text', 'Yes I apply' );
$force_apply_no_text = get_option( 'plugin_force_apply_no_text', 'No I cannot apply' );
$force_apply_no_url = get_option( 'plugin_force_apply_no_url', 'http://www.google.com' );
$force_apply_postid = get_option( 'plugin_force_apply_postid', 0 );
?>

<br/>
<form id="form1" name="form1" method="post" action="" onsubmit="return confirm('Are you sure?')">
<table class="widefat">
   <thead>
   <tr>
      <th class="manage-column" style="width: 250px;">Option</th>
      <th class="manage-column">Setting</th>
   </tr>
   </thead>
   <tbody>
   <tr class="alternate iedit">
      <td>Apply question:</td>
      <td><input type="input" value="<?php echo $force_apply_text; ?>" name="force_apply_text" size="50"></td>
   </tr>
   <tr class="alternate">
      <td>Apply "YES" text:</td>
      <td><input type="input" value="<?php echo $force_apply_yes_text; ?>" name="force_apply_yes_text"></td>
   </tr>
   <tr class="alternate iedit">
      <td>Apply "NO" text:</td>
      <td><input type="input" value="<?php echo $force_apply_no_text; ?>" name="force_apply_no_text"></td>
   </tr>
   <tr class="alternate">
      <td>Apply "NO" url:</td>
      <td><input type="input" value="<?php echo $force_apply_no_url; ?>" name="force_apply_no_url" size="50"></td>
   </tr>
   <tr class="iedit iedit">
      <td>Which page has an explanation text to show the visitor?</td>
      <td><?php wp_dropdown_pages(array('exclude_tree' => 0, 'selected' => $force_apply_postid, 'name' => 'force_apply_post_id', 'show_option_none' => __('None'), 'sort_column'=> 'menu_order, post_title')); ?></td>
   </tr>
   </tbody>
</table>
<input type="submit" name="submitbutton" value="Update settings" class="button-primary"></form>
<h3>How to use?</h3>
<p class="updated">
* Create a page with explanation for your visitors<br />
* Update these settings
</p>

<h3>Plugin info</h3>
<p class="updated">
* Coding by <a href="http://www.RamonFincken.com" title="RamonFincken.com">RamonFincken.com</a><br/>
* Idea by <a href="http://www.MKBconnect.net" title="MKBconnect.net">MKBconnect.net</a>
</p>
