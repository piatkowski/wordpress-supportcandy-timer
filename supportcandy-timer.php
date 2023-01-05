<?php

/*
Plugin Name: Timer dla SupportCandy
Description: Wtyczka dodaje funkcjonalność Timera do wtyczki SupportCandy (supportcandy.net)
Version: 1.1.0
Author: Krzysztof Piątkowski
Author URI: https://github.com/piatkowski
License: GPLv2
Requires PHP: 7.4
*/

if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
	return;
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include __DIR__ . '/src/helper.php';
include __DIR__ . '/src/cf-timer-templates.php';
include __DIR__ . '/src/class-wpsc-cf-timer.php';

if ( ! class_exists( 'Timer_Support_Candy' ) ) :

	final class Timer_Support_Candy {

		public static $version = '1.1.0';
		public static $path = '';
		public static $url = '';

		/*
		 * Priority_ID => 'DR' or 'DK' or 'off' (dni robocze, dni kalendarzowe, wyłączony timer)
		 * /wp-admin/admin.php?page=wpsc-settings&section=ticket-priorities
		*/
		const DAYS = [
			'priority_1' => array(
				'days' => 14,
				'mode' => 'DR'
			),
			'priority_5' => array(
				'days' => 14,
				'mode' => 'DK'
			),
			'priority_6' => array(
				'days' => 14,
				'mode' => 'DK'
			),
			'priority_7' => array(
				'days' => 14,
				'mode' => 'DK'
			),
			'priority_8' => array(
				'days' => 14,
				'mode' => 'DK'
			),
			'priority_9' => array(
				'days' => 14,
				'mode' => 'DK'
			),
			0 => array(
				'days' => 0,
				'mode' => 'off'
			),
			'status_5' => array(
				'days' => 30,
				'mode' => 'DR'
			)
		];

		const STATUS_TRIGGER = array( 6 );

		const STATUS_STOP = array( 4, 11, 12, 14 );

		const TIMER_FIELD_ID = 38;

		public static function init() {
			self::$path = plugin_dir_path( __FILE__ );
			self::$url  = plugins_url( '/', __FILE__ );
			register_activation_hook( __FILE__, [ __CLASS__, 'activate' ] );
			//add_action( 'wpsc_before_ticket_widget', [ WPSC_CF_Timer_Templates::class, 'before_ticket_widget' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_enqueue_scripts' ], 11 );
			add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ], 11 );
		}

		public static function activate() {
			if ( ! class_exists( 'PSM_Support_Candy' ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				wp_die( 'Aktywuj najpierw wtyczkę SupportCandy!', 'Sprawdzanie zależności', array( 'back_link' => true ) );
			}
		}

		public static function admin_enqueue_scripts( $hook ) {
			if ( $hook === 'toplevel_page_wpsc-tickets' ) {
				self::enqueue_scripts();
			}
		}

		public static function enqueue_scripts() {
			wp_register_style(
				'sc_timer_css',
				self::$url . 'assets/css/style.min.css',
				false,
				self::$version
			);
			wp_enqueue_style( 'sc_timer_css' );
		}
	}

endif;

Timer_Support_Candy::init();