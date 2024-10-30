<?php
/*
Plugin Name: LAMP Version Checker
Plugin URI: http://wordpress.org/extend/plugins/lamp-version-checker/
Version: 0.7.1
Description: Show versions of server applications: WordPress, PHP, MySQL, WebServer.
Author: IKEDA Yuriko
Author URI: http://en.yuriko.net/
Text Domain: lamp-version-checker
Domain Path: /languages
*/

/*  Copyright (c) 2008-2010 IKEDA Yuriko

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( !defined('WP_INSTALLING') || !WP_INSTALLING ) :

/* ==================================================
 *   LampVersionChecker class
   ================================================== */

class LampVersionChecker {
	var $plugin_dir;
	var $text_domain = 'lamp-version-checker';
	var $domain_path = '/languages';
	var $textdomain_loaded = false;
	var $versions = array();

function LampVersionChecker() {
	return $this->__construct();
}

/* ==================================================
 * @param	none
 * @return	none
 * @since	0.7.0
 * @access	public
 */
function __construct() {
	global $wpdb, $wp_version;
	$this->plugin_dir = basename(dirname(__FILE__));
	$this->versions['WordPress'] = $wp_version;
	$this->versions['PHP'] = phpversion();
	$this->versions['MySQL'] = mysql_get_server_info($wpdb->dbh);
	$this->versions['WebServer'] = $_SERVER['SERVER_SOFTWARE'];
	add_action('plugins_loaded', array($this, 'load_textdomain'));
	add_filter('plugin_action_links', array($this, 'add_link'), 10, 2);
	add_action('admin_menu',  array($this, 'add_menu'));
	return;
}

/* ==================================================
 * @param	none
 * @return	none
 * @since	0.7.0
 * @access	public
 */
function load_textdomain() {
	if (! $this->textdomain_loaded) {
		$lang_dir = $this->plugin_dir . $this->domain_path;
		$plugin_path = defined('PLUGINDIR') ? PLUGINDIR . '/' : 'wp-content/plugins/';
		load_plugin_textdomain($this->text_domain, $plugin_path . $lang_dir, $lang_dir);
		$this->textdomain_loaded = true;
	}
}

/* ==================================================
 * @param	none
 * @return	none
 * @since	0.7.0
 * @access	public
 */
function add_link($links, $file) {
	if ( $file == plugin_basename(__FILE__) ) {
		array_unshift($links, '<a href="' . admin_url('admin.php?page=' . plugin_basename(__FILE__)) . '">' . __('View') . '</a>');
	}
	return $links;
}

/* ==================================================
 * @param	none
 * @return	none
 * @sinde   0.7.0
 * @access	public
 */
function add_menu() {
	$hookname = add_management_page(__('LAMP System Versions', 'lamp-version-checker'), __('LAMP Versions', 'lamp-version-checker'), 'manage_options', __FILE__, array($this, 'show_versions'));
	add_action('admin_print_styles-' . $hookname, array($this, 'add_style') );
}

/* ==================================================
 * @param	none
 * @return	none
 * @sinde   0.7.0
 * @access	public
 */
function add_style() { ?>
<style>pre.lamp-versions { border: 1px solid; background: white; padding: 0.5em; }</style>
<?php }

/* ==================================================
 * @param	none
 * @return	none
 * @sinde   0.7.0
 * @access	public
 */
function show_versions() {
?>
<div class="wrap">
<h2><?php _e('LAMP System Versions', 'lamp-version-checker'); ?></h2>
<p><?php _e('You can copy below infomation and submit at <a href="http://wordpress.org/support/">the support forums</a>.', 'lamp-version-checker'); ?></p>
<pre class="lamp-versions"><?php 
	foreach ($this->versions as $app => $vers) {
		printf("%s: %s\n", wp_specialchars($app), wp_specialchars($vers) );
	} ?>
</pre>
</div>
<?php
} 

// ===== End of class ====================
}

$LampVersionChecker = new LampVersionChecker();
endif;
?>