<?php
/*
Plugin Name: Votecount for Balatarin
Plugin URI: http://www.tafreevar.com/wordpress-plugins/votecount-for-balatarin
Description: The "Votecount for Balatarin" plugin shows the number of votes on "Balatarin.com" your posts get and allows users to vote it themselves.
Version: 0.1.1
Author: Tafreevar.Com
Author URI: http://www.tafreevar.com/
*/

/*  Copyright 2009  Tafreevar.Com  (email : tafreevar.weblog@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if (is_admin()) {
	add_action('admin_menu', 'bvc_options');
	add_action('admin_init', 'bvc_init');
	register_activation_hook(__FILE__, 'bvc_activate');
}

add_filter('the_content', 'bvc_update');
add_filter('get_the_excerpt', 'bvc_remove_filter', 9);

function bvc_options() {
	add_options_page('Votecount for Balatarin: Settings', 'Votecount for Balatarin', 8, 'votecount-for-balatarin', 'bvc_options_page');
}

// Register these variables (WP 2.7 & newer)
function bvc_init() {
	if (function_exists('register_setting')) {		
		register_setting('bvc-options', 'bvc_location');
		register_setting('bvc-options', 'bvc_style');
		register_setting('bvc-options', 'bvc_pages');
		register_setting('bvc-options', 'bvc_donate');
	}
}

// default options
function bvc_activate() {	
	add_option('bvc_location', 'top');
	add_option('bvc_style', 'float:right;margin-right:5px;margin-left:5px;');
	add_option('bvc_pages', 'true');
	add_option('bvc_donate', 'true');
}

function bvc_update($content) {
	global $post;
	
	if (get_option('bvc_location') == 'manual') {
		return $content;
	}
	
	if (is_feed()) {
		return $content;
	}
	
	if (is_page() and (get_option('bvc_pages') != 'true')) {
		return $content;
	}
	
	if (get_post_meta($post->ID, 'bvc', true) == '') {
		$button = balatarin_votecount();
		switch (get_option('bvc_location')) {
			case 'topbottom':
				return $button . $content . $button;
			break;
			case 'top':
				return $button . $content;
			break;
			case 'bottom':
				return $content . $button;
			break;
			default:
				return $button . $content;
			break;
		}
	} else {
		return $content;
	}
}

function bvc_remove_filter($content) {
	remove_action('the_content', 'bvc_update');
	return $content;
}

function bvc_options_page() {
	echo '<div class="wrap">';
	if (function_exists('screen_icon')) { screen_icon(); }
	echo'<h2>Votecount for Balatarin</h2>';
	echo '<form method="post" action="options.php">';
	wp_nonce_field('update-options');
	echo '<table class="form-table">';	
	echo '<tr valign="top"><th scope="row">Location</th><td><select name="bvc_location"><option value="top">top</option><option value="bottom"' . ((get_option('bvc_location')=='bottom')?' selected':'') . '>bottom</option><option value="topbottom"' . ((get_option('bvc_location')=='topbottom')?' selected':'') . '>top &amp; bottom</option><option value="manual"' . ((get_option('bvc_location')=='manual')?' selected':'') . '>manual</option></select> <span class="setting-description">For manual positioning, echo balatarin_votecount(); where you would like the button to appear in your template.</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Wrapper Style</th><td><input type="text" size="50" name="bvc_style" value="' . get_option('bvc_style') . '" /> <span class="setting-description">CSS for positioning, margins, etc</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Show Button on Pages</th><td><input type="checkbox" value="true" name="bvc_pages"' . ((get_option('bvc_pages')=='true')?' checked':'true') . ' /> <span class="setting-description">Show the button on Pages as well as Posts</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Donate please!</th><td><input type="checkbox" value="true" name="bvc_donate"' . ((get_option('bvc_donate')=='true')?' checked':'true') . ' /> <span class="setting-description">Giving a link wont kill! :)</span></td></tr>';
	echo '</table>';
	echo '<input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="bvc_location,bvc_style,bvc_pages,bvc_donate" /><p class="submit"><input type="submit" class="button-primary" value="Save Changes" /></p></form></div>';
}

function balatarin_votecount($style=null,$donate=null) {
	global $post;
	$url = '';
	$cnt = null;
	
	// let users override these vars when calling manually	
	$style = ($style === null) ? get_option('bvc_style') : $style;
	$donate = ($donate === null) ? get_option('bvc_donate') : $donate;
	
	if (get_post_status($post->ID) == 'publish') {
		$url = get_permalink();
		$title = $post->post_title;
		
		if ((function_exists('curl_init') || function_exists('file_get_contents')) && function_exists('unserialize')) {
			$meta = get_post_meta($post->ID, 'bvc_cache', true);
			if ($meta != '') {
				$pieces = explode(':', $meta);
				$timestamp = (int)$pieces[0];
				$cnt = (int)$pieces[1];
			}
			

		}
	}


	if ($style !== '') {
		$button = '<div style="' . $style . '">';
	} else {
		$button = '';
	}

	$bvcdir = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
	$bvcurl = get_post_meta($post->ID, 'bvcurl', 'true');
	
	$button = $button . '<iframe height="56" width="40" src="' . $bvcdir . 'bvc.php?';		
	
	$button .= 'url=' . $url . '&bvcurl=' . $bvcurl . '&title=' . wp_specialchars($title, '1');


 	if ($donate != 'true') {
		$button .= '&donate=0';
	} else {
		$button .= '&donate=1';		
	}
	 			
	$button .= '" frameborder="0" scrolling="no" allowtransparency="true"></iframe>';

	
	if ($style !== '') {
		$button .= '</div>';
	}
			 
	return $button;
}



function bvc_urlopen($url) {
	if (function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	} else {
		return file_get_contents($url);
	}
}

?>
