<?php

namespace Core\UseCase\Video\Paginate;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Paginate\DTO\PaginateInputVideoDTO;
use Core\UseCase\Video\Paginate\DTO\PaginateOutputVideoDTO;

class ListVideosUseCase
{

    public function __construct(
        protected VideoRepositoryInterface $repository,
    )
    {
    }

    public function execute(PaginateInputVideoDTO $input): PaginateOutputVideoDTO
    {
        $result = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );
        return new PaginateOutputVideoDTO(
            items: $result->items(),
            total: $result->total(),
            current_page: $result->currentPage(),
            last_page: $result->lastPage(),
            first_page: $result->firstPage(),
            per_page: $result->perPage(),
            to: $result->to(),
            from: $result->from()
        );
    }

}
