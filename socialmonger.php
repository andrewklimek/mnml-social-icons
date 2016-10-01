<?php
/*
Plugin Name: Social Monger
Plugin URI:  https://github.com/andrewklimek/socialmonger/
Description: crisp and light (official) social media icons (embeds SVG code for fast loading and vector rendering)
Version:     1.3.0
Author:      Andrew J Klimek
Author URI:  https://readycat.net
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Social Monger is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by the Free 
Software Foundation, either version 2 of the License, or any later version.

Social Monger is distributed in the hope that it will be useful, but without 
any warranty; without even the implied warranty of merchantability or fitness for a 
particular purpose. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with 
Social Monger. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


add_shortcode( 'socialmonger', 'socialmonger' );

/***
*
* [socialmonger] shortcode.
* place one social link perline between opening and closing shorcode.
* Links should start with protocol (probably https) but we will add them if you forget.
*
* supported sites: twitter, facebook, instagram, pinterest, youtube, google plus, linkedin
*
* Shortcode options:
*
* size - include units (refault is 2em)
* color - include the # or use rgba or whatever
* padding - how much space around icons. default is 0 1ex (no padding above and below, 1ex left and right)
* align - center, left, right, inline (default)
* 
* if you leave any of these empty in a secondary instance, they will pull the attributes from the first shortcode on the page.
*
* Each instance gets an incremental number in the ID so you can target for further customization
*  
**/


