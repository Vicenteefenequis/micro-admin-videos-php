<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\DTO\Genre\Create\{
    GenreCreateOutputDto,
    GenreCreateInputDto
};
use Throwable;

class CreateGenreUseCase
{
    protected GenreRepositoryInterface $repository;
    protected TransactionInterface $transaction;
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        GenreRepositoryInterface    $repository,
        TransactionInterface        $transaction,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->repository = $repository;
        $this->transaction = $transaction;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(GenreCreateInputDto $input): GenreCreateOutputDto
    {
        try {
            $genre = new Genre(
                name: $input->name,
                isActive: $input->isActive,
                categoriesId: $input->categoriesId
            );

            $this->validateCategoriesId($input->categoriesId);

            $genreDb = $this->repository->insert($genre);

            $this->transaction->commit();

            return new GenreCreateOutputDto(
                id: (string)$genreDb->id,
                name: $genreDb->name,
                isActive: $genreDb->isActive,
                created_at: $genreDb->createdAt()
            );

        } catch (Throwable $throwable) {
            $this->transaction->rollback();
            throw $throwable;
        }
    }

    private function validateCategoriesId(array $categoriesId = [])
    {
        $categoriesDb = $this->categoryRepository->getIdsListIds($categoriesId);

        $arrayDiff = array_diff($categoriesId, $categoriesDb);

        if (count($arrayDiff)) {
            $msg = sprintf('%s %s not found', count($arrayDiff) > 1 ? 'Categories' : 'Category', implode(', ', $arrayDiff));

            throw new NotFoundException($msg);
        }

    }
}
