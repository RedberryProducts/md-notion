<?php

namespace Redberry\MdNotion\SDK\Resource;

use Redberry\MdNotion\SDK\Requests\Actions\AddCommentToPage;
use Redberry\MdNotion\SDK\Requests\Actions\BlockChildren;
use Redberry\MdNotion\SDK\Requests\Actions\Database;
use Redberry\MdNotion\SDK\Requests\Actions\ListComments;
use Redberry\MdNotion\SDK\Requests\Actions\Page;
use Redberry\MdNotion\SDK\Requests\Actions\QueryDataSource;
use Redberry\MdNotion\SDK\Resource;
use Saloon\Http\Response;

class Actions extends Resource
{
    public function getPage(string $id): Response
    {
        return $this->connector->send(new Page($id));
    }

    public function getBlockChildren(string $id, ?int $pageSize = null): Response
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

    public function queryDataSource(string $dataSourceId, ?array $filter = null, ?int $pageSize = null): Response
    {
        return $this->connector->send(new QueryDataSource($dataSourceId, $filter, $pageSize));
    }
}
