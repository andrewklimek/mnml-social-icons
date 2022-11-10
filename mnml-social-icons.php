<?php
/*
Plugin Name: Minimalist Social Icons
Plugin URI:  https://github.com/andrewklimek/mnml-social-icons
Description: crisp and light (official) social media icons (embeds SVG code for fast loading and vector rendering) using [mnmlsocial] shortcode and simply pasting links, one per line, before the closing [/mnmlsocial]
Version:     1.5.4
Author:      Andrew J Klimek
Author URI:  https://andrewklimek.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Minimalist Social Icons is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by the Free 
Software Foundation, either version 2 of the License, or any later version.

Minimalist Social Icons is distributed in the hope that it will be useful, but without 
any warranty; without even the implied warranty of merchantability or fitness for a 
particular purpose. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with 
Minimalist Social Icons. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


// Disabling the shortcode and sneaking it in before Embeds are processed
// Certain URLs (soundcloud..) are converted into embeds and thus are no longer normal URLs during shortcode processing.
// See https://github.com/WordPress/WordPress/blob/2361ca884f562e996fffd1ee373f29f75d41aff3/wp-includes/class-wp-embed.php#L32
// actually, adding shortcode back in just so that calls to strip_shortcodes() know to remove this as well.
add_shortcode( 'mnmlsocial', 'mnmlsocial' );

add_filter( 'the_content', 'mnmlsocial_custom_shortcode_parsing', 7 );// Run this early to avoid embed parsing (priority 8) and wpautop
add_filter( 'widget_text', 'mnmlsocial_custom_shortcode_parsing', 7 );// also process text and HTML widgets
	
function mnmlsocial_custom_shortcode_parsing( $c ) {
    
	$tag = "mnmlsocial";
	
	if ( false === strpos($c, '[' . $tag ) ) return $c;
    
	$c = preg_replace_callback(
		"/\[{$tag}([^\]]*)\]((?:[^\[]*|\[(?!\/{$tag}\]))*)\[\/{$tag}\]/",
		function($m){ return mnmlsocial( shortcode_parse_atts($m[1]), $m[2] );},
		$c );
	
    return $c;
}

/***
*
* [mnmlsocial] shortcode.
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
* opacity - the CSS property. 0 - 1
* show - svg, text, both (default svg)
* 
* if you leave any of these empty in a secondary instance, they will pull the attributes from the first shortcode on the page.
*
* Each instance gets an incremental number in the ID so you can target for further customization
*  
**/



function mnmlsocial( $a, $c ) {
	
	// Check to see if this is the first shortcode
	$idno = 1 + wp_cache_get( 'mnmlsocial_id' );
	wp_cache_set('mnmlsocial_id', $idno );
	
	$out = "<nav id='mnmlsocial-{$idno}' class='mnmlsocial'>";
	
	// initial style, only print once
	if ( $idno === 1 ) {
		
		$size = !empty( $a['size'] ) ? $a['size'] : "2rem";
		$color = !empty( $a['color'] ) ? $a['color'] : "currentColor";
		$padding = !empty( $a['padding'] ) ? $a['padding'] : "0 1ex";
		$opacity = !empty( $a['opacity'] ) ? ";opacity:" . $a['opacity'] : "";
		
		if ( empty( $a['align'] ) || 'inline' === $a['align'] ) $align = "display:inline-table";
		elseif ( 'center' === $a['align'] ) $align = "display:table;margin-left:auto;margin-right:auto";
		elseif ( 'left' === $a['align'] ) $align = "display:table;margin-right:auto";
		elseif ( 'right' === $a['align'] ) $align = "display:table;margin-left:auto";
		
		$out .= "
		<style>
		.mnmlsocial{padding:0;{$align}}
		.mnmlsocial-item > a{text-decoration:none}
		.mnmlsocial-item{display:table-cell;vertical-align:middle;padding:{$padding}}
		.mnmlsocial svg{display:block;width:{$size};height:{$size};fill:{$color}{$opacity}}
		</style>";
		// removed max-width:100%; from svg because it was shrinking them in flex layouts.

	} elseif ( $a ) {// subsequent styles, for second instances on same page, only run if any attributes exist
		
		$out .= "<style>#mnmlsocial-{$idno} svg {";
		if ( !empty( $a['size'] ) )
			$out .= "width: {$a['size']};height: {$a['size']};";
		if ( !empty( $a['color'] ) )
			$out .= "fill: {$a['color']};";
		if ( !empty( $a['padding'] ) )
			$out .= "} #mnmlsocial-{$idno} > .mnmlsocial-item {padding: {$a['padding']};";
		if ( !empty( $a['align'] ) ) {
			if ( 'inline' === $a['align'] ) $align = "display: inline-table;";
			elseif ( 'center' === $a['align'] ) $align = "display: table;margin-left: auto;margin-right: auto;";
			elseif ( 'left' === $a['align'] ) $align = "display: table;margin-right: auto;";
			elseif ( 'right' === $a['align'] ) $align = "display: table;margin-left: auto;";
			$out .= "} #mnmlsocial-{$idno} {{$align}";
		}
		$out .= "}</style>";
	
	}
	
	$dir = __DIR__ . '/svgs/';
	$lines = explode( "\n", $c );
	
	$sites = array(		
		'facebook' => 'Facebook',
		'twitter' => 'Twitter',
		'instagram' => 'Instagram',
		'youtube' => 'YouTube',
		'linkedin' => 'Linkedin',
		'pinterest' => 'Pinterest',
		'google' => 'Google Plus',
		'soundcloud' => 'SoundCloud',
		'spotify' => 'Spotify',
		'bandcamp' => 'Bandcamp',
		'apple' => 'Apple',
	);

	foreach ( $lines as $line ) {

		$line =  trim( $line );

		if ( ! $line ) continue;
		
		// check for scheme and add if missing. preserve original $line in case it's a custom html
		$link = strip_tags( ( false === strpos( $line, '//' ) ) ? '//' . $line : $line );

		foreach ( $sites as $site_url => $site_display ) {
			
			if ( false !== stripos( $line, $site_url ) ) {
			    
			    if ( empty($a['show']) || "svg" === $a['show'] ) $show = file_get_contents( "{$dir}{$site_url}.svg" );
    			elseif ( "both" === $a['show'] ) $show = file_get_contents( "{$dir}{$site_url}.svg" ) . " " . $site_display;
    			elseif ( "text" === $a['show'] ) $show = $site_display;
				
				$out .= "\n<div class=mnmlsocial-item><a href='{$link}' rel=nofollow target=_blank title='{$site_display}'>" . $show . "</a></div>";
					
				continue 2;// break out of this loop start at next line
			}
		}
		
		// custom links
		if ( stripos( $line, '</a>' ) ) {// custom links entered directly as html
		    $out .= "\n<div class=mnmlsocial-item>{$line}</div>";
		} else {
		    // plaintext links
		    $label = explode( '/', explode('//', $link)[1] )[0];
            $out .= "\n<div class=mnmlsocial-item><a href='{$link}' rel=nofollow target=_blank title='{$label}'>{$label}</a></div>";
		}
			
	}// foreach link
	
	$out .= "</nav>";

	return $out;
}
