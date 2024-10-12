<?php
/**
 * Dummy adapter: Main Sender.
 *
 * This file will not be present if Utopia provides the adapter you are looking for. Just use that!
 *
 * @package all-in-one-messaging
 */

namespace Souptik\AIOMessaging\Email\Adapters\Dummy;

use Exception;
use Utopia\Messaging\Adapter\Email as EmailAdapter;
use Utopia\Messaging\Messages\Email as EmailMessage;
use Utopia\Messaging\Messages\Email\Attachment;
use Utopia\Messaging\Response;

/**
 * Dummy class.
 */
class Dummy extends EmailAdapter {

	/**
	 * Adapter Name.
	 *
	 * @var string
	 */
	protected const NAME = 'Dummy'; // phpcs:ignore Travelopia.Whitespace.GroupedConst.AddEmptyLineBeforeConstGroup

	/**
	 * Constructor.
	 *
	 * @param  string $api_key_or_something Your Dummy API key to authenticate with the API or something else.
	 * @param  string $something_else       Some other setting.
	 */
	public function __construct( private string $api_key_or_something = '', private string $something_else = '' ) {
		// Silence is golden.
	}

	/**
	 * Get adapter name.
	 *
	 * @return string
	 */
	public function getName(): string {
		// Return the name.
		return static::NAME;
	}

	/**
	 * Get adapter description.
	 *
	 * @return int
	 */
	public function getMaxMessagesPerRequest(): int {
		// Return the maximum messages per request.
		return 1000;
	}

	/**
	 * Core function to send the email.
	 *
	 * @param ?EmailMessage $message Email message object.
	 *
	 * @throws Exception If the message is invalid.
	 *
	 * @return mixed[]
	 */
	protected function process( EmailMessage $message = null ): array {
		// Return early if message is invalid.
		if ( ! $message instanceof EmailMessage ) {
			throw new Exception( 'Invalid message' );
		}

		// Start building the body (different providers have different structure requirements).
		$body = [
			'to'          => $message->getFromEmail(),
			'sender'      => [
				'name'  => $message->getFromName(),
				'email' => $message->getFromEmail(),
			],
			'subject'     => $message->getSubject(),
			'textContent' => $message->isHtml() ? null : $message->getContent(),
			'htmlContent' => $message->isHtml() ? $message->getContent() : null,
		];

		// Start building the attachments.
		if ( ! empty( $message->getAttachments() ) ) {
			$size = 0;

			// Get the total size of all the attachments.
			foreach ( $message->getAttachments() as $attachment ) {
				// Skip if attachment not valid.
				if ( ! $attachment instanceof Attachment ) {
					continue;
				}

				// Add up the sizes.
				$size += filesize( $attachment->getPath() );
			}

			// Throw error if the size exceeds the maximum allowed size.
			if ( $size > self::MAX_ATTACHMENT_BYTES ) {
				throw new Exception( 'Attachments size exceeds the maximum allowed size of ' );
			}

			// Loop over the attachments.
			foreach ( $message->getAttachments() as $attachment ) {
				// Skip if attachment not valid.
				if ( ! $attachment instanceof Attachment ) {
					continue;
				}

				// Attachment data - do whatever you want with it.
				$attachment_data = [
					'name' => $attachment->getName(),
					'type' => $attachment->getType(),
					'path' => $attachment->getPath(),
				];
			}
		}

		// Create a new response.
		$response = new Response( $this->getType() );

		// Build the header.
		$headers = [
			'something_like'    => $this->api_key_or_something,
			'or_something_like' => $this->something_else,
		];

		// Send the request.
		$result = $this->request(
			method: 'POST',
			url: 'https://souptik.dev',
			headers: $headers,
			body: $body,
		);

		// Get the status code.
		$status_code = $result['statusCode'];

		// Check for errors - checking for error might depend on provider.
		if ( $status_code >= 200 && $status_code < 300 ) {
			$response->setDeliveredTo( count( $message->getTo() ) );

			// Build the response.
			foreach ( $message->getTo() as $to ) {
				$response->addResult( $to );
			}
		} elseif ( $status_code >= 400 && $status_code < 500 ) {
			// Build the response.
			foreach ( $message->getTo() as $to ) {
				if ( is_string( $result['response'] ) ) {
					$response->addResult( $to, $result['response'] );
				} elseif ( isset( $result['response']['message'] ) ) {
					$response->addResult( $to, strval( $result['response']['message'] ) );
				} else {
					$response->addResult( $to, 'Unknown error' );
				}
			}
		}

		// Return the response.
		return $response->toArray();
	}
}
