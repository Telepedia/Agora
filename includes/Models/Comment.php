<?php

namespace Telepedia\Extensions\Agora\Models;

class Comment {

	/**
	 * Unique database ID for this comment
	 * @var int
	 */
	public int $id;

	/**
	 * Page ID that this comment belongs to
	 * @var int
	 */
	public int $pageId;

	/**
	 * Wikitext content of this comment
	 * @var string
	 */
	public string $wikitext;

	/**
	 * Time at which this comment was posted
	 * @var string
	 */
	public string $postedTime;

	/**
	 * ID of the parent comment if this is a reply, otherwise, null
	 * @var ?int
	 */
	public ?int $parentId = null;

	/**
	 * ID of the actor who posted this comment
	 * @var int
	 */
	public int $actorId;

	/**
	 * Is this comment deleted?
	 * @var bool
	 */
	public bool $deleted = false;

	public function __construct() {}

	/**
	 * Return the ID of the parent, alternatively, null if top-level comment
	 * @return int|null
	 */
	public function getParentId(): ?int {
		return $this->parentId ?: null;
	}

	/**
	 * Return the actor who posted this comment
	 * @return int
	 */
	public function getActorId(): int {
		return $this->actorId;
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

}