<?php
/**
 * Plugin Name: Bankingbridge
 * Description: The BankingBridge plugin makes it super easy to embed our innovative software directly on your WordPress website.
 * Author: BankingBridge
 * Version: 1.0
 * Author URI: bankingbridge.com
 *
 * @package: bankingbridge
 * */

/**
 * Include CSS and JavaScript files.
 **/
function cibb_enqueue_scripts() {
	wp_enqueue_style( 'bb_custom_css', plugin_dir_url( __FILE__ ) . '/assets/css/cibb_custom.css', '', time() );
	wp_register_script( 'bb_lib', 'https://cdn.bankingbridge.com/assets/external/index.js', '', time(), false );
	wp_register_script( 'bb_custom_js', plugin_dir_url( __FILE__ ) . '/assets/js/cibb_custom_js.js', array( 'jquery', 'bb_lib' ), time(), false );
}
add_action( 'wp_enqueue_scripts', 'cibb_enqueue_scripts' );

/**
 * Add Scripts And Styles
 */
function cibb_admin_enqueue_scripts() {
	wp_enqueue_style( 'bb_custom_admin_css', plugin_dir_url( __FILE__ ) . 'assets/admin/css/cibb_admin.css', '', time() );
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'bb_custom_admin_js', plugin_dir_url( __FILE__ ) . 'assets/admin/js/cibb_custom_admin.js', array( 'wp-color-picker' ), time(), false );
}
add_action( 'admin_enqueue_scripts', 'cibb_admin_enqueue_scripts' );

/**
 * Add async to bbpress library
 *
 * @param strting $tag tag to be synched.
 * @param array   $handle slug.
 */
function cibb_async_scripts( $tag, $handle ) {
	// Just return the tag normally if this isn't one we want to async.
	$skip_lib_arr = array( 'bb_lib', 'bb_custom_js' );
	if ( ! in_array( $handle, $skip_lib_arr ) ) {
		return $tag;
	}
	return str_replace( ' src', ' data-cfasync="false" src', $tag );
}
add_filter( 'script_loader_tag', 'cibb_async_scripts', 10, 2 );

/**
 * Create a shortcode for adding custom buttons.
 *
 * @param array  $atts Shortcode Attributes.
 * @param string $content post contents.
 * @param array  $shortcode_tag html tag of shortcode.
 **/
function cibb_deploy_custom_buttons( $atts, $content, $shortcode_tag ) {
	ob_start();
	$post_id           = $atts['button'];
	$bb_id             = 'bkbg-' . wp_rand();
	$button_text_color = get_post_meta( $post_id, 'cibb_text_hover_color', true );
	if ( isset( $atts['hover_state_color'] ) ) {
		$button_text_color = $atts['hover_state_color'];
	}
	$bb_js_var = shortcode_atts(
		array(
			'app_key'           => '',
			'type'              => 'inline',
			'button'            => '',
			'button_text_color' => $button_text_color,
			'bbid'				=> $bb_id,
		),
		$atts
	);
	wp_enqueue_script( 'bb_lib' );
	wp_enqueue_script( 'bb_custom_js' );
	wp_localize_script( 'bb_custom_js', 'bb_js_object', $bb_js_var );

	?>
	<div id='<?php echo esc_attr( $bb_id ); ?>'></div>
	<style>
		.accent-color {
			color:<?php echo esc_attr( $button_text_color ); ?> !important;
		}
	</style>
	<?php
	switch ( $bb_js_var['type'] ) {
		case 'popup':
			$post_id      = $bb_js_var['button'];
			$post_content = get_post( $post_id );
			$content      = $post_content->post_content;
			?>
			<div id="<?php echo esc_attr( $bb_id ); ?>-buttons" style="text-align: center; margin-top: 20px;" class="popup bkbg_popup" data-circle-color="<?php echo esc_attr( $button_text_color ); ?>">
				<div class="bkbg_buttons buttons d-flex buttons--list  is-rounded flex-column flex-md-row justify-content-center">
					<?php if ( $post_id ) { ?>
						<?php echo $content; ?>
					<?php } else { ?>
					<button tabindex="0" class="buttons__item is-alt-btns" type="button" onclick="main('purchase')">
						<div class="buttons__icon" style="background-image: url('https://cdn.bankingbridge.com/uploads/greybrownblack+purchase+for+button.svg')">
							<span class="accent-color">Home Purchase</span>
						</div>
					</button>
					<button tabindex="0" class="buttons__item is-last is-alt-btns" type="button" onclick="main('refinance')">
						<div class="buttons__icon" style="background-image: url(&quot;https://cdn.bankingbridge.com/uploads/greybrownblack+refinance+for+button.svg&quot;);">
							<span class="accent-color">Home Refinance</span>
						</div>
					</button>
					<?php } ?>
				</div>
				</div>
			<?php
			break;
		case 'inline':
			?>
				<div style="text-align: center; display: none;" class="inline">
					<?php if ( 'purchase' === $bb_js_var['button'] ) { ?>
						<button class="ybtn purchase" onclick="main('purchase')"><span>Purchase</span></button>	
					<?php } elseif ( 'refinance' === $bb_js_var['button'] ) { ?>
						<button class="ybtn refinance" onclick="main('refinance')"><span>Refinance</span></button>
					<?php } else { ?>
						<button class="ybtn purchase" onclick="main('purchase')"><span>Purchase</span></button>	
						<button class="ybtn refinance" onclick="main('refinance')"><span>Refinance</span></button>
					<?php } ?>
				</div>
				<?php
			break;
		default:
			?>
				<div style="text-align: center; display: none;" class="inline">
					<?php if ( 'purchase' === $bb_js_var['button'] ) { ?>
						<button class="ybtn purchase" onclick="main('purchase')"><span>Purchase</span></button>	
					<?php } elseif ( 'refinance' === $bb_js_var['button'] ) { ?>
						<button class="ybtn refinance" onclick="main('refinance')"><span>Refinance</span></button>
					<?php } else { ?>
						<button class="ybtn purchase" onclick="main('purchase')"><span>Purchase</span></button>	
						<button class="ybtn refinance" onclick="main('refinance')"><span>Refinance</span></button>
					<?php } ?>
				</div>
				<?php
	}
	?>
	<?php
	return ob_get_clean();
}
add_shortcode( 'bg_deploy_custom_buttons', 'cibb_deploy_custom_buttons' );

