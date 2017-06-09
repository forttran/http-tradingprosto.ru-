<?php
/*
	 Plugin Name: my_plugin
	 Plugin URI: http://tradingprosto.ru/wordpress-plugins/my_plugin
	 Description: This is discription my plugin
	 Version: 1.0
	 Author: Sergey Vasiluev
	 Author URI: http://tradingprosto.ru
	 License: GPLv2
*/
add_action( 'admin_menu', 'prowp_create_menu' );
function prowp_create_menu() {
	// создаем новое меню верхнего уровня
	add_menu_page( 'my_plugin', 'Мой плагин',
			'manage_options', 'my_plugin', 'prowp_main_plugin_page',
			plugins_url( '/images/wordpress.png', __FILE__ ) );
	// создаем подпункты меню: настройка и поддержка
	/*add_submenu_page( 'my_plugin', 'my_plugin_settings',
			'Свойства', 'manage_options', 'my_plugin_settings',
			'prowp_settings_page' );
	add_submenu_page( 'my_plugin', 'my_plugin_support',
			'Поддержка', 'manage_options', 'my_plugin_support', 'prowp_support_page' );
	//Добавляем подменю в меню настройки
	add_options_page( 'my_plugin_settings', 'Halloween Settings',
			'manage_options', 'halloween_settings_menu', 'prowp_settings_page' );
*/
	add_action('admin_init', 'my_plugin_register_settings');
}

function my_plugin_register_settings(){
	register_setting('my_plugin_settings_group', 'my_plugin_options', 'my_plugin_sanitize_options');
}

function prowp_main_plugin_page(){
	?>
		<div class="wrap">
		<h2>Halloween Plugin options</h2>
		<form method="post" action='options.php'>
		<?php settings_fields( 'my_plugin_settings_group' ); ?>
		<?php $my_plugin_options = get_option( 'my_plugin_options' ); ?>
		<table class="form-table">
		<tr valign="top">
		<th scope="row">Name</th>
		<td> <input type="text" name="my_plugin_options[option_name]"
		value="<?php echo esc_attr( $my_plugin_options['option_name'] ); ?>" />
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">Email</th>
		<td> <input type="text" name="my_plugin_options[option_email]"
		value="<?php echo esc_attr( $my_plugin_options['option_email'] ); ?>"/>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">URL</th>
		<td><input type="text" name="my_plugin_options[option_url]"
		value="<?php echo esc_url( $my_plugin_options['option_url'] ); ?>" />
		</td>
		</tr>
		</table>
		<p class="submit">
		<input type="submit" class="button-primary"
		value="Save Changes" />
		</p>
		</form>
		</div>
		<?php
}

function my_plugin_sanitize_options( $input ) {
	$input['option_name'] = sanitize_text_field( $input['option_name'] );
	$input['option_email'] = sanitize_email( $input['option_email'] );
	$input['option_unl'] = esc_url( $input['option_url'] );
	return $input;
}



