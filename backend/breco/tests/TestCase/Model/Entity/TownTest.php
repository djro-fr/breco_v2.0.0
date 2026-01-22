<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Town;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Town Test Case
 */
class TownTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Entity\Town
     */
    protected $Town;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->Town = new Town();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Town);

        parent::tearDown();
    }
}
