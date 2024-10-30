<?php
defined( 'ABSPATH' ) or die();

/** @var Lana_Email $lana_email */
global $lana_email;

$lana_email->headers = str_replace( '\n', "\n\n", $lana_email->headers );
?>

<h3>
	<?php _e( 'Headers', 'lana-email-logger' ); ?>
</h3>
<div id="lana-email-logger-email-headers">

    <p><?php echo wpautop( esc_html( $lana_email->headers ) ); ?></p>

</div>

<br/>