global $wp_version;
global $wpdb;
register_activation_hook( __FILE__ , 'prowp_install' );
function prowp_install() {
	global $wpdb;
	$wpdb->query(
			"
			CREATE TABLE IF NOT EXISTS `test` (
				`iad` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(20) NOT NULL,
				PRIMARY KEY (`iad`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
			"
			);
	update_option('my_option_word', 'sensor');
}

add_filter('the_content', 'prof_filter');
function prof_filter($content){
	$prof = array('sissy', 'dummy');
	$content = str_ireplace($prof, '['.get_option('my_option_word').']', $content);
	return $content;
}

// выполняем функцию раздела настроек
add_action( 'admin_init', 'prowp_settings_init' );
function prowp_settings_init() {
	// создаем новый раздел настроек в Параметры > Чтение
	add_settings_section( 'prowp_setting_section', 'Halloween Plugin Settings',
			'prowp_setting_section', 'reading' );
	// регистрируем индивидуальные настройки
	add_settings_field( 'prowp_setting_enable_id', 'Enable Halloween Feature?',
			'prowp_setting_enabled', 'reading', 'prowp_setting_section' );
	add_settings_field( 'prowp_saved_setting_name_id', 'Your Name',
			'prowp_setting_name', 'reading', 'prowp_setting_section' );
	// регистрируем настройки с помощью массива значений
	register_setting( 'reading', 'prowp_setting_values', 'prowp_sanitize_settings');
}

function prowp_sanitize_settings($input){
	$input['enabled'] = ($input['enabled'] == 'on') ? 'on' : '';
	$input['name'] = sanitize_text_field($input['name']);
	return $input;
}
function prowp_setting_section(){
	echo "<p>Configure the Halloween plugin options below</p>";
}
function prowp_setting_enabled() {
	// получаем настройки плагина
	$prowp_options = get_option( 'prowp_setting_values' );
	// отображаем форму с чекбоксами
	echo '<input '.checked( $prowp_options['enabled'], 'on', false ).'name="prowp_setting_values[enabled]" type="checkbox" /> Enabled';
}
function prowp_setting_name() {
	// получаем значение настройки
	$prowp_options = get_option( 'prowp_setting_values' );
	// отображаем текстовое поле
	echo '<input type="text" name="prowp_setting_values[name]"
		value="'.esc_attr( $prowp_options['name']).'" />';
}

//-----------------------------------------------------------------------------
add_action( 'add_meta_boxes', 'prowp_meta_box_init' );
// функции для добавления метаполя и сохранения данных
function prowp_meta_box_init() {
	// создаем произвольное метаполе
	add_meta_box( 'prowp-meta', 'Product Information',
			'prowp_meta_box', 'post', 'side', 'default' );
}
function prowp_meta_box($post, $box){
	// извлекаем значения произвольного метаполя
	$prowp_featured = get_post_meta($post->ID, '_prowp_type', true );
	$prowp_price = get_post_meta( $post->ID, '_prowp_price', true );
	// временные значения из соображений безопасности
	wp_nonce_field( plugin_basename( __FILE__ ), 'prowp_save_meta_box' );
	// форма метаполя
	echo '<p>Price: <input type="text" name="prowp_price"
		value="'.esc_attr( $prowp_price ).'" size="5" /></p>';
	echo '<p>Type:
		<select name="prowp_product_type" id="prowp_product_type">
		<option value="0" '
		.selected( $prowp_featured, 'normal', false ). '>Normal
		</option>
		<option value="special" '
		.selected( $prowp_featured, 'special', false ). '>Special
		</option>
		<option value="featured" '
		.selected( $prowp_featured, 'featured', false ). '>Featured
		</option>
		<option value="clearance" '
		.selected( $prowp_featured, 'clearance', false ). '>Clearance
		</option>
		</selectx/p>';
}

// сохраняем данные метаполя во время сохранения записи
add_action( 'save_post', 'prowp_save_meta_box' );
function prowp_save_meta_box( $post_id ) {
	// обрабатываем данные формы, если установлена переменная $_POST
	if( isset( $_POST['prowp_product_type'] ) ) {
		// если включено автосохранение, пропускаем этап сохранения данных метаполя
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
		// проверяем временное значение из соображений безопасности
		check_admin_referer( plugin_basename( __FILE__ ), 'prowp_save_meta_box' );
		// сохраняем данные метаполя в произвольных полях записи, используя префикс ID
		update_post_meta( $post_id, '_prowp_type',
				sanitize_text_field( $_POST['prowp_product_type'] ) );
		update_post_meta( $post_id, '_prowp_price',
				sanitize_text_field ( $_POST['prowp_price'] ) );
	}
}
add_action( 'init', 'prowp_register_my_post_types' );
function prowp_register_my_post_types() {
	register_post_type( 'products',
			array(
				'labels' => array(
					'name' => 'Products'
					),
				'public' => true,
				)
			);
}
/*add_action( 'init', 'prowp_define_product_type_taxonomy' );
function prowp_define_product_type_taxonomy() {
	register_taxonomy( 'type', 'products', array( 'hierarchical' => true,
				'label' => 'Type', 'query_var' => true, 'rewrite' => true ) );
}*/

add_action( 'init', 'prowp_define_product_type_taxonomy' );
function prowp_define_product_type_taxonomy() {
	$labels = array(
			'name' => 'Тип',
			'singular_name' => 'Типы',
			'seanch_items' => 'Поиск типов',
			'all_items' => 'Все типы',
			'parent_item' => 'Родительский тип',
			'parent_item_colon' => 'Родительский тип:',
			'edit_item' => 'правка типа',
			'update_item' => 'обновление типа',
			'add_new_item' => 'Добравить новый тип',
			'new_item_name' => 'Новое имя типа',
			'menu_name' => 'Тип'
			);
	$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'query_var' => true,
			'rewrite' => true
			);
	register_taxonomy( 'type', 'products', $args );
}
add_shortcode( 'mytwitter', 'prowp_twitter' );
function prowp_twitter() {
	return '<a href = "http://twitter.com/williamsba">@willianisba</a>';
}
//-----------------------------------------------------------------------------
include 'prowp_widget.php';
//колхозим виджет
add_action('widgets_init', 'prowp_register_widgets');
function prowp_register_widgets(){
	register_widget('prowp_widget');
}

add_action( 'wp_dashboard_setup', 'prowp_add_dashboard_widget' );
// вызываем функцию для создания консольного виджета
function prowp_add_dashboard_widget() {
	wp_add_dashboard_widget( 'prowp_dashboard_widget',
			'Pro WP Dashboard Widget', 'prowp_create_dashboard_widget' );
}
// функция для отображения содержания консольного виджета
function prowp_create_dashboard_widget() {
	echo '<p>Hello World! This is my Dashboard Widget</p>';
}
?>
