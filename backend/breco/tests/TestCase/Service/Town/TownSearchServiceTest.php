<?php

// backend\breco\tests\TestCase\Service\Town\TownSearchServiceTest.php

declare(strict_types=1);

namespace App\Test\TestCase\Service\Town;

use App\Dto\Town\TownSearchRequest;
use App\Repository\TownRepository;
use App\Service\Town\TownSearchService;
use Cake\TestSuite\TestCase;

/**
 * TownSearchService Test Case
 *
 * Tested layer: TownSearchService (business logic only)
 * TownRepository is mocked - no database calls.
 * TownSearchRequest DTO is tested directly for validation (TC-85, TC-90).
 */
class TownSearchServiceTest extends TestCase
{
    private TownSearchService $townSearchService;
    private TownRepository&\PHPUnit\Framework\MockObject\MockObject $townRepository;

    private const TOWN_RANNEE = 'Rannée';
    private const TOWN_RANNEE_ID = 228;
    private const TOWN_RANNEE_POSTAL = '35130';
    private const TOWN_RANNEE_INSEE = '35235';

    protected function setUp(): void
    {
        parent::setUp();

        // Mock TownRepository - no real DB calls
        $this->townRepository = $this->createMock(TownRepository::class);
        $this->townSearchService = new TownSearchService($this->townRepository);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-85 Single Letter Query → INVALID
    //
    // Expected result: InvalidArgumentException thrown, query never executed
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc85SingleLetterQueryThrowsException(): void
    {
        // No ARRANGE nor mock, DTO tests itself
        // ASSERT
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La recherche doit contenir au moins 2 caractères');

        // ACT
        TownSearchRequest::create('r');
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-86 Two Letters With Single Result → VALID
    //
    // Expected result: one town returned (Rannée)
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc86TwoLettersReturnsSingleResult(): void
    {
        // ARRANGE
        $this->townRepository
            ->method('searchByName')
            ->with('ra', 10)
            ->willReturn([
                ['id' => self::TOWN_RANNEE_ID, 'name' => self::TOWN_RANNEE, 'postal_code' => self::TOWN_RANNEE_POSTAL, 'insee_code' => self::TOWN_RANNEE_INSEE]
            ]);

        $request = new TownSearchRequest('ra');

        // ACT
        $result = $this->townSearchService->search($request);

        // ASSERT
        $this->assertCount(1, $result['towns']);
        $this->assertEquals(self::TOWN_RANNEE, $result['towns'][0]['name']);
        $this->assertEquals(1, $result['count']);
        $this->assertEquals('ra', $result['query']);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-87 Two Letters With Multiple Results → VALID
    //
    // Expected result: multiple towns returned
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc87TwoLettersReturnsMultipleResults(): void
    {
        // ARRANGE
        $this->townRepository
            ->method('searchByName')
            ->with('re', 10)
            ->willReturn([
                ['id' => 229, 'name' => 'Rédené',  'postal_code' => '29300', 'insee_code' => '29234'],
                ['id' => 230, 'name' => 'Redon',   'postal_code' => '35600', 'insee_code' => '35236'],
                ['id' => 231, 'name' => 'Rennes',  'postal_code' => '35000', 'insee_code' => '35238'],
                ['id' => 232, 'name' => 'Retiers', 'postal_code' => '35240', 'insee_code' => '35239'],
            ]);

        $request = new TownSearchRequest('re');

        // ACT
        $result = $this->townSearchService->search($request);

        // ASSERT
        $this->assertCount(4, $result['towns']);
        $this->assertEquals(4, $result['count']);
        $this->assertEquals('re', $result['query']);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-88 Two Letters With No Results → VALID
    //
    // Expected result: empty list returned
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc88TwoLettersReturnsEmptyList(): void
    {
        // ARRANGE
        $this->townRepository
            ->method('searchByName')
            ->with('rz', 10)
            ->willReturn([]);

        $request = new TownSearchRequest('rz');

        // ACT
        $result = $this->townSearchService->search($request);

        // ASSERT
        $this->assertCount(0, $result['towns']);
        $this->assertEquals(0, $result['count']);
        $this->assertEquals('rz', $result['query']);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-89 Query With Accents → VALID
    //
    // Expected result: town with accents returned correctly
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc89QueryWithAccentsReturnsCorrectResult(): void
    {
        // ARRANGE
        $this->townRepository
            ->method('searchByName')
            ->with('rannée', 10)
            ->willReturn([
                ['id' => self::TOWN_RANNEE_ID, 'name' => self::TOWN_RANNEE, 'postal_code' => self::TOWN_RANNEE_POSTAL, 'insee_code' => self::TOWN_RANNEE_INSEE]
            ]);

        $request = new TownSearchRequest('rannée');

        // ACT
        $result = $this->townSearchService->search($request);

        // ASSERT
        $this->assertCount(1, $result['towns']);
        $this->assertEquals(self::TOWN_RANNEE, $result['towns'][0]['name']);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-90 Special Characters In Query → INVALID
    //
    // Expected result: InvalidArgumentException thrown, query never executed
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc90SpecialCharactersInQueryThrowsException(): void
    {
        // No ARRANGE nor mock, DTO tests itself
        // ASSERT
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Caractères non autorisés dans la recherche');

        // ACT
        TownSearchRequest::create('@#<>?&+!');
    }
}
