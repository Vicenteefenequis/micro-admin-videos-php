<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

abstract class BaseVideoUseCaseUnitTest extends TestCase
{
    protected $useCase;

    abstract protected function getUseCase(): string;

    abstract protected function nameActionRepository();

    abstract protected function createMockInputDto(
        array  $categoriesId = [],
        array  $genreIds = [],
        array  $castMembersId = [],
        ?array $videoFile = null,
        ?array $trailerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null
    );


    protected function createUseCase(
        int $timesCallMethodActionRepository = 1,
        int $timesCallMethodUpdateMediaRepository = 1,

        int $timesCallMethodCommitTransaction = 1,
        int $timesCallMethodRollbackTransaction = 0,

        int $timesCallMethodStoreFileStorage = 0,

        int $timesCallMethodDispatchEventManager = 0,
    )
    {
        $this->useCase = new ($this->getUseCase())(
            repository: $this->createMockRepository(
                timesCallAction: $timesCallMethodActionRepository, timesCallUpdateMedia: $timesCallMethodUpdateMediaRepository
            ),
            transaction: $this->createMockTransaction(
                timesCallCommit: $timesCallMethodCommitTransaction, timesCallRollback: $timesCallMethodRollbackTransaction
            ),
            storage: $this->createMockFileStorage(
                timesCall: $timesCallMethodStoreFileStorage
            ),
            eventManager: $this->createMockEventManager(
                timesCall: $timesCallMethodDispatchEventManager
            ),
            repositoryCategory: $this->createMockRepositoryCategory(),
            repositoryGenre: $this->createMockRepositoryGenre(),
            repositoryCastMember: $this->createMockRepositoryCastMembers()
        );
    }


    /**
     * @dataProvider dataProviderIds
     */
    public function test_exception_categories_ids(
        string $label,
        array  $ids
    )
    {
        $this->createUseCase(0, 0, 0);
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf("%s %s not found", $label, implode(', ', $ids)));

        $this->useCase->execute(
            input: $this->createMockInputDto(categoriesId: $ids)
        );
    }

    public function dataProviderIds(): array
    {
        return [
            ['Category', ['1']],
            ['Categories', ['1', '2']],
            ['Categories', ['1', '2', '3', '4']]
        ];
    }

    /**
     * @dataProvider dataProviderFiles
     */
    public function test_upload_files(
        array $video,
        array $trailer,
        array $thumb,
        array $thumbHalf,
        array $banner,
        int   $timesStorage,
        int   $timesDispatch
    )
    {
        $this->createUseCase(timesCallMethodStoreFileStorage: $timesStorage, timesCallMethodDispatchEventManager: $timesDispatch);
        $response = $this->useCase->execute(
            input: $this->createMockInputDto(
                videoFile: $video['value'],
                trailerFile: $trailer['value'],
                thumbFile: $thumb['value'],
                thumbHalf: $thumbHalf['value'],
                bannerFile: $banner['value'],
            )
        );
        $this->assertEquals($response->videoFile, $video['expected']);
        $this->assertEquals($response->trailerFile, $trailer['expected']);
        $this->assertEquals($response->thumbFile, $thumb['expected']);
        $this->assertEquals($response->thumbHalf, $thumbHalf['expected']);
        $this->assertEquals($response->bannerFile, $banner['expected']);

    }

    public function dataProviderFiles(): array
    {
        return [
            [
                'video' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.png'],
                'trailer' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.png'],
                'thumb' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.png'],
                'thumbHalf' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'banner' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'timesStorage' => 5,
                'timesDispatch' => 1
            ],
            [
                'video' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.png'],
                'trailer' => ['value' => null, 'expected' => null],
                'thumb' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.png'],
                'thumbHalf' => ['value' => null, 'expected' => null],
                'banner' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'timesStorage' => 3,
                'timesDispatch' => 1
            ],
            [
                'video' => ['value' => null, 'expected' => null],
                'trailer' => ['value' => null, 'expected' => null],
                'thumb' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.png'],
                'thumbHalf' => ['value' => null, 'expected' => null],
                'banner' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'timesStorage' => 2,
                'timesDispatch' => 0
            ],
            [
                'video' => ['value' => null, 'expected' => null],
                'trailer' => ['value' => null, 'expected' => null],
                'thumb' => ['value' => null, 'expected' => null],
                'thumbHalf' => ['value' => null, 'expected' => null],
                'banner' => ['value' => null, 'expected' => null],
                'timesStorage' => 0,
                'timesDispatch' => 0
            ]
        ];

    }


    protected function createMockRepository(
        int $timesCallAction,
        int $timesCallUpdateMedia
    )
    {
        $mockRepo = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepo->shouldReceive($this->nameActionRepository())
            ->times($timesCallAction)
            ->andReturn($this->createMockEntity());
        $mockRepo->shouldReceive('updateMedia')
            ->times($timesCallUpdateMedia)
            ->andReturn($this->createMockEntity());
        return $mockRepo;
    }

    protected  function createMockRepositoryCategory(array $categoriesResponse = [])
    {
        $mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('getIdsListIds')->andReturn($categoriesResponse);
        return $mockRepo;
    }

    protected  function createMockRepositoryGenre(array $genresResponse = [])
    {
        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('getIdsListIds')->andReturn($genresResponse);
        return $mockRepo;
    }

    protected  function createMockRepositoryCastMembers(array $castMembers = [])
    {
        $mockRepo = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('getIdsListIds')->andReturn($castMembers);
        return $mockRepo;
    }


    protected  function createMockTransaction(
        int $timesCallCommit,
        int $timesCallRollback
    )
    {
        $mockTransaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $mockTransaction->shouldReceive('commit')->times($timesCallCommit);
        $mockTransaction->shouldReceive('rollback')->times($timesCallRollback);
        return $mockTransaction;
    }

    protected  function createMockFileStorage(
        int $timesCall
    )
    {
        $mockFileStorage = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $mockFileStorage->shouldReceive('store')->times($timesCall)->andReturn('path/file.png');
        return $mockFileStorage;
    }

    protected  function createMockEventManager(
        int $timesCall
    )
    {
        $mockEventManager = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $mockEventManager->shouldReceive('dispatch')->times($timesCall);
        return $mockEventManager;
    }


    protected  function createMockEntity()
    {
        return Mockery::mock(Video::class, [
            'Title', 'Description', 2020, 12, true, Rating::RATE12
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