function socialmonger( $a, $c ) {
	
	// Check to see if this is the first shortcode
	$idno = 1 + wp_cache_get( 'socialmonger_id' );
	wp_cache_set('socialmonger_id', $idno );
	
	$out = "<aside id='socialmonger-{$idno}' class='socialmonger'>";
	
	// initial style, only print once
	if ( $idno === 1 ) {
		
		$size = !empty( $a['size'] ) ? $a['size'] : "2rem";
		$color = !empty( $a['color'] ) ? $a['color'] : "#777";
		$padding = !empty( $a['padding'] ) ? $a['padding'] : "0 1ex";
		
		if ( empty( $a['align'] ) || 'inline' === $a['align'] ) $align = "display: inline-table;";
		elseif ( 'center' === $a['align'] ) $align = "display: table;margin-left: auto;margin-right: auto;";
		elseif ( 'left' === $a['align'] ) $align = "display: table;margin-right: auto;";
		elseif ( 'right' === $a['align'] ) $align = "display: table;margin-left: auto;";
		
		$out .= "
		<style>
		.socialmonger {padding: 0;$align}
		.socialmonger a {text-decoration: none;}
		.socialmonger > span {display: table-cell;vertical-align: middle; padding: {$padding};}
		.socialmonger svg {display: block;width: {$size};height: {$size};fill: {$color};}
		</style>";
		
	} elseif ( $a ) {// subsequent styles, for second instances on same page, only run if any attributes exist
		
		$out .= "<style>#socialmonger-{$idno} svg {";
		if ( !empty( $a['size'] ) )
			$out .= "width: {$a['size']};height: {$a['size']};";
		if ( !empty( $a['color'] ) )
			$out .= "fill: {$a['color']};";
		if ( !empty( $a['padding'] ) )
			$out .= "} #socialmonger-{$idno} > span {padding: {$a['padding']};";
		if ( !empty( $a['align'] ) ) {
			if ( 'inline' === $a['align'] ) $align = "display: inline-table;";
			elseif ( 'center' === $a['align'] ) $align = "display: table;margin-left: auto;margin-right: auto;";
			elseif ( 'left' === $a['align'] ) $align = "display: table;margin-right: auto;";
			elseif ( 'right' === $a['align'] ) $align = "display: table;margin-left: auto;";
			$out .= "} #socialmonger-{$idno} {{$align}";
		}
		$out .= "}</style>";
	
	}
	
	$lines = array_filter( explode( "\n", $c ) );
	
	foreach ( $lines as $line ) {
		
			// check for scheme and add if missing. preserve original $line in case it's a custom html (else block at the end)
			$link = ( false === strpos( $line, '//' ) ) ? '//' . $line : $line;

		
		if ( false !== stripos( $link, 'twitter' ) ) {
			$link = strip_tags($link);
			$out .= "
				<span><a href='{$link}' rel='nofollow' target='_blank'>
					<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 100 100'><path d='M91.6 28.8c-3 1.3-6 2-9 2.5 3-2 5.7-5 7-8.8-3.2 1.8-6.6 3-10.2 4-3-3.2-7-5.2-11.7-5.2-8.7 0-16 7.2-16 16 0 1.2.3 2.4.6 3.6C39 40 27.3 34 19.5 24c-1.4 2.4-2.2 5-2.2 8 0 5.6 2.8 10.4 7 13.3-2.5 0-5-.8-7-2v.2c0 7.7 5.4 14 12.6 15.6-1.4.4-2.8.5-4.2.5-1 0-2 0-3-.2 2 6.3 8 11 14.8 11-5.4 4.3-12.3 6.8-19.7 6.8-1.4 0-2.7 0-4-.2 7 4.5 15.5 7 24.5 7 29.2 0 45.2-24 45.2-45V37c3-2.2 5.8-5 8-8.2'/></svg>
				</a></span>";
		} elseif ( false !== stripos( $link, 'facebook' ) ) {
			$link = strip_tags($link);
			$out .= "
				<span><a href='{$link}' rel='nofollow' target='_blank'>
					<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 100 100'><path d='M80.3 16H19.8c-2 0-3.8 1.7-3.8 3.8v60.4c0 2 1.7 3.8 3.8 3.8h32.5V57.7h-8.8V47.4h8.8v-7.6c0-8.8 5.4-13.5 13.2-13.5 3.8 0 7 .2 8 .4V36H68c-4.3 0-5 2-5 4.8v6.6h10L72 57.7h-9V84h17.5c2 0 3.7-1.7 3.7-3.8V19.8c0-2-1.7-3.7-3.8-3.7'/></svg>
				</a></span>";
		} elseif ( false !== stripos( $link, 'instagram' ) ) {
			$link = strip_tags($link);
			$out .= "
				<span><a href='{$link}' rel='nofollow' target='_blank'>
					<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 100 100'><path d='M50 32.5c-9.6 0-17.5 8-17.5 17.5 0 9.6 8 17.5 17.5 17.5 9.6 0 17.5-8 17.5-17.5 0-9.6-8-17.5-17.5-17.5m0 28.8c-6.3 0-11.3-5-11.3-11.3 0-6.3 5-11.3 11.3-11.3 6.3 0 11.3 5 11.3 11.3 0 6.3-5 11.3-11.3 11.3zM72.2 32c0 2-1.8 4-4 4-2.3 0-4-2-4-4s1.7-4.2 4-4.2c2.2 0 4 1.8 4 4M50 22.2c9 0 10.2.2 13.7.3 3.4.2 5.2.7 6.4 1.2 1.6.6 2.8 1.3 4 2.5 1.2 1.2 2 2.4 2.5 4 .5 1 1 3 1.2 6.3 0 3.5.2 4.6.2 13.7 0 9-.2 10.2-.3 13.7-.2 3.4-.7 5.2-1.2 6.3-.6 1.6-1.3 2.8-2.5 4-1.2 1-2.4 2-4 2.5-1 .5-3 1-6.3 1.2-3.5 0-4.6.2-13.7.2-9 0-10.2-.2-13.7-.3-3.4-.2-5.2-.7-6.4-1.2-1.6-.6-2.8-1.3-4-2.5-1-1.2-2-2.4-2.5-4-.5-1-1-3-1.2-6.3 0-3.5-.2-4.6-.2-13.7 0-9 .2-10.2.3-13.7.2-3.4.7-5.2 1.2-6.4.6-1.6 1.4-2.8 2.5-4 1.2-1 2.4-2 4-2.5 1-.5 3-1 6.3-1.2 3.5 0 4.6-.2 13.7-.2m0-6c-9.2 0-10.4 0-14 .2-3.6.2-6 .7-8.3 1.6-2.2 1-4 2-6 4-2 1.8-3 3.7-4 6-.8 2-1.3 4.6-1.5 8.2-.2 3.6-.2 4.8-.2 14s0 10.4.2 14c.2 3.6.7 6 1.6 8.3 1 2.2 2 4 4 6s3.7 3 6 4c2 .8 4.6 1.3 8.2 1.5 3.6.2 4.8.2 14 .2s10.4 0 14-.2c3.6-.2 6-.7 8.3-1.6 2.2-1 4-2 6-4 2-1.8 3-3.7 4-6 .8-2 1.3-4.6 1.5-8.2.2-3.6.2-4.8.2-14s0-10.4-.2-14c-.2-3.6-.7-6-1.6-8.3-1-2.2-2-4-4-6-1.8-2-3.7-3-6-4-2-.8-4.6-1.3-8.2-1.5-3.6-.2-4.8-.2-14-.2z'/></svg>
				</a></span>";
		} elseif ( false !== stripos( $link, 'pinterest' ) ) {
			$link = strip_tags($link);
			$out .= "
				<span><a href='{$link}' rel='nofollow' target='_blank'>
					<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 100 100'><path d='M50 11.8c-21 0-38.3 17-38.3 38.2 0 16.2 10 30 24.4 35.6-.3-3-.6-7.7.2-11l4.5-19s-1.2-2.3-1.2-5.6c0-5.4 3-9.3 7-9.3 3.2 0 4.8 2.4 4.8 5.3 0 3.3-2 8.2-3.2 12.8-.8 3.8 2 7 5.8 7 6.8 0 12-7.3 12-17.6 0-9.2-6.6-15.6-16-15.6-10.8 0-17.2 8.2-17.2 16.6 0 3.3 1.2 6.8 2.8 8.7.3.3.4.7.3 1l-1 4.4c-.2.7-.6.8-1.4.5-4.7-2.3-7.7-9.3-7.7-15 0-12 8.7-23 25.2-23 13.3 0 23.6 9.4 23.6 22 0 13.2-8.3 23.8-19.8 23.8-4 0-7.5-2-8.8-4.4l-2.4 9c-.8 3.4-3.2 7.6-4.7 10 3.6 1.3 7.4 2 11.3 2C71 88 88.3 71 88.3 50 88.3 29 71 11.7 50 11.7'/></svg>
				</a></span>";
		} elseif ( false !== stripos( $link, 'youtube' ) ) {
			$link = strip_tags($link);
			$out .= "
				<span><a href='{$link}' rel='nofollow' target='_blank'>
					<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 100 100'><path d='M87.5 34.7s-.8-5.3-3-7.6c-3-3-6.2-3-7.7-3C66 23 50 23 50 23s-16 0-26.8 1c-1.5 0-4.7 0-7.6 3-2.3 2.4-3 7.7-3 7.7s-1 6.2-1 12.4v6c0 6 1 12.3 1 12.3s.7 5.2 3 7.6c3 3 6.7 2.8 8.4 3 6 .7 26 1 26 1s16 0 26.8-1c1.5 0 4.7 0 7.6-3 2.3-2.5 3-7.7 3-7.7s1-6.2 1-12.4v-6c-.2-6-1-12.3-1-12.3M42.2 60V38.3L63 49.2 42 60z'/></svg>
				</a></span>";
		} elseif ( false !== stripos( $link, 'google' ) ) {
			$link = strip_tags($link);
			$out .= "
				<span><a href='{$link}' rel='nofollow' target='_blank'>
					<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 100 100'><path d='M33 45.4v9.8h16.4c-.7 4.2-5 12.4-16.3 12.4-9.6 0-17.6-8-17.6-18s8-18.2 17.7-18.2c5.7 0 9.4 2.4 11.6 4.4l7.7-7.4C47.3 23.7 41 21 33 21 17.4 21 4.6 33.6 4.6 49.4S17.3 78 33 78c16.7 0 27.6-11.5 27.6-27.8 0-2-.2-3.4-.4-4.8h-27zm61.5 0h-8.2v-8.2H78v8.2h-8v8.2h8v8.2h8.3v-8.2h8.2v-8.2z'/></svg>
				</a></span>";
		} elseif ( false !== stripos( $link, 'linkedin' ) ) {
			$link = strip_tags($link);
			$out .= "
				<span><a href='{$link}' rel='nofollow' target='_blank'>
					<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 100 100'><path d='M79.5 16.5h-58c-2.8 0-5 2.2-5 5v58c0 2.8 2.2 5 5 5h58c2.7 0 5-2.2 5-5v-58c0-2.8-2.3-5-5-5zm-42.8 58h-10V42h10v32.5zm-5-37c-3.3 0-6-2.6-6-5.8 0-3.2 2.7-5.8 6-5.8 3.2 0 5.8 2.5 5.8 5.7s-2.6 6-6 6zm42.8 37h-10V58.7c0-3.8-.2-8.6-5.4-8.6-5 0-6 4.2-6 8.4v16H43V42h9.7v4.4c1.5-2.5 4.7-5.2 9.6-5.2 10.3 0 12.2 6.7 12.2 15.4v18z'/></svg>
				</a></span>";
		} else {
			$out .= $line;// this is for custom links entered directly as html.
		}
		
	}// foreach link
	
	$out .= "</aside>";

	return $out;
}