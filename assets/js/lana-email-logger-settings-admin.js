jQuery(function () {
    var $lanaEmailLoggerSettingsPage = jQuery('body.lana-email-logger_page_lana-email-logger-settings');

    /**
     * Lana Email Logger
     * display cleanup
     */
    function lanaEmailLoggerDisplayCleanup() {
        var $cleanup = $lanaEmailLoggerSettingsPage.find('tr' + jQuery(this).data('tr-target')),
            selected = jQuery(this).val();

        if ('1' === selected) {
            /** display */
            $cleanup.addClass('d-table-row');
        }
        else {
            /** hide */
            $cleanup.removeClass('d-table-row');
        }
    }

    /** display cleanup amount */
    $lanaEmailLoggerSettingsPage.find('#lana-email-logger-cleanup-by-amount').on('change', lanaEmailLoggerDisplayCleanup).trigger('change');

    /** display cleanup time */
    $lanaEmailLoggerSettingsPage.find('#lana-email-logger-cleanup-by-time').on('change', lanaEmailLoggerDisplayCleanup).trigger('change');
});