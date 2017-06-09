<?php
/*
Plugin Name: Halloween store
Plugin URI: http://mail.ru
Description: Создаем плагин Hakkiween Store для отображения информации о продуктах 
Version: 1.0
Author: Sergey Vasilyev
Author URI: http://mail.ru
*/
register_activation_hook( __FILE__ , 'halloween_store_install' );
function halloween_store_install() {
	$hween_options_arr = array(
			'currency_sign' => '$'
			);
	// сохраняем параметры по умолчанию
	update_option( 'halloween_options', $hween_options_arr);
}

// Зацепка-действие для инициализации плагина
add_action( 'init', 'halloween_store_init' );
// функция инициализация плагина Halloween Store
function halloween_store_init() {
	//register the products custom post type
	$labels = array(
			'name' => __( 'Products', 'halloween-plugin' ),
			'singular_name' => __( 'Product', 'halloween-plugin' ),
			'add_new' => __( 'Add New', 'halloween-plugin' ),
			'add_new_item' => __( 'Add New Product', 'halloween-plugin' ),
			'edit_item' => __( 'Edit Product', 'halloween-plugin' ),
			'new_item' => __( 'New Product', 'halloween-plugin' ),
			'all_items' => __( 'All Products', 'halloween-plugin' ),
			'view_item' => __( 'View Product', 'halloween-plugin' ),
			'search_items' => __( 'Search Products', 'halloween-plugin' ),
			'not_found' => __( 'No products found', 'halloween-plugin' ),
			'not_found_in_trash' => __( 'No products found in Trash',
				'halloween-plugin' ),
			'menu_name' => __( 'Products', 'halloween-plugin' )
			);
	$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' )
			);
	register_post_type( 'halloween-products', $args );
}

// зацепка-действие, чтобы добавить пункт меню Продукты
add_action( 'admin_menu', 'halloween_store_menu' );
// создаем подпункт меню Halloween Masks
function halloween_store_menu() {
	add_options_page( __('Halloween Store Settings Page','halloween-plugin' ),
			__( 'Halloween Store Settings','halloween-plugin' ), 
			'manage_options', 'halloween-store-settings',
			'halloween_store_settings_page' );
}
// создаем страницу настроек плагина
function halloween_store_settings_page() {
	// извлекаем массив настроек плагина
	$hween_options_arr = get_option( 'halloween_options' );
	// устанавливаем значения переменных из массива
	$hs_inventory = ( ! empty( $hween_options_arr['show_inventory'] ) ) ?
		$hween_options_arr['show_inventory'] : '';
	$hs_currency_sign = $hween_options_arr['currency_sign'];
	?>
		<div class="wrap">
		<h2><?php _e( 'Halloween Store Options', 'halloween-plugin' ) ?></h2>
		<form method="post" action="options.php">
		<?php settings_fields( 'halloween-settings-group' ); ?>
		<table class="form-table">
		<tr valign="top">
		<th scope="row"><?php _e( 'Show Product Inventory',
				'halloween-plugin' ) ?></th>
		<td><input type="checkbox" name="halloween_options[show_inventory]"
		<?php echo checked( $hs_inventory, 'on' ); ?> /></td>
		</tr>
		<tr valign="top">
		<th scope="row"><?php _e( 'Currency Sign', 'halloween-plugin' ) ?></th>
		<td><input type="text" name="halloween_options[currency_sign]"
		value="<?php echo esc_attr( $hs_currency_sign ); ?>"
		size="l" maxlength="l" /></td>
		</tr>
		</table>
		<p class="submit">
		<input type="submit" class="button-primary”
		value="<?php _e( 'Save Changes', 'halloween-plugin' ); ?>" />
		</p>
		</form>
		</div>
<?php
}

// зацепка-действие, чтобы зарегистрировать настройки плагина
add_action( 'admin_init', 'halloween_store_register_settings' );
function halloween_store_register_settings() {
	// регистрируем настройки
	register_setting( 'halloween-settings-group',
			'halloween_options', 'halloween_sanitize_options' );
}
function halloween_sanitize_options( $options ) {
	$options['show_inventory'] = (!empty( $options['show_inventory'] ) ) ? 
		sanitize_text_field( $options['show_inventory'] ) : '';
	$options['currency_sign'] = ( ! empty( $options['currency_sign'] ) ) ?
		sanitize_text_field( $options['currency_sign'] ) : '';
	return $options;
}
// зацепка-действие, чтобы зарегистрировать метаполе товара
add_action( 'add_meta_boxes', 'halloween_store_register_meta_box' );
function halloween_store_register_meta_box() {
// создаем произвольное метаполе
	add_meta_box( 'halloween-product-meta',
			__( 'Product Information','halloween-plugin' ),
			'halloween_meta_box', 'halloween-products', 'side', 'default' );
}

