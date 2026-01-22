<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Location;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Location Test Case
 */
class LocationTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Entity\Location
     */
    protected $Location;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->Location = new Location();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Location);

        parent::tearDown();
    }

    /**
     * Test getAvailableTypes method
     *
     * @return void
     * @link \App\Model\Entity\Location::getAvailableTypes()
     */
    public function testGetAvailableTypes(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
