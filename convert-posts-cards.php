<?php
/**
 * Plugin Name:       Convert Posts 2 Cards
 * Plugin URI:        https://github.com/PhylomonTCG/convert-posts-cards
 * Description:       For Backwards compatibility
 * Version:           1.0.0
 * Author:            Phylogame Dev
 *
 */

/**
 *
 * @package Convert_Posts_2_Cards
 */
class Convert_Posts_2_Cards {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;


	protected $plugin_slug = 'convert-posts-cards';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		add_action( 'wp_ajax_convert_posts_to_cards', array( $this, 'convert_to_post_ajax' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_posts_page(
				'Convert Posts to Cards',
				'Posts 2 Cards',
				'manage_options',
				 $this->plugin_slug,
				 array( $this, 'display_plugin_admin_page' ));


	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {

		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	public function convert_to_post_ajax() {
		$this->phylo_bulk_convert_posts( $_POST['category'], $_POST['paged'], $_POST['post_type'] );
		die();
	}

	function phylo_bulk_convert_posts( $category_slug, $paged = 1, $post_type ) {
		$args = array(
			'orderby'			=> 'date',
			'posts_per_page'	=> 20,
			'post_status' 		=> 'any',
			'paged'				=> $paged,
			'category_name'		=> $category_slug
		);

		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {

			global $post;
			while ( $the_query->have_posts() ) {

				$the_query->the_post();

				// convert posts to cards

				$phylomon_classification_order	 = get_post_meta($post->ID, "_phylomon_classification_order",  true);

				// Temperature
		        $phylomon_temperatue             = maybe_unserialize( get_post_meta($post->ID, "_phylomon_temperature",true ) );

		        $my_post = array(
					 'post_title' 	=> $post->post_title,
					 'post_status' 	=> $post->post_status,
					 'post_author' 	=> $post->post_author,
					 'post_content' => $post->post_content,
					 'post_excerpt' => $post->post_excerpt,
					 'post_date'	=> $post->post_date,
					 'post_type'	=> $post_type
					);


				$post_id = wp_insert_post( $my_post );

				wp_set_object_terms( $post_id , explode( ",", $phylomon_classification_order ),  'classification' );

				echo '<a href="edit">edit</a> | <a href="'.get_permalink($post_id ).'">'.$post->post_title.'</a> -  '.$post_id.'<br />';


				// Latin Name
		        $phylomon_latin_name              = get_post_meta($post->ID, "_phylomon_latin_name",            true);

		        // Graphic
		        $phylomon_graphic_artist          = get_post_meta($post->ID, "_phylomon_graphic_artist",       true);
		        $phylomon_graphic_artist_url      = get_post_meta($post->ID, "_phylomon_graphic_artist_url",   true);
		        $phylomon_graphic                 = get_post_meta($post->ID, "_phylomon_graphic",              true);

		        // Photo
		        $phylomon_photo_artist            = get_post_meta($post->ID, "_phylomon_photo_artist",         true);
		        $phylomon_photo_artist_url        = get_post_meta($post->ID, "_phylomon_photo_artist_url",     true);
		        $phylomon_photo                   = get_post_meta($post->ID, "_phylomon_photo",                true);


		        $phylomon_size                    = get_post_meta($post->ID, "_phylomon_size",                 true);
		        $phylomon_food                    = get_post_meta($post->ID, "_phylomon_food",                 true);
		        $phylomon_hierarchy               = get_post_meta($post->ID, "_phylomon_hierarchy",            true);

		        // Habitat
		        $phylomon_habitat1                = get_post_meta($post->ID, "_phylomon_habitat1",             true);
		        $phylomon_habitat2                = get_post_meta($post->ID, "_phylomon_habitat2",             true);
		        $phylomon_habitat3                = get_post_meta($post->ID, "_phylomon_habitat3",             true);

		        // Card Color
		        $phylomon_card_color              = get_post_meta($post->ID, "_phylomon_card_color",           true);
		        // make sure the colour conaints the hash
		        if ( substr( $phylomon_card_color ,0,1 ) != "#") {
		        	$phylomon_card_color = "#".$phylomon_card_color;
		        }

		        // Temperature
		        $phylomon_temperatue             = maybe_unserialize(get_post_meta($post->ID, "_phylomon_temperature",true));


		        // Card Content
		        $phylomon_card_content			 = get_post_meta($post->ID, "_phylomon_card_content",          true);


		     	// Animal Classification
		     	$phylomon_classification_order	 = get_post_meta($post->ID, "_phylomon_classification_order",  true);

				// wiki
				$phylomon_wiki	 = get_post_meta($post->ID, "_phylomon_wiki",             true);
				// EOL
				$phylomon_eol	 = get_post_meta($post->ID, "_phylomon_eol",             true);

				/* DIY CARD DATA */

				// Card Text
				update_field( "field_517c835843f9b", $phylomon_card_content, $post_id );

				// Card Colour
				update_field( "field_517c83f74be9a", $phylomon_card_color, $post_id );

				// Graphic URL
				update_field( "field_517c81a7dae31", $phylomon_graphic, $post_id );

				// Graphic Artist Name
				update_field( "field_517c80abdae2e", $phylomon_graphic_artist, $post_id );

				// Graphic Artist URL
				update_field( "field_517c80dddae2f", $phylomon_graphic_artist_url, $post_id );

				// Graphic Source - Meta
				update_field( "field_517c814bdae30", "url", $post_id );



				// Latin Name
				update_field( "field_517c79b5406bd",  $phylomon_latin_name, $post_id );

				// Point Score not implemented in the other version.
				// update_field( "field_517c79b5496bd", $_POST['card_point_value'], $post_id );

				// Diet
				update_field( "field_517c75e3f7641", $phylomon_food , $post_id );

				// Food Chain Hierarchy
				update_field( "field_517c79f4406be", $phylomon_hierarchy, $post_id );

				// Scale
				update_field( "field_517c7b479fb94", $phylomon_size, $post_id );


				// Habitat 1
				update_field( "field_517c7cb221417", $phylomon_habitat1, $post_id );

				// Habitat 2
				update_field( "field_517c7d6b21418", $phylomon_habitat3, $post_id );

				// Habitat 3
				update_field( "field_517c7dc621419", $phylomon_habitat3, $post_id );

				// Temperature
				update_field( "field_517c7df02141a", $phylomon_temperatue, $post_id );

				// Name size - Meta
				update_field( "field_517c66e3f7641", "", $post_id );

				/* END OF DIY CARD DATA */

				// Wikipedia URL
				update_field( "field_517c81a7dae65", $phylomon_wiki, $post_id );

				// Encyclopedia of Life
				update_field( "field_517c81a7dae68", $phylomon_eol, $post_id );

				// Photo Source - Meta
				update_field( "field_517c82f243f99", "url", $post_id );

				// Photo URL
				update_field( "field_517c833943f9a", $phylomon_photo, $post_id );

				// Photo Artist Name
				update_field( "field_517c83b94be98", $phylomon_photo_artist, $post_id );

				// Photo Artist URL
				update_field( "field_517c83d54be99", $phylomon_photo_artist_url, $post_id );

				$redirect = true;
			} // end while

		} else {
			// no posts found

		}
		/* Restore original Post Data */
		wp_reset_postdata();

	}

}

add_action( 'plugins_loaded', array( 'Convert_Posts_2_Cards', 'get_instance' ) );
