<?php
defined( 'ABSPATH' ) or die();

/** @var Lana_Email $lana_email */
global $lana_email;
?>

<h3>
	<?php _e( 'Date', 'lana-email-logger' ); ?>
</h3>
<div id="lana-email-logger-email-date">

    <p><?php echo esc_html( $lana_email->date ); ?></p>

</div>

<br/>