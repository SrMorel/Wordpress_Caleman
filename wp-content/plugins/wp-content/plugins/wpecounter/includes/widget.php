<?php

/**
 * WP Views Counter widget.
 *
 * @since 1.0.0
 */
class WPeCounter_Widget extends WP_Widget {

	/**
	 * Default arguments for the widget settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $defaults = array();

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'		 => 'widget-wpecounter',
			'description'	 => esc_html__('Display posts based on their views count.', 'wpecounter')
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'	 => 200,
			'height' => 350
		);

		/* Create the widget. */
		parent::__construct(
				'wpecounter',
				__('Views Counter', 'wpecounter'),
				$widget_options,
				$control_options
		);

		/* Set up defaults. */
		$this->defaults = array(
			'title'				 => esc_attr__('Most Popular', 'wpecounter'),
			'posts_per_page'	 => 10,
			'post_type'			 => 'post',
			'order'				 => 'DESC',
			'show_views_count'	 => true
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $sidebar
	 * @param  array  $instance
	 * @return void
	 */
	function widget($sidebar, $instance) {

		/* Set the $args for wp_get_archives() to the $instance array. */
		$args = wp_parse_args($instance, $this->defaults);

		/* Output the sidebar's $before_widget wrapper. */
		echo $sidebar['before_widget'];

		/* If a title was input by the user, display it. */
		if (!empty($args['title']))
			echo $sidebar['before_title'] . apply_filters('widget_title', $args['title'], $instance, $this->id_base) . $sidebar['after_title'];

		if (!isset($WPeCounterViews))
			$WPeCounterViews = new WPeCounterViews();

		/* Query the most/least viewed posts. */
		$loop = new WP_Query(
				array(
			'post_type'				 => $args['post_type'],
			'posts_per_page'		 => $args['posts_per_page'],
			'order'					 => $args['order'],
			'orderby'				 => 'meta_value_num',
			'ignore_sticky_posts'	 => true,
			'meta_key'				 => $WPeCounterViews->wpecounter_views_meta_key()
				)
		);

		if ($loop->have_posts()) :
			?>
			<ul class="wpecounter-list">

				<?php while ($loop->have_posts()) : $loop->the_post(); ?>
					<?php
					/**
					 * wpecounter_widget_item_template allow to customize style and layout of the widget popular posts list.
					 */
					$WidgetTemplate = apply_filters('wpecounter_widget_item_template',false,$args);
					if ( !$WidgetTemplate ) {
						?>
						<li>
							<?php the_title('<a href="' . get_permalink() . '">', '</a>'); ?> 
							<?php if (true == $args['show_views_count']) : ?>
								<?php wpecounter_post_views(array('text' => '(%s)', 'wrap' => '<span class="show_views_count">%2$s</span>')); ?>
							<?php endif; ?>
						</li>
						<?php
					}else{
						echo $WidgetTemplate;
					}
				endwhile;
				?>

			</ul><!-- .wpecounter-list -->

			<?php
		endif;

		/* Close the sidebar's widget wrapper. */
		echo $sidebar['after_widget'];
	}

	/**
	 * The update callback for the widget control options.  This method is used to sanitize and/or
	 * validate the options before saving them into the database.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $new_instance
	 * @param  array  $old_instance
	 * @return array
	 */
	function update($new_instance, $old_instance) {

		/* Strip tags. */
		$instance['title'] = strip_tags($new_instance['title']);

		/* Array map sanitize key. */
		$instance['post_type'] = array_map('sanitize_key', $new_instance['post_type']);

		/* Whitelist options. */
		$order = array('ASC', 'DESC');

		$instance['order'] = in_array($new_instance['order'], $order) ? $new_instance['order'] : 'DESC';

		/* Integers. */
		$instance['posts_per_page'] = absint($new_instance['posts_per_page']);

		/* Checkboxes. */
		$instance['show_views_count'] = isset($new_instance['show_views_count']) ? 1 : 0;

		/* Return sanitized options. */
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $instance
	 * @param  void
	 */
	function form($instance) {

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args((array) $instance, $this->defaults);

		$post_types	 = array();
		$_post_types = get_post_types(array('public' => true), 'objects');

		foreach ($_post_types as $_post_type) {
			if (post_type_supports($_post_type->name, 'wpecounter'))
				$post_types[] = $_post_type;
		}

		/* Create an array of order options. */
		$order = array(
			'ASC'	 => esc_attr__('Ascending', 'wpecounter'),
			'DESC'	 => esc_attr__('Descending', 'wpecounter')
		);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wpecounter'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" placeholder="<?php echo esc_attr($this->defaults['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:', 'wpecounter'); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>[]" size="4" multiple="multiple">
				<?php foreach ($post_types as $post_type) { ?>
					<option value="<?php echo esc_attr($post_type->name); ?>" <?php selected(in_array($post_type->name, (array) $instance['post_type'])); ?>><?php echo esc_html($post_type->labels->singular_name); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?php _e('Limit:', 'wpecounter'); ?></label>
			<input type="number" class="widefat code" size="5" min="1" id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>" value="<?php echo esc_attr($instance['posts_per_page']); ?>" placeholder="10" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:', 'wpecounter'); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
				<?php foreach ($order as $option_value => $option_label) { ?>
					<option value="<?php echo esc_attr($option_value); ?>" <?php selected($instance['order'], $option_value); ?>><?php echo esc_html($option_label); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_views_count'); ?>">
				<input class="checkbox" type="checkbox" <?php checked($instance['show_views_count'], true); ?> id="<?php echo $this->get_field_id('show_views_count'); ?>" name="<?php echo $this->get_field_name('show_views_count'); ?>" /> <?php _e('Show views count?', 'wpecounter'); ?></label>
		</p>
		<?php
	}

}
