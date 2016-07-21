<?php
/**
*  Widget
*/

class WPALF_Widget extends WP_Widget  {

	public function __construct() {
		parent::__construct(
			"WPALF_Widget",
			"Ajax Login Form",
			array("description" => "Виджет модальной формы входа на сайт")
			);
	}

	public function widget($args, $instance) {
		if (!is_user_logged_in()) { 
?>
 		<div class="wpalf">
			<a href="#" id="wpalf-btn"><?php echo __('Войти'); ?></a>
		</div>
<?php		} else {   ?>
		<div class="wpalf">
			<a href="<?php echo get_edit_user_link(); ?>" spfieldtype="null">Профиль</a>
		</div>
		<p><a href="<?php echo wp_logout_url(get_permalink()); ?>">Выйти</a></p>
		<div class="clear"></div>
<?php		}
		 /* if (!is_user_logged_in()) { ?>
		}
 		<div>
			<p><button id="wpalf-btn">Login</button></p>
		</div>
		<?php } else {?>
		<div>
			<a href="<?php echo get_edit_user_link(); ?>" spfieldtype="null">Профиль</a>
		</div>
		<p><a href="<?php echo wp_logout_url(get_permalink()); ?>">Выйти</a></p>
		<div class="clear"></div>
		<?php } ?>

		*/
	}



}


add_action('widgets_init', function(){
	register_widget('WPALF_Widget');
});

?>
