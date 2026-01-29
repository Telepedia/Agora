<?php

namespace Telepedia\Extensions\Agora\Rest;

use MediaWiki\Message\Message;
use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\TokenAwareHandlerTrait;
use MediaWiki\Rest\Validator\Validator;
use Telepedia\Extensions\Agora\CommentFactory;
use Telepedia\Extensions\Agora\CommentService;
use Wikimedia\ParamValidator\ParamValidator;

class RestoreCommentHandler extends SimpleHandler {

	use TokenAwareHandlerTrait;

	public function __construct(
		private readonly CommentFactory $commentFactory,
		private readonly CommentService $commentService
	) {

	}

	public function run(): Response {
		$authority = $this->getAuthority();

		// only those with the ability to delete a comment can restore it
		if ( !$this->commentService->userCanDelete( $authority ) ) {
			return $this->getResponseFactory()->createHttpError(
				403,
				[ 'error' => wfMessage( 'agora-error-not-allowed-delete' )->text() ]
			);
		}

		$body = $this->getValidatedBody();

		if ( !$body['commentId'] ) {
			return $this->getResponseFactory()->createHttpError(
				400,
				[ 'error' => wfMessage( 'agora-error-malformed-request' )->text() ]
			);
		}

		$comment = $this->commentFactory->newFromId( $body['commentId'] );

		if ( !$comment ) {
			return $this->getResponseFactory()->createHttpError(
				403,
				[ 'error' => wfMessage( 'agora-error-comment-missing' )->text() ]
			);
		}

		if ( !$comment->isDeleted() ) {
			return $this->getResponseFactory()->createHttpError(
				403,
				[ 'error' => wfMessage( 'agora-error-comment-not-deleted' )->text() ]
			);
		}

		$actorId = $this->getSession()->getUser()->getActorId();

		if ( $actorId === 0 ) {
			// if we don't have an actor, we done fucked up
			// this shouldn't happen, obviously, since we'll likely have an actor already
			return $this->getResponseFactory()->createHttpError(
				400,
				[ 'error' => wfMessage( 'agora-error-actor-id-missing' )->text() ]
			);
		}

		$res = $this->commentService->restoreComment( $comment, $actorId );

		if ( !$res->isGood() ) {
			$error = Message::newFromSpecifier( $res->getMessages( 'error')[0] )->inLanguage( 'en' )->plain();
			return $this->getResponseFactory()->createHttpError(
				500,
				[ 'error' => $error ]
			);
		}

		// successful restoration, return no content
		// caller is responsible for tracking which comment it deleted, and removing it from the stack
		return $this->getResponseFactory()->createNoContent();
	}

	/**
	 * @inheritDoc
	 */
	public function needsWriteAccess(): bool {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function validate( Validator $restValidator ): void {
		parent::validate( $restValidator );
		$this->validateToken( false );
	}

	/**
	 * @inheritDoc
	 */
	public function getBodyParamSettings(): array {
		return [
				'commentId' => [
					self::PARAM_SOURCE => 'body',
					ParamValidator::PARAM_TYPE => 'integer',
					ParamValidator::PARAM_REQUIRED => true
				]
			] + $this->getTokenParamDefinition();
	}
}