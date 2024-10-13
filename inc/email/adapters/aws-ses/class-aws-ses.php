<?php
/**
 * AWS SES adapter: Main Sender.
 *
 * @package all-path-messaging
 */

namespace Souptik\AllPathMessaging\Email\Adapters\AWS_SES;

use Exception;
use Utopia\Messaging\Adapter\Email as EmailAdapter;
use Utopia\Messaging\Messages\Email as EmailMessage;
use Utopia\Messaging\Messages\Email\Attachment;
use Utopia\Messaging\Response;
use Aws\Ses\SesClient;
use Aws\Ses\Exception\SesException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * AWS_SES class.
 */
class AWS_SES extends EmailAdapter {

	/**
	 * Adapter Name.
	 *
	 * @var string
	 */
	protected const NAME = 'AWS SES'; // phpcs:ignore Travelopia.Whitespace.GroupedConst.AddEmptyLineBeforeConstGroup

	/**
	 * SES Client.
	 *
	 * @var ?SesClient
	 */
	private ?SesClient $client = null;

	/**
	 * Constructor.
	 *
	 * @param string $region Region.
	 * @param string $key Key.
	 * @param string $secret Secret.
	 */
	public function __construct( private string $region = '', private string $key = '', private string $secret = '' ) {
		// Return early if required credentials are missing.
		if ( empty( $this->region ) || empty( $this->key ) || empty( $this->secret ) ) {
			return;
		}

		// Initialize the SES Client.
		$this->client = new SesClient(
			[
				'version'     => 'latest',
				'region'      => $this->region,
				'credentials' => [
					'key'    => $this->key,
					'secret' => $this->secret,
				],
			]
		);
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

		// Return early if SES client is not initialized.
		if ( ! $this->client instanceof SesClient ) {
			throw new Exception( 'SES Client not initialized' );
		}

		// Create a new response.
		$response = new Response( $this->getType() );

		// Get the reply to email.
		$reply_to = ! empty( $message->getReplyToEmail() ) ? $message->getReplyToEmail() : $message->getFromEmail();

		// Initialize the mail.
		$mail = new PHPMailer();

		// Add recipients.
		foreach ( $message->getTo() as $recipient ) {
			$mail->addAddress( $recipient, $recipient );
		}

		// Build the mail.
		$mail->setFrom( $message->getFromEmail(), $message->getFromName() );
		$mail->addReplyTo( $reply_to, $reply_to );
		$mail->Subject = $message->getSubject(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- PHPMailer's naming convention.
		$mail->isHTML( $message->isHtml() );
		$mail->Body    = $message->isHtml() ? $message->getContent() : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- PHPMailer's naming convention.
		$mail->AltBody = $message->isHtml() ? '' : $message->getContent(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- PHPMailer's naming convention.

		// Add CC.
		if ( is_array( $message->getCC() ) ) {
			foreach ( $message->getCC() as $cc ) {
				$mail->addAddress( $cc['email'] ?? '', $cc['name'] ?? '' );
			}
		}

		// Add BCC.
		if ( is_array( $message->getBCC() ) ) {
			foreach ( $message->getBCC() as $bcc ) {
				$mail->addBCC( $bcc['email'] ?? '', $bcc['name'] ?? '' );
			}
		}

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

				// Add the attachment.
				$mail->addAttachment( $attachment->getPath() );
			}
		}

		// Attempt to assemble the above components into a MIME message.
		if ( ! $mail->preSend() ) {
			throw new Exception( 'Could not assemble mail.' );
		} else {
			// Create a new variable that contains the MIME message.
			$mail_payload = $mail->getSentMIMEMessage();
		}

		// Send the email.
		try {
			$this->client->sendRawEmail(
				[
					'RawMessage' => [
						'Data' => $mail_payload,
					],
				]
			);

			// Set the response.
			$response->setDeliveredTo( count( $message->getTo() ) );

			// Build the response.
			foreach ( $message->getTo() as $to ) {
				$response->addResult( $to );
			}
		} catch ( SesException $e ) {
			// Build the response.
			foreach ( $message->getTo() as $to ) {
				if ( ! empty( $e->getAwsErrorMessage() ) ) {
					$response->addResult( $to, $e->getAwsErrorMessage() );
				} else {
					$response->addResult( $to, 'Unknown error' );
				}
			}
		}

		// Return the response.
		return $response->toArray();
	}
}
