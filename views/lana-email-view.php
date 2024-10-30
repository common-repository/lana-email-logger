<?php
defined( 'ABSPATH' ) or die();

/** @var Lana_Email $lana_email */
global $lana_email;
?>

<div class="wrap">
    <h1><?php _e( 'View Email', 'lana-email-logger' ); ?></h1>
    <hr/>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div id="post-body-content">
                <h2 class="email-title">
					<?php echo esc_html( $lana_email->subject ); ?>
                </h2>

                <pre><?php echo wp_kses_post( $lana_email->message ); ?></pre>
            </div>

            <div id="postbox-container-1" class="postbox-container">

				<?php do_action( 'lana_email_logger_email_view_postbox_1' ); ?>

            </div>

            <div id="postbox-container-2" class="postbox-container">

				<?php do_action( 'lana_email_logger_email_view_postbox_2' ); ?>

            </div>

            <br class="clear">

        </div>
    </div>
</div>