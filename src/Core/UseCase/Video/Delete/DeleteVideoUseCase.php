<?php

namespace Core\UseCase\Video\Delete;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Delete\DTO\DeleteInputVideoDTO;
use Core\UseCase\Video\Delete\DTO\DeleteOutputVideoDTO;

class DeleteVideoUseCase
{
    public function __construct(
        protected VideoRepositoryInterface $repository
    )
    {
    }

    public function execute(DeleteInputVideoDTO $input): DeleteOutputVideoDTO
    {
        $success = $this->repository->delete($input->id);

        return new DeleteOutputVideoDTO(
            success: $success
        );
    }

}
