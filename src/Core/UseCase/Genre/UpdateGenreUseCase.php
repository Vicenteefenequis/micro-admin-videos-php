<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\Update\{
    GenreUpdateInputDto,
    GenreUpdateOutputDto,
};
use Core\UseCase\Interfaces\TransactionInterface;

class UpdateGenreUseCase
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

    public function execute(GenreUpdateInputDto $input): GenreUpdateOutputDto
    {
        $genre = $this->repository->findById($input->id);
        try {
            $genre->update(name: $input->name);

            foreach ($input->categoriesIds as $categoryId) {
                $genre->addCategory($categoryId);
            }

            $this->validateCategoriesId($input->categoriesIds);

            $genreDb = $this->repository->update($genre);

            $this->transaction->commit();

            return new GenreUpdateOutputDto(
                id: (string)$genreDb->id,
                name: $genreDb->name,
                is_active: $genreDb->isActive,
                created_at: $genreDb->createdAt()
            );

        } catch (\Throwable $throwable) {
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
