<?php

namespace RedberryProducts\MdNotion\SDK\Resource;

use RedberryProducts\MdNotion\SDK\Requests\Actions\AddCommentToDiscussion;
use RedberryProducts\MdNotion\SDK\Requests\Actions\AddCommentToPage;
use RedberryProducts\MdNotion\SDK\Requests\Actions\BlockChildren;
use RedberryProducts\MdNotion\SDK\Requests\Actions\Database;
use RedberryProducts\MdNotion\SDK\Requests\Actions\DatabaseItems;
use RedberryProducts\MdNotion\SDK\Requests\Actions\ListComments;
use RedberryProducts\MdNotion\SDK\Requests\Actions\Page;
use RedberryProducts\MdNotion\SDK\Requests\Actions\QueryDataSource;
use RedberryProducts\MdNotion\SDK\Resource;
use Saloon\Http\Response;

class Actions extends Resource
{
    public function getPage(string $id): Response
    {
        return $this->connector->send(new Page($id));
    }

    public function getBlockChildren(string $id, ?string $pageSize): Response
    {
        return $this->connector->send(new BlockChildren($id, $pageSize));
    }

    public function getDatabase(string $databaseId): Response
    {
        return $this->connector->send(new Database($databaseId));
    }

    public function addCommentToPage(mixed $parent, mixed $richText): Response
    {
        return $this->connector->send(new AddCommentToPage($parent, $richText));
    }

    public function listComments(?string $blockId): Response
    {
        return $this->connector->send(new ListComments($blockId));
    }
    
    public function queryDataSource(string $dataSourceId, ?array $filter = null): Response
    {
        return $this->connector->send(new QueryDataSource($dataSourceId, $filter));
    }
}
