<div class="bb_box">
	<?php
		wp_nonce_field( 'bbridge_color_meta', 'bbridge_color_meta_nonce' );
	?>
	<p class="meta-options bb_field">
		<input id="bb_text_color" type="text"  class="bb_color_picker" name="cibb_text_hover_color" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'cibb_text_hover_color', true ) ); ?>" data-default-color="#261d52">
	</p>	
</div>
