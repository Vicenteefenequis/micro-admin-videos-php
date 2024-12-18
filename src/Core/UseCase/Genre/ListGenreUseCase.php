<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\GenreOutputDto;

class ListGenreUseCase
{

    protected GenreRepositoryInterface $repository;

    public function __construct(GenreRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GenreInputDto $input): GenreOutputDto
    {
        $genre = $this->repository->findById($input->id);

        return new GenreOutputDto(
            id: (string)$genre->id,
            name: $genre->name,
            is_active: $genre->isActive,
            created_at: $genre->createdAt(),
        );

    }
}
