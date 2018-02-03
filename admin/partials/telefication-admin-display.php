<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://telefication.ir/wordpress-plugin
 * @since      1.0.0
 *
 * @package    Telefication
 * @subpackage Telefication/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h1><?php _e( 'Telefication Setting', 'telefication' ); ?> </h1>
    <div class="telefication-wrapper">
        <form method="post" action="options.php">

			<?php
			settings_fields( 'telefication_option_group' );
			do_settings_sections( 'telefication-setting' );
			submit_button();
			?>

        </form>
    </div>
    <p>
		<?php
		printf( __( 'If you like Telefication please leave us a %s rating. A huge thank you from us in advance!', 'telefication' ),
			'<a href="https://wordpress.org/support/plugin/telefication/reviews/#new-post" target="_blank">★★★★★</a>' );
		?>
    </p>
</div>