/**
 *  Create a custom post type for buttons
 * */
function cibb_create_posttype_button_designs() {
	register_post_type(
		'bb_button_designs',
		array(
			'labels' => array(
				'name'          => __( 'Button Designs' ),
				'singular_name' => __( 'Button Designs' ),
			),
			'show_in_menu' => 'bankingbridge',
			'public'      => true,
			'has_archive' => false,
			'rewrite'     => array( 'slug' => 'bb_button_designs' ),
		)
	);
}
add_action( 'init', 'cibb_create_posttype_button_designs' );

/**
 * Create BankingBridge Menu in admin.
 */
function cibb_admin_menu_bb() {
	add_menu_page(
		'BankingBridge',
		'BankingBridge',
		'read',
		'bankingbridge',
		'',
		plugin_dir_url( __FILE__ ) . '/assets/icon/Icon.png',
		40
	);
}
add_action( 'admin_menu', 'cibb_admin_menu_bb' );

/**
 * Add the custom columns to the button desing post type.
 *
 * @param array $columns table columns name on post list page in admin.
 * */
function cibb_shortcode_column( $columns ) {
	$columns['shortcode'] = __( 'Shortcode', 'bb' );
	$columns['action']    = __( 'Action', 'bb' );
	return $columns;
}
add_filter( 'manage_bb_button_designs_posts_columns', 'cibb_shortcode_column' );

/**
 * Contents for column design in post list page admin.
 *
 * @param string $column Name of column.
 * @param int    $post_id Id of post.
 */
function cibb_design_column( $column, $post_id ) {
	$hover_state_color = get_post_meta( $post_id, 'cibb_text_hover_color', true );
	if ( '' === $hover_state_color ) {
		$hover_state_color = '#0177df';
	}
	switch ( $column ) {
		case 'shortcode':
			?>
			<p>
				[bg_deploy_custom_buttons app_key=XXX type=popup button="<?php echo esc_attr( $post_id ); ?>" hover_state_color="<?php echo esc_attr( $hover_state_color ); ?>"]	
				<input class="bb_input" value='[bg_deploy_custom_buttons app_key=XXX type=popup button=<?php echo esc_attr( $post_id ); ?> hover_state_color="<?php echo esc_attr( $hover_state_color ); ?>"] '/>
			</p>
			<?php
			break;
		case 'action':
			?>
				<button class="bb_copy_btn">
					Copy
				</button>
			<?php
			break;
	}
}
add_action( 'manage_bb_button_designs_posts_custom_column', 'cibb_design_column', 10, 2 );

