<?php

namespace RedberryProducts\MdNotion\SDK\Resource;

use RedberryProducts\MdNotion\SDK\Requests\Actions\AddCommentToDiscussion;
use RedberryProducts\MdNotion\SDK\Requests\Actions\AddCommentToPage;
use RedberryProducts\MdNotion\SDK\Requests\Actions\BlockChildren;
use RedberryProducts\MdNotion\SDK\Requests\Actions\Database;
use RedberryProducts\MdNotion\SDK\Requests\Actions\DatabaseItems;
use RedberryProducts\MdNotion\SDK\Requests\Actions\ListComments;
use RedberryProducts\MdNotion\SDK\Requests\Actions\Page;
use RedberryProducts\MdNotion\SDK\Resource;
use Saloon\Http\Response;

class Actions extends Resource
{
	/**
	 * @param string $id
	 */
	public function getPage(string $id): Response
	{
		return $this->connector->send(new Page($id));
	}


	/**
	 * @param string $id
	 * @param string $pageSize
	 */
	public function getBlockChildren(string $id, ?string $pageSize): Response
	{
		return $this->connector->send(new BlockChildren($id, $pageSize));
	}


	/**
	 * @param string $databaseId
	 */
	public function getDatabase(string $databaseId): Response
	{
		return $this->connector->send(new Database($databaseId));
	}


	/**
	 * @param string $databaseId
	 */
	public function getDatabaseItems(string $databaseId): Response
	{
		return $this->connector->send(new DatabaseItems($databaseId));
	}


	/**
	 * @param mixed $parent
	 * @param mixed $richText
	 * @param mixed $displayName
	 */
	public function addCommentToPage(mixed $parent, mixed $richText, mixed $displayName): Response
	{
		return $this->connector->send(new AddCommentToPage($parent, $richText, $displayName));
	}


	/**
	 * @param mixed $discussionId
	 * @param mixed $richText
	 * @param mixed $displayName
	 */
	public function addCommentToDiscussion(mixed $discussionId, mixed $richText, mixed $displayName): Response
	{
		return $this->connector->send(new AddCommentToDiscussion($discussionId, $richText, $displayName));
	}


	/**
	 * @param string $blockId
	 */
	public function listComments(?string $blockId): Response
	{
		return $this->connector->send(new ListComments($blockId));
	}
}
