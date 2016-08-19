<?php
/* Functions */

function tut_m_command_result($upd_data){
	if(!isset($upd_data['command']) || $upd_data['command'] != 'm') { //no command in data
		$result['text'] = apply_filters('gwptb_output_text', 'К сожалению, вы отправили неверный запрос.');
		return $result;
	}
	
	$result = array();
	$args = array();
	$per_page = 5;
	
	$args = array(
		'post_type' => 'place',
		'posts_per_page' => $per_page,
		'paged' => 1
	);
	
	if(false !== strpos($upd_data['content'], 'paged=')) {
		//more search
		parse_str($upd_data['content'], $a);
			
		if(isset($a['paged'])){
			$args['paged'] = (int)$a['paged'];
			
		}
	}
	
	//query
	$query = new WP_Query($args);
	
	if($query->have_posts()){
		$result['parse_mode'] = 'HTML';
		$keys = array('inline_keyboard' => array());
		
		//list
		$paged = $args['paged'];
		if($query->found_posts > $per_page){
			$end = ($paged*$per_page < $query->found_posts) ? $paged*$per_page : $query->found_posts;
			$result['text'] = sprintf('Найдено %d / показано %d - %d', $query->found_posts, ($paged*$per_page - $per_page) + 1, $end).chr(10).chr(10);
		}
		else {
			$result['text'] = sprintf('Найдено - %d', $query->found_posts).chr(10).chr(10);
		}
		
		$result['text'] .= tut_format_places_list($query->posts);
		$result['text'] = apply_filters('gwptb_output_html', $result['text']);
		
		//nex/prev keys
		if($paged > 1){
			$keys['inline_keyboard'][0][] = array('text' => 'Пред.', 'callback_data' => 'm='.$s.'&paged='.($paged-1));				
		}
		
		if($paged < ceil($query->found_posts/$per_page)) {
			$keys['inline_keyboard'][0][] = array('text' => 'След.', 'callback_data' => 'm='.$s.'&paged='.($paged+1));		
		}
		
		//donation button
		$donation_url = Tutbot_Core::get_donation_url();
		if(!empty($donation_url)) {
			$keys['inline_keyboard'][][] = array('text' => 'Сделать пожертвование', 'url' => 'http://outfundspb.ru/wp/#help');
		}
		$result['reply_markup'] = json_encode($keys);
		
	}
	else{
		$result['text'] = 'К сожалению, по вашему запросу ничего не найдено.';
		$result['text'] = apply_filters('gwptb_output_text', $result['text']);
	}
	
	return $result;
}

//images
function tut_i_command_result($upd_data) {
	if(!isset($upd_data['command']) || $upd_data['command'] != 'i') { //no command in data
		$result['text'] = apply_filters('gwptb_output_text', 'К сожалению, вы отправили неверный запрос.');
		return $result;
	}

	$result = array();
	
	$attaches = new WP_Query(array(
			'post_type' => 'attachment',
			'post_status' => 'any',
			'orderby' => 'rand',
			'posts_per_page' => 1,
			'post_parent' => 3111,
		)
	);
	if ($attaches->have_posts()) {
		foreach ($attaches->posts as $attach) {
			send_attach($upd_data['chat_id'], $attach->ID);
			$result['text'] = '';
		}
	}
	else {
		$result['text'] = 'К сожалению, по вашему запросу ничего не найдено.';
	}

	$donation_url = Tutbot_Core::get_donation_url();
	if(!empty($donation_url)) {
		$keys['inline_keyboard'][][] = array('text' => 'Сделать пожертвование', 'url' => 'http://outfundspb.ru/wp/#help');
	}
	$result['reply_markup'] = json_encode($keys);
	
	return $result;
}

