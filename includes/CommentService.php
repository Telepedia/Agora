<?php

namespace Telepedia\Extensions\Agora;

use MediaWiki\Config\ServiceOptions;
use MediaWiki\MainConfigNames;
use MediaWiki\Permissions\RestrictionStore;
use MediaWiki\Permissions\UserAuthority;
use MediaWiki\Title\Title;
use Psr\Log\LoggerInterface;
use Wikimedia\Rdbms\IConnectionProvider;

class CommentService {

	public const CONSTRUCTOR_OPTIONS = [
		MainConfigNames::ContentNamespaces
	];

	public function __construct(
		private readonly ServiceOptions $options,
		private readonly LoggerInterface $logger,
		private readonly IConnectionProvider $connectionProvider,
		private readonly RestrictionStore $restrictionStore,
	) {
		$options->assertRequiredOptions( self::CONSTRUCTOR_OPTIONS );
	}

	/**
	 * Check whether we can dispaly comments on this page
	 * @param Title $title title object of the page we are checking
	 * @return bool true if we are allowed, false otherwise
	 */
	public function canDisplayComments( Title $title ): bool {
		$titleNs = $title->getNamespace();
		$contentNs = $this->options->get( MainConfigNames::ContentNamespaces );

		if ( !in_array( $titleNs, $contentNs, true ) ) {
			return false;
		}

		// we use page protections for deciding whether an article can have comments or not
		// and we toggle it to admin only if comments should be disabled for a page; even if the user
		// is an admin, we still return false to prevent comments from even administrators
		// note: we remove the protection option from ?action=protect so it is only toggleable by the editor

		if ( !$this->areCommentsEnabledOnPage( $title )) {
			return false;
		}

		// here we also need to check if the page has comments enabled, if not bail
		return true;
	}

	/**
	 * Helper to check whether article comments are enabled on this article
	 * @param Title $title
	 * @return bool
	 */
	public function areCommentsEnabledOnPage( Title $title ): bool {
		$protectionLevel = $this->restrictionStore->getRestrictions( $title, 'commenting' );

		if ( in_array( 'sysop', $protectionLevel, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the number of comments on this article; includes comments that may be deleted/hidden
	 * @param Title $title title object for the page we want the number of comments for
	 * @return int the number of comments, obviously
	 */
	public function getCommentCount( Title $title ): int {
		$dbr = $this->connectionProvider->getReplicaDatabase();

		// its more efficient here to just use a COUNT(*) than to do ->fetchRowCount()
		$res = $dbr->newSelectQueryBuilder()
			->select( 'COUNT(*)' )
			->from( 'agora_comments' )
			->caller( __METHOD__ )
			->fetchField();

		return $res;
	}

	/**
	 * Check whether the current user is able to post comments
	 * @param UserAuthority $user the authority of the user in question
	 * @return bool true if they can, false otherwise
	 */
	public function userCanComment( UserAuthority $user ): bool {
		if ( !$user->isDefinitelyAllowed( 'comments' ) ) {
			return false;
		}

		$block = $user->getBlock();

		if ( $block && ( $block->isSitewide() || $block->appliesToRight( 'agora-comments' ) ) ) {
			return false;
		}

		return true;
	}
}