/**
 * Instructions for using button designs.
 */
function cibb_button_design_instruction() {
	$screen = get_current_screen();
	if ( 'edit-bb_button_designs' === $screen->id ) {
		add_action( 'all_admin_notices', function() {
			?>
			<div class="bbride_instruction_wrapper">
				<h2>Shortcode Instructions</h2>
				<p>
					Copy the shortcode below for the default buttons and no extra styles needed and paste it into the page. You can edit the color of the text, the border when hover, and the loader icon. <br> if you wish to customize your buttons further, you can do so below by clicking on add new next to button designs.
				</p>
				<b>Choose Color</b>
				<input id="bb_default_color" type="text"  class="bb_default_color" name="bb_default_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'cibb_text_hover_color', true ) ); ?>" data-default-color="#1a154a">
				<p>
					<b>[bg_deploy_custom_buttons app_key=XXX hover_state_color="<span class="bb_default_color_txt">#1a154a</span>" type="popup" ]</b>
				</p>
			</div>
			<p>
				<b>Shorcode Parameters</b>	
			</p>
			<ul>
				<li><b>app_key :</b> 
					Insert in your app key</li>
				<li>
					<b>type :</b> 
					popup / inline
				</li>
				<li>
					<b>button:</b> 
					design of the button (If not passed default design will be rendered)
				</li>
				<li>
					<b>hover_state_color: </b>
					Color for Non-hover text, Border color on hover and cirlce color.
				</li>
			</ul>
			<?php
		});
	}
}
add_action( 'load-edit.php', 'cibb_button_design_instruction' );

/**
 * Set default buttons designs
 *
 * @param string $content Current post contents.
 * @param object $post    Current post object.
 */
function cibb_default_button_design( $content, $post ) {
	switch ( $post->post_type ) {
		case 'bb_button_designs':
			ob_start();
			?>
			<button tabindex="0" class="buttons__item  is-alt-btns" type="button" onclick="main('purchase')">
				<div class="buttons__icon " style="background-image: url('https://cdn.bankingbridge.com/uploads/greybrownblack+purchase+for+button.svg')">
					<span class="accent-color">Home Purchase</span>
				</div>
			</button>
			<button tabindex="0" class="buttons__item is-last is-alt-btns" type="button" onclick="main('refinance')">
				<div class="buttons__icon " style="background-image: url('https://cdn.bankingbridge.com/uploads/greybrownblack+refinance+for+button.svg')">
					<span class="accent-color">Home Refinance</span>
				</div>
			</button>
			<?php
			$content = ob_get_clean();
			break;
	}
	return $content;
}
add_filter( 'default_content', 'cibb_default_button_design', 10, 2 );

/**
 * Disable wyswyg editor for shortcode.
 *
 * @param string $default Default post contents.
 */
function cibb_disable_wyswyg_for_btn_design( $default ) {
	global $post;
	if ( 'bb_button_designs' === get_post_type( $post ) ) {
		return false;
	}
	return $default;
}
add_filter( 'user_can_richedit', 'cibb_disable_wyswyg_for_btn_design' );


/**
 * Register meta boxes.
 */
function cibb_register_meta_boxes() {
	add_meta_box( 'bb-1', __( 'Choose Hover Border, Loader Icon and Non-hover text color', 'cibb' ), 'cibb_display_callback', 'bb_button_designs' );
}
add_action( 'add_meta_boxes', 'cibb_register_meta_boxes' );

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function cibb_display_callback( $post ) {
	include plugin_dir_path( __FILE__ ) . './admin/partials/view-button-form.php';
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID.
 */
function cibb_save_meta_box( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['bbridge_color_meta_nonce'] ) ) {
		$nonce = sanitize_text_field( wp_unslash( $_POST['bbridge_color_meta_nonce'] ) );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'bbridge_color_meta' ) ) {
			return;
		}
	}
	$fields = array(
		'cibb_text_hover_color',
	);
	foreach ( $fields as $field ) {
		if ( array_key_exists( $field, $_POST ) ) {
			$val = sanitize_text_field( wp_unslash( $_POST[$field] ) );
			update_post_meta( $post_id, $field, $val );
		}
	}
}
add_action( 'save_post', 'cibb_save_meta_box' );

