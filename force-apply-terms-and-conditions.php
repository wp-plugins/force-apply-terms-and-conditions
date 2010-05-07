<?php
/*
Plugin Name: Force apply terms and conditions
Plugin URI: http://www.websitefreelancers.nl
Description: Class to show Terms and Conditions to every visitor upon first visit. Visitor can apply (YES) or leave the website.
Author: Ramon Fincken
Version: 1.0
Author URI: http://www.websitefreelancers.nl
 * @author  Ramon Fincken
 */

// Are we on frontend?
if (!defined('WP_ADMIN')) {
	add_action('init', 'plugin_force_apply_frontend');
}
else
{
	if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");
	add_action('admin_menu', 'plugin_force_apply_backend');
}


function plugin_force_apply_backend()
{
	add_submenu_page("plugins.php", "Force apply terms &amp; conditions", "Force apply t&amp;c", 10, __FILE__, 'plugin_force_apply_backend_initpage');	
}

function plugin_force_apply_backend_initpage()
{
	include('form.php');
}

/**
 * Initiates plugin object upon frontend init
 */
function plugin_force_apply_frontend() {
	$force_apply = new plugin_force_apply();

	// Make the use of sessions possible.
	if (!session_id() && !isset ($_SESSION)) {
		session_start();
	}

	if (isset ($_GET['force_apply']) && isset ($_GET['force_apply_unique']) && isset ($_SESSION['force_apply_rnd'])) {
		if ($_GET['force_apply']) {
			if ($_GET['force_apply_unique'] == $_SESSION['force_apply_rnd']) {
				$_SESSION['force_apply_has_applied'] = true;
			}
		}
	}
	
	// Do the check
	if ($force_apply->has_applied()) {
		// Void, user may pass
	} else {
		// Show confirm page
		$force_apply->show_confirm();
	}
}

class plugin_force_apply {

	/**
	 * Shows apply page
	 */
	function show_confirm() {
		global $wpdb;

		get_header();
		echo '<div id="content" class="narrowcolumn" role="main">';

		$force_apply_text = get_option( 'plugin_force_apply_text', 'Do you apply to these rules and conditions?' );
		$force_apply_yes_text = get_option( 'plugin_force_apply_yes_text', 'Yes I apply' );
		$force_apply_no_text = get_option( 'plugin_force_apply_no_text', 'No I cannot apply' );
		$force_apply_no_url = get_option( 'plugin_force_apply_no_url', 'http://www.google.com' );
		$force_apply_postid = get_option( 'plugin_force_apply_postid', 0 );

		$apply_post_page = $force_apply_postid;
		// Taken from wp-includes/post.php
		$post = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", $apply_post_page));
		// Show content		
		echo $post->post_content;

		echo '<p>&nbsp;</p>';

		// Show apply question, yes/no-url
		echo '<p><hr/>'.$force_apply_text.'<br/><a href="' . $this->getApplyAddress() . '">'.$force_apply_yes_text.'</a>&nbsp;<a href="'.$force_apply_no_url.'">'.$force_apply_no_text.'</a></p>';

		echo '</div> <!-- /content -->';
		get_footer();

		// Stop from processing anymore
		die();
	}

	/**
	 * Gets current url and adds apply GET param
	 */
	function getApplyAddress() {
		if (!isset ($_SESSION['force_apply_rnd'])) {
			// Some random string
			$_SESSION['force_apply_rnd'] = md5(microtime());
		}

		$url = $this->getAddress();
		$backfix = 'force_apply=true&force_apply_unique=' . $_SESSION['force_apply_rnd'];

		$pos = strpos($url, '?');
		if ($pos === false) {
			// No ? found
			$url .= '?' . $backfix;
		} else {
			// ? was found, add a &
			$url .= '&' . $backfix;
		}

		return $url;
	}

	/**
	 * Utility function to get the full address of the current request - credit: http://www.phpro.org/examples/Get-Full-URL.html
	 */
	function getAddress() {
		if (!isset ($_SERVER['HTTPS'])) {
			$_SERVER['HTTPS'] = '';
		}
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http'; //check for https
		return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; //return the full address
	}

	/**
	 * Has user applied this session?
	 */
	function has_applied() {
		if (isset($_SESSION['force_apply_has_applied']) && $_SESSION['force_apply_has_applied']) {
			return true;
		}
		return false;
	}
}
?>