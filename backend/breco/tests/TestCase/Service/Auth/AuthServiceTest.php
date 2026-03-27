<?php

// backend\breco\tests\TestCase\Service\Auth\AuthServiceTest.php

declare(strict_types=1);

namespace App\Test\TestCase\Service\Auth;

use App\Dto\Auth\RegisterRequest;
use App\Exception\EmailAlreadyInUseException;
use App\Repository\UserRepository;
use App\Service\Auth\AuthService;
use App\Service\EmailService;
use Cake\TestSuite\TestCase;

/**
 * AuthService Test Case
 *
 * Tested layer: AuthService (business logic only)
 * UserRepository and EmailService are mocked - no database, no real email.
 */
class AuthServiceTest extends TestCase
{
    private AuthService $authService;
    private UserRepository&\PHPUnit\Framework\MockObject\MockObject $userRepository; // UserRepository and MockObject at the same time
    private EmailService&\PHPUnit\Framework\MockObject\MockObject $emailService; // EmailService and MockObject at the same time

    private const TEST_EMAIL = 'dev@test.com';

    // ─ Valid registration data (shared between TC) ─
    private array $validUserData = [
        'email'      => self::TEST_EMAIL,
        'password'   => 'DevPass123!',
        'firstName'  => 'Dev',
        'lastName'   => 'Test',
        'phone'      => '0607080910',
        'driver'     => false,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Mock UserRepository and EmailService - no real DB or SMTP calls
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->emailService = $this->createMock(EmailService::class);

        $this->authService = new AuthService(
            $this->userRepository,
            $this->emailService,
        );
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-52b  Registration with valid data → VALID
    //
    // Expected result:
    //   ['success' => true, 'requiresVerification' => true, 'message' => '...']
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc52bRegisterWithValidDataReturnsRequiresVerification(): void
    {
        // ARRANGE: email does not exist yet, user is created, email is sent
        $this->userRepository
            ->method('emailExists')
            ->with(self::TEST_EMAIL)
            ->willReturn(false);

        $this->userRepository
            ->method('create')
            ->willReturn([
                'id'         => 1,
                'email'      => self::TEST_EMAIL,
                'first_name' => 'Dev',
                'last_name'  => 'Test',
                'phone'      => '0607080910',
            ]);

        $this->emailService
            ->method('sendVerificationEmail')
            ->willReturn(true);

        $registerRequest = new RegisterRequest(
            $this->validUserData['email'],
            $this->validUserData['password'],
            $this->validUserData['firstName'],
            $this->validUserData['lastName'],
            $this->validUserData['phone'],
        );

        // ACT
        $result = $this->authService->register($registerRequest);

        // ASSERT: response shape
        $this->assertTrue($result['success']);
        $this->assertTrue($result['requiresVerification']);
        $this->assertArrayHasKey('message', $result);
        $this->assertNotEmpty($result['message']);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-52c  Registration with email already in database → INVALID
    //
    // Expected result: EmailAlreadyInUseException thrown
    // ────────────────────────────────────────────────────────────────────────
    public function testTc52cRegisterWithExistingEmailThrowsException(): void
    {
        // ARRANGE: email already exists in database
        $this->userRepository
            ->method('emailExists')
            ->with(self::TEST_EMAIL)
            ->willReturn(true);

        // Repository and EmailService should never be called past this point
        $this->userRepository->expects($this->never())->method('create');
        $this->emailService->expects($this->never())->method('sendVerificationEmail');

        $registerRequest = new RegisterRequest(
            $this->validUserData['email'],
            $this->validUserData['password'],
            $this->validUserData['firstName'],
            $this->validUserData['lastName'],
            $this->validUserData['phone'],
        );

        // ASSERT: exception is thrown with correct message and HTTP code
        $this->expectException(EmailAlreadyInUseException::class);
        $this->expectExceptionMessage('Cette adresse e-mail est déjà utilisée');
        $this->expectExceptionCode(422);

        // ACT
        $this->authService->register($registerRequest);
    }
}
