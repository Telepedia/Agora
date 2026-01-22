<?php

namespace Telepedia\Extensions\Agora\Domain;

use JsonSerializable;
use MediaWiki\Title\Title;

class Comment implements JsonSerializable {

	/**
	 * Unique database ID for this comment
	 * @var int
	 */
	private ?int $id = null;

	/**
	 * Page ID that this comment belongs to
	 * @var int
	 */
	private int $pageId;

	/**
	 * Wikitext content of this comment
	 * @var string
	 */
	private string $wikitext;

	/**
	 * Time at which this comment was posted
	 * @var string
	 */
	private string $postedTime;

	/**
	 * ID of the parent comment if this is a reply, otherwise, null
	 * @var ?int
	 */
	private ?int $parentId = null;

	/**
	 * ID of the actor who posted this comment
	 * @var int
	 */
	private ?int $actorId = null;

	/**
	 * Is this comment deleted?
	 * @var ?int
	 */
	private ?int $deletedActor = null;

	/**
	 * Title instance for the article this comment was posted on
	 * @var Title
	 */
	private ?Title $pageTitle = null;

	/**
	 * HTML representation of this comment
	 * @var string
	 */
	private string $html;

	/**
	 * @var Comment[]
	 */
	private array $children = [];

	private string $avatarUrl = '';

	private string $username;

	public function __construct() {}

	/**
	 * Return the ID of the parent, alternatively, null if top-level comment
	 * @return int|null
	 */
	public function getParentId(): ?int {
		return $this->parentId ?: null;
	}

	public function setParentId( ?int $parentId ): self {
		if ( $parentId !== null ) {
			$this->parentId = $parentId;
		}
		return $this;
	}

	/**
	 * Return the actor who posted this comment
	 * @return int
	 */
	public function getActorId(): int {
		return $this->actorId;
	}

	/**
	 * Set the actor responsible for posting this comment
	 * @param int $actorId
	 * @return $this
	 */
	public function setActorId(int $actorId): self {
		$this->actorId = $actorId;
		return $this;
	}

	/**
	 * Return the time at which this comment was posted
	 * @return string
	 */
	public function getTimestamp(): string {
		return $this->postedTime;
	}

	/**
	 * Return the wikitext for this comment
	 * @return string
	 */
	public function getWikitext(): string {
		return $this->wikitext;
	}

	/**
	 * Set the wikitext content of this comment
	 * @param string $wikiText
	 *
	 * @return $this
	 */
	public function setWikiText( string $wikiText ): self {
		$this->wikitext = $wikiText;
		return $this;
	}

	/**
	 * Set the time at which this comment was posted
	 * @param string $postedTime
	 *
	 * @return $this
	 */
	public function setTimestamp( string $postedTime ): self {
		$this->postedTime = $postedTime;
		return $this;
	}

	/**
	 * Get the title object for the page which this comment was posted on
	 * @return Title
	 */
	public function getTitle(): Title {
		if ( !is_null( $this->pageTitle ) ) {
			return $this->pageTitle;
		}

		$this->pageTitle = Title::newFromID( $this->pageId );
		return $this->pageTitle;
	}

	/**
	 * Get the ID of this comment
	 * @return ?int
	 */
	public function getId(): ?int {
		return $this->id;
	}

	/**
	 * Set the ID for this comment
	 * @param int $id
	 * @return $this
	 */
	public function setId( int $id ): self {
		$this->id = $id;
		return $this;
	}

	/**
	 * Get the ID of the article this comment was posted on
	 * @return int
	 */
	public function getPageId(): int {
		return $this->pageId;
	}

	/**
	 * Set the ID of the article this comment was posted on
	 * @param int $pageId
	 * @return $this
	 */
	public function setPageId( int $pageId ): self {
		$this->pageId = $pageId;
		return $this;
	}

	/**
	 * Set the time at which this comment was posted - this will be in the form TS_MW
	 * @param string $postedTime
	 *
	 * @return $this
	 */
	public function setPostedTime( string $postedTime ): self {
		$this->postedTime = $postedTime;
		return $this;
	}

	/**
	 * Is this comment deleted?
	 * @return bool
	 */
	public function isDeleted(): bool {
		return $this->deletedActor !== null;
	}

	/**
	 * Get whether this comment is deleted
	 * @param int $deleted
	 * @return self
	 */
	public function setActorForDeletion( int $actor ): self {
		$this->deletedActor = $actor;
		return $this;
	}

	/**
	 * Get the HTML for this comment
	 * @return string
	 */
	public function getHTML(): string {
		return $this->html;
	}

	/**
	 * Set the HTML for this comment and return the object
	 * @param string $html
	 * @return $this
	 */
	public function setHTML( string $html ): self {
		$this->html = $html;
		return $this;
	}

	/**
	 * Add a child comment (reply) to this comment
	 *
	 * @param Comment $comment
	 * @return $this the comment instance for easier changing
	 *
	 */
	public function addChild( Comment $comment ): self {
		$this->children[] = $comment;
		return $this;
	}

	/**
	 * Return the children of this comment (the replies) each will be a comment instance in itself
	 * @return Comment[]
	 */
	public function getChildren(): array {
		return $this->children;
	}

	/**
	 * Set the avatar for the user responsible for this comment
	 * @param string $avatarUrl
	 * @return $this the comment instance for easier changing
	 */
	public function setAvatar( string $avatarUrl ): self {
		$this->avatarUrl = $avatarUrl;
		return $this;
	}

	public function setUsername( string $username ): self {
		$this->username = $username;
		return $this;
	}

	/**
	 * Get the username of the actor who posted this comment
	 * @return string
	 */
	public function getUsername(): string {
		return $this->username;
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'parent' => $this->parentId,
			'html' => $this->html,
			'timestamp' => $this->postedTime,
			'author' => [
				'id' => $this->actorId,
				'name' => $this->username,
				'avatar' => $this->avatarUrl
			],
			'children' => $this->children,
			'isDeleted' => $this->isDeleted()
		];
	}
}