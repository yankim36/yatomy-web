<?php
/**
 * Remove plugin options on uninstall.
 *
 * @package Atomy_QR_Landing
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'atomy_qrl_settings' );
delete_option( 'atomy_qrl_flush_rewrites' );
