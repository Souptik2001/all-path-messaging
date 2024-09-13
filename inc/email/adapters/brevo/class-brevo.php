<?php
/**
 * Brevo adapter: Main Sender.
 *
 * @package wp-messaging
 */

namespace Souptik\WPMessaging\Email\Adapters\Brevo;

use Exception;
use Utopia\Messaging\Adapter\Email as EmailAdapter;
use Utopia\Messaging\Messages\Email as EmailMessage;
use Utopia\Messaging\Messages\Email\Attachment;
use Utopia\Messaging\Response;

/**
 * Brevo class.
 */
class Brevo extends EmailAdapter {

	/**
	 * Adapter Name.
	 *
	 * @var string
	 */
	protected const NAME = 'Brevo'; // phpcs:ignore

	/**
	 * Constructor.
	 *
	 * @param  string $api_key Your Brevo API key to authenticate with the API.
	 */
	public function __construct( private string $api_key = '' ) {
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

	/** // phpcs:ignore -- We are inheriting the doc here - as it is.
	 * {@inheritdoc}
	 */ // phpcs:ignore -- We are inheriting the doc here - as it is.
	protected function process( EmailMessage $message = null ): array { // phpcs:ignore -- We are inheriting the doc here - as it is.
		// Return early if message is invalid.
		if ( ! $message instanceof EmailMessage ) {
			throw new Exception( 'Invalid message' );
		}

		// Get the to email addresses.
		$to = array_map(
			function ( string $value = '' ) {
				return [
					'email' => $value,
					'name'  => $value,
				];
			},
			$message->getTo()
		);

		// Start building the body.
		$body = [
			'to'          => $to,
			'sender'      => [
				'name'  => $message->getFromName(),
				'email' => $message->getFromEmail(),
			],
			'subject'     => $message->getSubject(),
			'textContent' => $message->isHtml() ? null : $message->getContent(),
			'htmlContent' => $message->isHtml() ? $message->getContent() : null,
		];

		// Build the reply to.
		if ( ! empty( $message->getReplyToEmail() ) ) {
			$body['replyTo'] = [
				'email' => $message->getReplyToEmail(),
				'name'  => $message->getReplyToName(),
			];
		}

		// CC and BCC.
		$cc_body  = [];
		$bcc_body = [];

		// Build CC.
		if ( ! empty( $message->getCC() ) ) {
			foreach ( $message->getCC() as $cc ) {
				$cc_body[] = [
					'email' => $cc['email'] ?? '',
					'name'  => $cc['name'] ?? '',
				];
			}
		}

		// Build BCC.
		if ( ! empty( $message->getBCC() ) ) {
			foreach ( $message->getBCC() as $bcc ) {
				$bcc_body[] = [
					'email' => $bcc['email'] ?? '',
					'name'  => $bcc['name'] ?? '',
				];
			}
		}

		// Add CC to the body.
		if ( ! empty( $cc ) ) {
			$body['cc'] = $cc_body;
		}

		// Add BCC to the body.
		if ( ! empty( $bcc ) ) {
			$body['bcc'] = $bcc_body;
		}

		// Attachments.
		$attachments = [];

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

				// Create a new CURL file.
				$attachment_file = curl_file_create( // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_file_create
					$attachment->getPath(),
					$attachment->getType(),
					$attachment->getName(),
				);

				// Get the attachment contents.
				$attachment_content = file_get_contents( $attachment->getPath() ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

				// Insert the attachments.
				if ( ! empty( $attachment_content ) && ! empty( $attachment_file->getPostFilename() ) ) {
					$attachments[] = [
						'name'    => $attachment_file->getPostFileName(),
						'content' => base64_encode( $attachment_content ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					];
				}
			}
		}

		// Add attachments to the body.
		if ( ! empty( $attachments ) ) {
			$body['attachment'] = $attachments;
		}

		// Create a new response.
		$response = new Response( $this->getType() );

		// Build the header.
		$headers = [
			"api-key:$this->api_key",
			'Content-Type: application/json',
		];

		// Send the request.
		$result = $this->request(
			method: 'POST',
			url: 'https://api.brevo.com/v3/smtp/email',
			headers: $headers,
			body: $body,
		);

		// Get the status code.
		$status_code = $result['statusCode'];

		// Check for errors.
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
