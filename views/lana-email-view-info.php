<?php
defined( 'ABSPATH' ) or die();

/** @var Lana_Email $lana_email */
global $lana_email;
?>

<h3>
	<?php _e( 'Information', 'lana-email-logger' ); ?>
</h3>
<div id="lana-email-logger-email-info">

	<?php if ( ! empty( $lana_email->email_to ) ): ?>
        <p>
			<?php _e( 'Email to', 'lana-email-logger' ); ?>:
			<?php echo esc_html( $lana_email->email_to ); ?>
        </p>
	<?php endif; ?>

	<?php if ( ! empty( $lana_email->username ) ): ?>
        <p>
			<?php _e( 'Username', 'lana-email-logger' ); ?>:
			<?php echo esc_html( $lana_email->username ); ?>
        </p>
	<?php endif; ?>

	<?php if ( ! empty( $lana_email->user_ip ) ): ?>
        <p>
			<?php _e( 'User IP', 'lana-email-logger' ); ?>:
			<?php echo esc_html( $lana_email->user_ip ); ?>
        </p>
	<?php endif; ?>

	<?php if ( ! empty( $lana_email->user_agent ) ): ?>
        <p>
			<?php _e( 'User Agent', 'lana-email-logger' ); ?>:
			<?php echo esc_html( $lana_email->user_agent ); ?>
        </p>
	<?php endif; ?>

</div>

<br/>