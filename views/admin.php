<div class="wrap">
	<h2>Convert Posts to Cards</h2>

	<?php

	if ( empty( $_POST ) ) { ?>
	<form action="" method="POST">
		<!-- <input type="hidden" name="page" value="convert-posts-cards2" /> -->
		<?php wp_nonce_field( 'convert-posts-to-cards', 'nonce' ); ?>
		<p><input type="text" name="category_text" style="width:300px" placeholder="Enter the category slug to convert"><br />
		</p>
		<p>
		<label><input type="radio" name="post_type_text" value="card"  /> Card</label><br />
		<label><input type="radio" name="post_type_text" value="diy-card"  /> DIY Card</label><br />
		</p>

		<input type="submit" value="Convert Posts" class="button-primary" />
	</form>
	<?php  }
	if ( wp_verify_nonce( $_POST['nonce'], 'convert-posts-to-cards' ) ) {

		if ( ! empty( $_POST['category_text'] )  && ! empty( $_POST['post_type_text'] ) ) { ?>
			<div id="new-cards-shell">
			<?php $this->phylo_bulk_convert_posts( trim( $_POST['category_text'] ), 1, $_POST['post_type_text'] ); ?>
			</div>
		<script type="text/javascript">
			var paged = 2;
			var category_page = '<?php echo trim( esc_js( $_POST['category_text'] ) ); ?>';
			var post_type 	  = '<?php echo trim( esc_js( $_POST['post_type_text'] ) ); ?>';
			var new_shell = jQuery( '#new-cards-shell' );
			var convert_posts_to_cards_ajax = function( data ) {

				jQuery.post( ajaxurl, data, function(response) {

					if ( response != 'none' ){
						new_shell.append(response);
					paged = paged+1;
					var data = {
						action: 	'convert_posts_to_cards',
						paged: 	paged,
						category: 	category_page,
						post_type: 	post_type
					};

					convert_posts_to_cards_ajax( data );
					} else {
						new_shell.append("<h2>Done!</h2>");
					}
				});
			}
			var data = {
				action: 	'convert_posts_to_cards',
				paged: 		paged,
				category: 	category_page,
				post_type: 	post_type
			};
			convert_posts_to_cards_ajax( data );
		</script>
		<?php
		}
	} ?>
</div>
