<?php

if (! class_exists('Nexus_WP_GPL_Bootstrap_Navwalker')) {
	/**
	 * Nexus_WP_GPL_Bootstrap_Navwalker class.
	 *
	 * @extends WP_Bootstrap_Navwalker which is covered by GPL 3+
	 * 
	 * @license The start_el mmethod is a duplication of WP_Bootstrap_Navwalker with slight changes to class names. Therefore the code must be published with GPL 3+ licence. See https://github.com/wp-bootstrap/wp-bootstrap-navwalker for the original source
	 */
	class Nexus_WP_GPL_Bootstrap_Navwalker extends WP_Bootstrap_Navwalker
	{
		/**
		 * Start El.
		 *
		 * @see Walker::start_el()
		 * @since 1.0.0
		 *
		 * @param string   $output Used to append additional content (passed by reference).
		 * @param WP_Post  $item   Menu item data object.
		 * @param int      $depth  Depth of menu item. Used for padding.
		 * @param stdClass $args   An object of wp_nav_menu() arguments.
		 * @param int      $id     Current item ID.
		 */
		public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
		{
			/**
			 * Dividers, Headers or Disabled
			 * =============================
			 * Determine whether the item is a Divider, Header, Disabled or regular
			 * menu item. To prevent errors we use the strcasecmp() function to so a
			 * comparison that is not case sensitive. The strcasecmp() function returns
			 * a 0 if the strings are equal.
			 */
			if (0 === strcasecmp($item->attr_title, 'divider') && 1 === $depth) {
				$output .= '<li role="presentation" class="divider">';
			} elseif (0 === strcasecmp($item->title, 'divider') && 1 === $depth) {
				$output .= '<li role="presentation" class="divider">';
			} elseif (0 === strcasecmp($item->attr_title, 'dropdown-header') && 1 === $depth) {
				$output .= '<li role="presentation" class="dropdown-header">' . esc_attr($item->title);
			} elseif (0 === strcasecmp($item->attr_title, 'disabled')) {
				$output .= '<li role="presentation" class="disabled"><a href="#">' . esc_attr($item->title) . '</a>';
			} else {
				$atts        = array();

				$classes     = empty($item->classes) ? array() : (array) $item->classes;
				if (0 === $depth) {
					$classes[] = 'nav-item'; // First level.
				}
				$classes[]   = 'menu-item-' . $item->ID;
				$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
				if ($args->has_children) {
					// $class_names .= ' dropdown';
				}
				if (preg_grep('/^current/', $classes)) {
					$atts['aria-current'] = 'page';
				}
				// w-auto d-inline-block  me-3 me-lg-0 
				$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

				$id          = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
				$id          = $id ? ' id="' . esc_attr($id) . '"' : '';

				$output     .= '<li itemscope="itemscope" itemtype="https://www.schema.org/SiteNavigationElement"' . $id . $class_names . '>';

				if (empty($item->attr_title)) {
					$atts['title'] = ! empty($item->title) ? strip_tags($item->title) : '';
				} else {
					$atts['title'] = $item->attr_title;
				}

				$atts['target'] = ! empty($item->target) ? $item->target : '';
				$atts['rel']    = ! empty($item->xfn) ? $item->xfn : '';
				// If item has_children add atts to a.
				if ($args->has_children && 0 === $depth) {
					$atts['href']           = '#';
					$atts['data-bs-toggle'] = 'dropdown';
					$atts['class']          = 'text-xl-center dropdown-toggle nav-link';
					$atts['aria-expanded']  = 'false';
				} else {
					$atts['href'] = ! empty($item->url) ? $item->url : '';
					if ($depth > 0) {
						$atts['class'] = ''; // Dropdown item.
					} else {
						$atts['class'] = 'nav-link'; // First level.
					}
					if (in_array('current-menu-item', $classes)) {
						$atts['class'] .= ' active';
					}
				}
				$atts       = apply_filters('nav_menu_link_attributes', $atts, $item, $args);
				$attributes = '';
				foreach ($atts as $attr => $value) {
					if (! empty($value)) {
						$value       = ('href' === $attr) ? esc_url($value) : esc_attr($value);
						$attributes .= ' ' . $attr . '="' . $value . '"';
					}
				}
				$item_output = $args->before;

				/*
				 * Glyphicons/Font-Awesome
				 * ===========
				 * Since the the menu item is NOT a Divider or Header we check the see
				 * if there is a value in the attr_title property. If the attr_title
				 * property is NOT null we apply it as the class name for the glyphicon.
				 */
				if (! empty($item->attr_title)) {
					$pos = strpos(esc_attr($item->attr_title), 'glyphicon');
					if (false !== $pos) {
						$item_output .= '<a' . $attributes . '><span class="glyphicon ' . esc_attr($item->attr_title) . '" aria-hidden="true"></span>&nbsp;';
					} else {
						$item_output .= '<a' . $attributes . '><i class="fa ' . esc_attr($item->attr_title) . '" aria-hidden="true"></i>&nbsp;';
					}
				} else {
					$item_output .= '<a' . $attributes . '>';
				}
				$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
				$item_output .= ($args->has_children && 0 === $depth) ? ' <span class="caret"></span></a>' : '</a>';
				$item_output .= $args->after;
				$output      .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
			}
		}
	}
}
