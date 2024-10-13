<?php
/**
 * Plugin Name: Test MU Plugin for all-in-one-messaging.
 *
 * This mu-plugin contains test for each service's each adapter.
 * Just un-comment the one you want to test and see it in action.
 * Please change the input values as required.
 *
 * @package all-in-one-messaging
 */

/**
 * SMS: Default adapter Messaging.
 */
// add_action( 'init', function () {
// 	$test = \Souptik\AIOMessaging\SMS\send( [ '+xxxxxxxxxxxx' ], 'Yay its working!' );
// 	echo '<h4>SMS: Default Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	print_r( $test );
// 	echo '</pre>';
// 	exit();
// } );

/**
 * SMS: Twilio Messaging.
 */
// add_action( 'init', function () {
// 	$test = \Souptik\AIOMessaging\SMS\send( [ '+xxxxxxxxxxxx' ], 'Yay its working!', 'twilio' );
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
// 	$test = \Souptik\AIOMessaging\SMS\send( [ '+xxxxxxxxxxxx' ], 'Yay its working!', 'telesign' );
// 	echo '<h4>SMS: Telesign Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	print_r( $test );
// 	echo '</pre>';
// 	exit();
// } );

/**
 * Email: WP Mail override.
 */
// add_action( 'init', function () {
// 	$test = wp_mail(
// 		[ 'dev2@souptik.dev' ],
// 		'Yay its working!',
// 		'This is some long mail body - from <strong>wp_mail</strong>.',
// 		[],
// 		[]
// 	);
// 	echo '<h4>Email: WP Mail Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	echo $test ? 'Success!' : 'Failed :(';
// 	echo '</pre>';
// 	exit();
// } );

/**
 * Email: Default adapter Messaging.
 */
// add_action( 'init', function () {
// 	$test = \Souptik\AIOMessaging\Email\send(
// 		[ 'dev2@souptik.dev' ],
// 		'Yay its working!',
// 		'This is some long mail body.',
// 	);
// 	echo '<h4>Email: Default Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	print_r( $test );
// 	echo '</pre>';
// 	exit();
// } );

/**
 * Email: Mailgun Messaging.
 */
// add_action( 'init', function () {
// 	$test = \Souptik\AIOMessaging\Email\send(
// 		[ 'dev2@souptik.dev' ],
// 		'Yay its working!',
// 		'<h1>This is some long mail body.</h1>',
// 		'Souptik',
// 		'dev1@souptik.dev',
// 		[],
// 		'mailgun'
// 	);
// 	echo '<h4>Email: Mailgun Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	print_r( $test );
// 	echo '</pre>';
// 	exit();
// } );

/**
 * Email: Mailgun Messaging - with headers.
 */
// add_action( 'init', function () {
// 	$test = \Souptik\AIOMessaging\Email\send(
// 		[ 'dev2@souptik.dev' ],
// 		'Yay its working!',
// 		'This is some long mail body.',
// 		'Souptik',
// 		'dev1@souptik.dev',
// 		[
// 			'cc' => [
// 				[
// 					'name'  => 'CC Test',
// 					'email' => 'cc@souptik.dev',
// 				],
// 			],
// 			'attachments' => [
// 				trailingslashit( WP_CONTENT_DIR ) . '/mu-plugins/test-all-in-one-messaging.php',
// 		 		'SameFileDifferentName.php' => trailingslashit( WP_CONTENT_DIR ) . '/mu-plugins/test-all-in-one-messaging.php',
// 			],
// 		],
// 		'mailgun'
// 	);
// 	echo '<h4>Email: Mailgun Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	print_r( $test );
// 	echo '</pre>';
// 	exit();
// } );

/**
 * Email: Brevo Messaging - with headers.
 */
// add_action( 'init', function () {
// 	$test = \Souptik\AIOMessaging\Email\send(
// 		[ 'dev2@souptik.dev' ],
// 		'Yay its working!',
// 		'This is some long mail body.',
// 		'',
// 		'dev1@souptik.dev',
// 		[
// 			'reply_to_name' => 'Reply Test',
// 			'reply_to_email' => 'dev2@souptik.dev',
// 			'cc' => [
// 				[
// 					'name'  => 'CC Test',
// 					'email' => 'cc@souptik.dev',
// 				],
// 			],
// 			'attachments' => [
// 				// For Brevo only few supported file types are allowed, for example PHP is not supported.
// 		 		'SameFileDifferentName.txt' => trailingslashit( WP_CONTENT_DIR ) . '/mu-plugins/test-all-in-one-messaging.php',
// 			],
// 		],
// 		'brevo'
// 	);
// 	echo '<h4>Email: Brevo Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	print_r( $test );
// 	echo '</pre>';
// 	exit();
// } );

/**
 * Email: AWS SES Messaging - with headers.
 */
// add_action( 'init', function () {
// 	$test = \Souptik\AIOMessaging\Email\send(
// 		[ 'dev2@souptik.dev' ],
// 		'Yay its working!',
// 		'<strong>This is some</strong> long mail body.',
// 		'',
// 		'dev1@souptik.dev', // Use verified email address for the AWS credential you are using.
// 		[
// 			'reply_to_name' => 'Reply Test',
// 			'reply_to_email' => 'dev2@souptik.dev',
// 			'cc' => [
// 				[
// 					'name'  => 'CC Test',
// 					'email' => 'cc@souptik.dev',
// 				],
// 			],
// 			'attachments' => [
// 		 		'SameFileDifferentName.php' => trailingslashit( WP_CONTENT_DIR ) . '/mu-plugins/test-all-in-one-messaging.php',
// 			],
// 		],
// 		'aws_ses'
// 	);
// 	echo '<h4>Email: AWS SES Messaging -- Triggered from `test-mu-plugin`</h4>';
// 	echo '<pre>';
// 	print_r( $test );
// 	echo '</pre>';
// 	exit();
// } );
