<?php
/**
 * Plugin Name: Test MU Plugin for wp-messaging.
 *
 * This mu-plugin contains test for each service's each adapter.
 * Just un-comment the one you want to test and see it in action.
 * Please change the input values as required.
 *
 * @package wp-messaging
 */

/**
 * SMS: Twilio Messaging.
 */
// add_action( 'init', function () {
// 	$test = \Souptik\WPMessaging\SMS\send( [ '+xxxxxxxxxxx' ], 'Yay its working!', 'twilio' );
// 	echo '<h4>SMS: Twilio Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	print_r( $test );
// 	echo '</pre>';
// 	exit();
// } );

/**
 * SMS: Telesign Messaging.
 */
// add_action( 'init', function () {
// 	$test = \Souptik\WPMessaging\SMS\send( [ '+918334012176' ], 'Yay its working!', 'telesign' );
// 	echo '<h4>SMS: Telesign Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	print_r( $test );
// 	echo '</pre>';
// 	exit();
// } );