// создаем метаполе Продукты
function halloween_meta_box($post ) {
	// извлекаем значения произвольного метаполя
	$hween_sku = get_post_meta($post->ID, '_halloween_product_sku', true);
	$hween_price = get_post_meta($post->ID, '_halloween_product_price', true);
	$hween_weight = get_post_meta($post->ID, '_halloween_product_weight', true);
	$hween_color = get_post_meta($post->ID, '_halloween_product_color', true );
	$hween_inventory = get_post_meta($post->ID, '_halloween_product_inventory', true );
	// проверяем временное значение из соображений безопасности
	wp_nonce_field( 'meta-box-save', 'halloween-plugin' );
	// отображаем форму метаполя
	echo '<table>';
	echo '<tr>';
	echo '<td>' .__('Sku', 'halloween-plugin').':</td>
		<td><input type="text" name="halloween_product_sku"
		value="' .esc_attr( $hween_sku ).'" size="10"></td»';
	echo '</tr><tr>';
	echo '<td>' . __('Price', 'halloween-plugin').':</td>
		<td><input type="text" name="halloween_product_price"
		value="' .esc_attr( $hween_price ).'" size="5"></td>';
	echo '</tr><tr>';
	echo '<td>' . __('Weight', 'halloween-plugin').':</td>
		<td><input type="text" name="halloween_product_weight"
		value="' .esc_attr( $hween_weight ).'" size="5"></td>';
	echo '</tr><tr>';
	echo '<td>' . __('Color', 'halloween-plugin').':</td>
		<td><input type="text" name="halloween_product_color"
		value="' .esc_attr( $hween_color ).'" size="5"></td>';
	echo '</tr><tr>';
	echo '<td>Inventory:</td>
		<td><select name="halloween_product_inventory"
		id="halloween_product_inventory">
		<option value="In Stock"'
		.selected($hween_inventory, 'In Stock', false ). '>'
		.__( 'In Stock', 'halloween-plugin' ). '</option>
		<option value="Backordered"'
		.selected( $hween_inventory, 'Backordered', false ). '>'
		.__( 'Backordered', 'halloween-plugin' ). '</option>
		<option value="Out of Stock"'
		.selected( $hween_inventory, 'Out of Stock', false ). '>'
		.__( 'Out of Stock', 'halloween-plugin' ). '</option>
		<option value="Discontinued"'
		.selected( $hween_inventory, 'Discontinued', false ). '>'
		. __( 'Discontinued', 'halloween-plugin' ). '</option>
		</select></td> ' ;
	echo '</tr>';
	// отображаем легенду раздела с сокращенным кодом
	echo '<tr><td colspan="2"><hr></td></tr>';
	echo '<tr><td colspan="2"><strong>'
		.__( 'Shortcode Legend', 'halloween-plugin' ). ' </strong></td></tr> ' ;
	echo '<tr><td>' . __( 'Sku', 'halloween-plugin' ). ':
		</td><td>[hs show=sku]</td></tr>';
	echo '<tr><td>' . __( 'Price', 'halloween-plugin'). ':
		</td><td>[hs show=price]</td></tr> ' ;
	echo '<tr><td>' .__( 'Weight', 'halloween-plugin'). ':
		</td><td>[hs show=weight]</td></tr>';
	echo '<tr><td>' .__( 'Color', 'halloween-plugin'). ':
		</td><td>[hs show=color]</td></tr>';
	echo '<tr><td>' .__( 'Inventory', 'halloween-plugin'). ':
		</td><td>[hs show=inventory]</td></tr>';
	echo '</table>';
}
// зацепка-действие для сохранения данных метаполя, когда сохраняется запись
add_action( 'save_post','halloween_store_save_meta_box' );
// сохраняем данные метаполя
function halloween_store_save_meta_box( $post_id ) {
	// проверяем, относится ли запись к типу Halloween Products
	// и были ли отправлены метаданные
	if ( get_post_type( $post_id ) == 'halloween-products'
			&& isset( $_POST['halloween_product_sku'] ) ) {
		// если установлено автосохранение, пропускаем сохранение данных
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
		// проверка из соображений безопасности
		check_admin_referer( 'meta-box-save', 'halloween-plugin' );
		// сохраняем данные метаполя в произвольных полях записи
		update_post_meta( $post_id, '_halloween_product_sku',
				sanitize_text_field( $_POST['halloween_product_sku'] ) );
		update_post_meta( $post_id, '_halloween_product_price',
				sanitize_text_field( $_POST['halloween_product_price'] ) );
		update_post_meta( $post_id, '_halloween_product_weight',
				sanitize_text_field( $_POST['halloween_product_weight'] ) );
		update_post_meta( $post_id, '_halloween_product_color',
				sanitize_text_field( $_POST['halloween_product_color'] ) );
		update_post_meta( $post_id, '_halloween_product_inventory',
				sanitize_text_field( $_POST['halloween_product_inventory'] ) );
	}
}
// зацепка-действие, чтобы создать сокращенный код для товаров
add_shortcode( 'hs', 'halloween_store_shortcode' );
// создаем сокращенный код
function halloween_store_shortcode( $atts, $content = null ) {
	global $post;
	extract( shortcode_atts( array(
					"show" => ''
					), $atts ) );
	// извлекаем настройки
	$hween_options_arr = get_option( 'halloween_options' );
	if ( $show == 'sku') {
		$hs_show = get_post_meta( $post->ID, '_halloween_product_sku', true );
	}elseif ( $show == 'price' ) {
		$hs_show = $hween_options_arr['currency_sign'].
			get_post_meta( $post->ID, '_halloween_product_price', true );
	}elseif ( $show == 'weight' ) {
		$hs_show = get_post_meta( $post->ID,
				'_halloween_product_weight', true );
	}elseif ( $show == 'color' ) {
		$hs_show = get_post_meta( $post->ID,
				'_halloween_product_color', true );
	}elseif ( $show == 'inventory' ) {
		$hs_show = get_post_meta( $post->ID,
				'_halloween_product_inventory', true );
	}
	// возвращаем значение сокращенного кода для отображения
	return $hs_show;
}