//quotes
function tut_q_command_result($upd_data) {
	if(!isset($upd_data['command']) || $upd_data['command'] != 'q') { //no command in data
		$result['text'] = apply_filters('gwptb_output_text', 'К сожалению, вы отправили неверный запрос.');
		return $result;
	}
	
	$result = array();
	$args = array();
	$per_page = 2;
	$s = '';
	
	if(false !== strpos($upd_data['content'], 'next=')) { //update
		//more random 
		$args = array(
			'post_type' => 'quote',
			'posts_per_page' => 1,
			'orderby' => 'rand'
		);
		
	}
	elseif(false !== strpos($upd_data['content'], 'paged=')) {
		//more search
		parse_str($upd_data['content'], $a);
			
		if(isset($a['q']) && isset($a['paged'])) {
			$args = array(
				'post_type' => 'quote',
				'posts_per_page' => $per_page,
				's' => apply_filters('gwptb_search_term', $a['q']),
				'paged' => (int)$a['paged']
			);
			
		}
	}
	else {
		//have search term
		$self = Gwptb_Self::get_instance();
		$s = apply_filters('gwptb_search_term', str_replace(array('@', '/q', $self->get_self_username()), '', $upd_data['content']));
			
		
		if(!empty($s)) { //initial search
			$args = array(
				'post_type' => 'quote',
				'posts_per_page' => $per_page,
				's' => $s,
				'paged' => 1
			);
		}
		else { //random quote
			$args = array(
				'post_type' => 'quote',
				'posts_per_page' => 1,
				'orderby' => 'rand'
			);
		}
	}
	
	//query
	$query = new WP_Query($args);
	
	if($query->have_posts()) {
		$result['parse_mode'] = 'HTML';
		$keys = array('inline_keyboard' => array());
		
		if(isset($args['s'])) { //search results buttons
			//list
			$paged = $args['paged'];
			if($query->found_posts > $per_page){
				$end = ($paged*$per_page < $query->found_posts) ? $paged*$per_page : $query->found_posts;
				$result['text'] = sprintf('Найдено %d / показано %d - %d', $query->found_posts, ($paged*$per_page - $per_page) + 1, $end).chr(10).chr(10);
			}
			else {
				$result['text'] = sprintf('Найдено - %d', $query->found_posts).chr(10).chr(10);
			}
			
			$result['text'] .= tut_format_quotes_list($query->posts);
			$result['text'] = apply_filters('gwptb_output_html', $result['text']);

			//nex/prev keys
			if($paged > 1){
				$keys['inline_keyboard'][0][] = array('text' => 'Пред.', 'callback_data' => 'q='.$s.'&paged='.($paged-1));				
			}
			
			if($paged < ceil($query->found_posts/$per_page)) {
				$keys['inline_keyboard'][0][] = array('text' => 'След.', 'callback_data' => 'q='.$s.'&paged='.($paged+1));		
			}
		}
		else { //random quote button			
				
			$p = reset($query->posts);
			
			$result['text'] = ''; 
			$result['text'] .= $p->post_content;
			$result['text'] = apply_filters('gwptb_output_text', $result['text']);
			
			$keys['inline_keyboard'][0][] = array('text' => 'Еще цитата', 'callback_data' => 'q=1&next=1');
		}
		
		//donation button
		$donation_url = Tutbot_Core::get_donation_url();
		if(!empty($donation_url)) {
			$keys['inline_keyboard'][][] = array('text' => 'Сделать пожертвование', 'url' => 'http://outfundspb.ru/wp/#help');
		}
		
		//add buttons

		$result['reply_markup'] = json_encode($keys);
	}
	else {
		$result = handle_quote_not_found($upd_data);
	}
	
	return $result;
}

function handle_quote_not_found($upd_data) {	
	$result = array();
	$args = array(
				'post_type' => 'atr_images',
				'posts_per_page' => 1,
				'orderby' => 'rand'
			);
	
	//query
	$query = new WP_Query($args);
	
	if($query->have_posts()) {	
		foreach ($query->posts as $post) {
			$attaches = new WP_Query(array(
					'post_type' => 'attachment',
					'post_status' => 'any',
					'posts_per_page' => 1,
					'post_parent' => $post->ID,
				)
			);
			if ($attaches->have_posts()) {
				foreach ($attaches->posts as $attach) {
					send_attach($upd_data['chat_id'], $attach->ID);
					$result['text'] = '';
				}
			}
		}
	}
	$result['text'] = $post->post_title;

	$donation_url = Tutbot_Core::get_donation_url();
	if(!empty($donation_url)) {
		$keys['inline_keyboard'][][] = array('text' => 'Сделать пожертвование', 'url' => 'http://outfundspb.ru/wp/#help');
	}
	$result['reply_markup'] = json_encode($keys);

	return $result;
}