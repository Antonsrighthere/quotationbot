<?php
if(!defined('ABSPATH')) die; // Die if accessed directly

class Tutbot_Core {
	
	private static $instance = NULL; //instance store
						
	private function __construct() {
		
		add_action('plugins_loaded', array($this, 'init'));
		add_filter('gwptb_supported_commnds_list', array($this, 'custom_commands'), 20);
	}
	
	/** instance */
    public static function get_instance(){
        
        if (NULL === self :: $instance)
			self :: $instance = new self;
					
		return self :: $instance;
    }     

	public function init(){
		
		if(!class_exists('Gwptb_Core'))
			return;
		
		add_action('init', array($this,'custom_post_types') );
		
		if(is_admin()){
			add_action( 'admin_init', array($this, 'settings_init'), 15);
		}

		add_filter('gwptb_supported_commnds_list', array($this, 'custom_commands'), 20);
	}
	
	public function custom_post_types(){
		
		register_post_type('quote', array(
			'labels' => array(
				'name'               => 'Цитаты',
				'singular_name'      => 'Цитата',
				'menu_name'          => 'Цитаты',
				'name_admin_bar'     => 'Добавить цитату',
				'add_new'            => 'Добавить новую',
				'add_new_item'       => 'Добавить новую',
				'new_item'           => 'Новая цитат',
				'edit_item'          => 'Редактировать цитату',
				'view_item'          => 'Просмотр цитаты',
				'all_items'          => 'Все цитаты',
				'search_items'       => 'Искать цитаты',
				'parent_item_colon'  => 'Родительская цитата:',
				'not_found'          => 'Цитаты не найдены',
				'not_found_in_trash' => 'В Корзине цитаты не найдены'
		   ),
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			//'query_var'           => true,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'rewrite'             => false,
			'hierarchical'        => false,
			'menu_position'       => 25,
			//'menu_icon'           => 'dashicons-calendar',
			'supports'            => array('title', 'editor', 'author'),
			'taxonomies'          => array(),
		));
		
		
		register_post_type('place', array(
			'labels' => array(
				'name'               => 'Места',
				'singular_name'      => 'Место',
				'menu_name'          => 'Места',
				'name_admin_bar'     => 'Добавить место',
				'add_new'            => 'Добавить новое',
				'add_new_item'       => 'Добавить новое',
				'new_item'           => 'Новое место',
				'edit_item'          => 'Редактировать место',
				'view_item'          => 'Просмотр места',
				'all_items'          => 'Все места',
				'search_items'       => 'Искать места',
				'parent_item_colon'  => 'Родительское место:',
				'not_found'          => 'Места не найдены',
				'not_found_in_trash' => 'В Корзине места не найдены'
		   ),
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			//'query_var'           => true,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'rewrite'             => false,
			'hierarchical'        => false,
			'menu_position'       => 25,
			//'menu_icon'           => 'dashicons-calendar',
			'supports'            => array('title', 'editor', 'author', 'excerpt', 'custom-fields'),
			'taxonomies'          => array(),
		));
		
		
		register_post_type('atr_images', array(
			'labels' => array(
				'name'               => 'Рисунки',
				'singular_name'      => 'Рисунок',
				'menu_name'          => 'Рисунки',
				'name_admin_bar'     => 'Добавить рисунок',
				'add_new'            => 'Добавить новый',
				'add_new_item'       => 'Добавить новый',
				'new_item'           => 'Новый рисунок',
				'edit_item'          => 'Редактировать рисунок',
				'view_item'          => 'Просмотр рисунка',
				'all_items'          => 'Все рисунки',
				'search_items'       => 'Искать рисунки',
				'parent_item_colon'  => 'Родительский рисунок:',
				'not_found'          => 'Рисунки не найдены',
				'not_found_in_trash' => 'В Корзине рисунки не найдены'
		   ),
			'public'              => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			//'query_var'           => true,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'rewrite'             => false,
			'hierarchical'        => false,
			'menu_position'       => 25,
			//'menu_icon'           => 'dashicons-calendar',
			'supports'            => array('title', 'editor', 'author'),
			'taxonomies'          => array(),
		));
	}
	
	//bot logic 
	public function custom_commands($commands){
		
		$commands['q'] = 'tut_q_command_result';
		$commands['m'] = 'tut_m_command_result';
		$commands['i'] = 'tut_i_command_result';
		
		return $commands;
	}
	
	
	/** Custom options */
	function settings_init(){
		register_setting( 'gwptb_settings', 'tut_donation_url',  array('GWPTB_Filters', 'sanitize_url'));
		
		add_settings_field( 
			'tut_donation_url', 
			'Ссылка "сделать пожертвование"', 
			array($this, 'donation_url_render'), 
			'gwptb_settings', 
			'gwptb_bot_section' 
		);
	}
	
	function donation_url_render() {
		$value = get_option('tut_donation_url');
	?>	
		<input type='text' name='tut_donation_url' value='<?php echo $value; ?>' class="large-text">
	<?php
	}
	
	public static function get_donation_url(){
		
		$value = get_option('tut_donation_url');
		return (!empty($value)) ? esc_url($value) : '';
	}
	
} //class