// зацепка-действие, чтобы создать виджет
add_action( 'widgets_init', 'halloween_store_register_widgets' );
// регистрируем виджет
function halloween_store_register_widgets() {
	register_widget( 'hs_widget' );
}
//hs_widget class
class hs_widget extends WP_Widget {
// создаем виджет
	public function __construct() {
	 	parent::__construct("hs-widget-class", "Simple Text Widget",
		 		array("description" => __( 'Display Halloween Products',	'halloween-plugin' ) ));
 	}
//создаем форму настроек виджета
	function form( $instance ) {
		$defaults = array(
				'title' => __( 'Products', 'halloween-plugin' ),
				'number_products' => '3' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = $instance['title'];
		$number_products = $instance['number_products'];
		?>
			<p><?php _e('Title', 'halloween-plugin') ?>:
			<input class="widefat"
			name="<?php echo $this->get_field_name( 'title' ); ?>"
			type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
			<p><?php _e( 'Number of Products', 'halloween-plugin' ) ?>:
			<input name="
			<?php echo $this->get_field_name( 'number_products' ); ?>"
			type="text" value="<?php echo esc_attr( $number_products ); ?>"
			size="2" maxlength="2" />
			</p>
			<?php
	}		
	// сохраняем настройки виджета
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['number_products'] = absint( $new_instance['number_products'] );
		return $instance;
	}
	// отображаем виджет
	function widget( $args, $instance ) {
		global $post;
		extract( $args );
		echo $before_widget;
		$title = apply_filters( 'widget_title', $instance['title'] );
		$number_products = $instance['number_products'];
		if ( ! empty( $title ) ) { echo $before_title . esc_html( $title )
			. $after_title; };
		// произвольный запрос, чтобы извлечь продукты
		$args = array(
				'post_type' => 'halloween-products',
				'posts_per_page' => absint( $number_products )
				);
		$dispProducts = new WP_Query();
		$dispProducts->query( $args );
		while ( $dispProducts->have_posts() ) : $dispProducts->the_post();
		// извлекаем массив настроек
		$hween_options_arr = get_option( 'halloween_options' );
		// извлекаем значения произвольных полей
		$hs_price = get_post_meta( $post->ID,
				'_halloween_product_price', true );
		$hs_inventory = get_post_meta( $post->ID,
				'_halloween_product_inventory', true );
		?>
			<p>
			<a href="<?php the_permalink(); ?>"
			rel="bookmark"
			title="<?php the_title_attribute(); ?> Product Information">
			<?php the_title(); ?>
			</a>
			</p>
			<?php
			echo '<p>' . __( 'Price', 'halloween-plugin' ). ': '
			.$hween_options_arr['currency_sign'] .$hs_price .'</p>';
		// проверяем, включена ли опция отображения списка продуктов
		if ( $hween_options_arr['show_inventory'] ) {
			// отображаем метаданные для продукта
			echo '<р>' .__( 'Stock', 'halloween-plugin' ). ': '
				.$hs_inventory .'</р>';
		}
		echo '<hr>';
		endwhile;
		wp_reset_postdata();
		echo $after_widget;
	}
}
?>
