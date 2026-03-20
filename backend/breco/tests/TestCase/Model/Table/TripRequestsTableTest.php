<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TripRequestsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TripRequestsTable Test Case
 */
class TripRequestsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TripRequestsTable
     */
    protected $tripRequests;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.TripRequests',
        'app.Users',
        'app.Towns',
        'app.Locations',
        'app.Bookings',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('TripRequests') ? [] : ['className' => TripRequestsTable::class];
        $this->tripRequests = $this->getTableLocator()->get('TripRequests', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->TripRequests);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\TripRequestsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\TripRequestsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
