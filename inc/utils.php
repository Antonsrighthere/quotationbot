<?php

function post_image_multipart($url, $local_file) {
	$boundary = wp_generate_password( 24 );
	$headers  = array(
	 	'content-type' => 'multipart/form-data; boundary=' . $boundary
	);
	$payload = '';
	if ( $local_file ) {
		$payload .= '--' . $boundary;
		$payload .= "\r\n";
		$payload .= 'Content-Disposition: form-data; name="' . 'photo' . '"; filename="' . basename( $local_file ) . '"' . "\r\n";
		$payload .= 'Content-Type: image/jpeg' . "\r\n";
		$payload .= "\r\n";
		$payload .= file_get_contents( $local_file );
		$payload .= "\r\n";
	}
	$payload .= '--' . $boundary . '--';
	$response = wp_remote_post( $url,
		array(
			'headers' => $headers,
		  	'body'    => $payload,
		)
	);
	return $response;
}

function send_attach($chat_id, $attach_id) {
	$token = get_option('gwptb_bot_token');
	$api_url = trailingslashit('https://api.telegram.org/bot'.$token);

	$get_params = 'sendPhoto?chat_id='.$chat_id;

	$image_attributes = wp_get_attachment_image_src($attach_id, 'full');
	$image_path = $image_attributes[0];

	$response = post_image_multipart($api_url.$get_params, $image_path);
}

function tut_format_quotes_list($posts){
	
	$out = '';
	
	foreach($posts as $p){
		$out .= $p->post_content.chr(10).chr(10);		
	} 
	
	return $out;
}

function tut_format_places_list($posts){
	
	$out = '';
	
	foreach($posts as $p){
		
		if(empty($p->post_excerpt))
			continue;
		
	
		$map_link = tut_get_map_link($p);
		$map_link = (!empty($map_link)) ? ' '.tut_get_map_link($p) : '';
		
		$out .= $p->post_title.chr(10);
		$out .= $p->post_excerpt.$map_link.chr(10);
		$out .= $p->post_content.chr(10).chr(10);
	} 
	
	return $out;
}

function tut_get_map_link($p){
	
	$lat = (float)get_post_meta($p->ID, 'lat', true);
	$lon = (float)get_post_meta($p->ID, 'lon', true);
	
	if(empty($lat)||empty($lon))
		return '';
	
	$map = 'http://www.google.com/maps/place/'.$lat.','.$lon;
	$map_link = "<a href='{$map}'>[карта]</a>";
	
	return $map_link;
}