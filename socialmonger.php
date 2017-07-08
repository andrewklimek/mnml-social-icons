<?php
/*
Plugin Name: Social Monger
Plugin URI:  https://github.com/andrewklimek/socialmonger/
Description: crisp and light (official) social media icons (embeds SVG code for fast loading and vector rendering)
Version:     1.3.1
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
* supported sites: twitter, facebook, instagram, pinterest, youtube, google plus, linkedin, soundcloud, spotify, bandcamp
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
	
	$dir = __DIR__ . '/svgs/';
	$lines = array_filter( explode( "\n", $c ) );
	
	$sites = array(		
		'facebook',
		'twitter',
		'instagram',
		'youtube',
		'linkedin',
		'pinterest',
		'google',
		'soundcloud',
		'spotify',
		'bandcamp',
	);
	
	foreach ( $lines as $line ) {
		
		// check for scheme and add if missing. preserve original $line in case it's a custom html (else block at the end)
		$link = strip_tags( ( false === strpos( $line, '//' ) ) ? '//' . $line : $line );

		foreach ( $sites as $site ) {
				
			poo($site);
				
			if ( false !== stripos( $line, $site ) ) {
				
				$out .= "\n<span><a href='{$link}' rel='nofollow' target='_blank'>" . file_get_contents( "{$dir}{$site}.svg" ) . "</a></span>";
					
				continue 2;// break out of this loop start at next line
			}
		}
			
		poo( "custom link");
			
		$out .= $line;// this is for custom links entered directly as html.
			
	}// foreach link
	
	$out .= "</aside>";

	return $out;
}
