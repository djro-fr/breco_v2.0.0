<?php

// backend\breco\tests\TestCase\Service\Auth\AuthServiceTest.php

declare(strict_types=1);

namespace App\Test\TestCase\Service\Auth;

use App\Dto\Auth\RegisterRequest;
use App\Dto\Auth\LoginRequest;

use App\Repository\UserRepository;
use App\Service\Auth\AuthService;
use App\Service\EmailService;
use Cake\TestSuite\TestCase;

use App\Exception\AuthenticationException;
use App\Exception\EmailAlreadyInUseException;
use App\Exception\EmailNotVerifiedException;
use App\Exception\VerificationException;
use App\Exception\TooManyAttemptsException;

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

    private const TEST_EMAIL_OK = 'toto@titi.com';
    private const TEST_EMAIL_KO = 'toto@tata.com';


    // ─ Valid registration data (shared between TC) ─
    private array $validUserData = [
        'email'      => self::TEST_EMAIL_OK,
        'password'   => 'Toto1234', // NOSONAR
        'firstName'  => 'Toto',
        'lastName'   => 'TITI',
        'phone'      => '0607080910', // NOSONAR
        'driver'     => false,
    ];

    private array $unvalidUserData = [
        'email'      => self::TEST_EMAIL_KO,
        'password' => 'Chabada123', // NOSONAR
        'wrongPassword' => 'MauvaisMot2Passe', // NOSONAR
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
            ->with(self::TEST_EMAIL_OK)
            ->willReturn(false);

        $this->userRepository
            ->method('create')
            ->willReturn([
                'id'         => 1,
                'email'      => self::TEST_EMAIL_OK,
                'first_name' => 'Toto',
                'last_name'  => 'TITI',
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
            ->with(self::TEST_EMAIL_OK)
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

        // ACT (after ASSERT for exceptions in PHPUnit)
        $this->authService->register($registerRequest);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-53  Login with email not in database → INVALID
    //
    // Expected result: AuthenticationException thrown
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc53LoginWithNonExistingEmailThrowsException(): void
    {
        // ARRANGE
        $this->userRepository
            ->method('findByEmail')
            ->with(self::TEST_EMAIL_KO)
            ->willReturn(null);

        $loginRequest = new LoginRequest(
            $this->unvalidUserData['email'],
            $this->unvalidUserData['password']
        );

        // ASSERT
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('E-mail ou mot de passe incorrect');  // NOSONAR
        $this->expectExceptionCode(401);

        // ACT (after ASSERT for exceptions in PHPUnit)
        $this->authService->login($loginRequest);

    }


    // ────────────────────────────────────────────────────────────────────────
    // TC-54  Login with email in database → VALID
    //
    // Expected result: Token and user generated
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc54LoginWithExistingEmailReturnsToken(): void
    {
        // ARRANGE
        $this->userRepository
            ->method('findByEmail')
            ->with(self::TEST_EMAIL_OK)
            // Mock returns raw DB row - must include all fields accessed by AuthService::login()
            // before formatUser() is called: 'password' (password_verify), 'email_verified' (guard check),
            // then all fields used by generateToken() and formatUser().
            ->willReturn([
                'password'   => password_hash($this->validUserData['password'], PASSWORD_DEFAULT), // NOSONAR
                'email_verified' => true,
                'id'             => 1,
                'email'      => self::TEST_EMAIL_OK,
                'phone'      => '0607080910',
                'first_name'  => 'Toto',
                'last_name'   => 'TITI',
                'gender'         => null,
                'age'            => null,
                'created'        => null,
            ]);

        $loginRequest = new LoginRequest(
            $this->validUserData['email'],
            $this->validUserData['password']
        );

        // ACT
        $result = $this->authService->login($loginRequest);

        // ASSERT
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertNotEmpty($result['token']);
        $this->assertEquals(self::TEST_EMAIL_OK, $result['user']['email']);
    }


    // ────────────────────────────────────────────────────────────────────────
    // TC-55 Login with incorrect password → INVALID
    //
    // Expected result: AuthenticationException thrown
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc55LoginWithIncorrectPasswordThrowsException(): void
    {
        // ARRANGE
        $this->userRepository
            ->method('findByEmail')
            ->with(self::TEST_EMAIL_OK)
            ->willReturn([
                'password'   => password_hash($this->validUserData['password'], PASSWORD_DEFAULT), // NOSONAR
                'email_verified' => true,
                'id'             => 1,
                'email'      => self::TEST_EMAIL_OK,
                'phone'      => '0607080910',
                'first_name'  => 'Toto',
                'last_name'   => 'TITI',
                'gender'         => null,
                'age'            => null,
                'created'        => null,
            ]);

        $loginRequest = new LoginRequest(
            $this->validUserData['email'],
            $this->unvalidUserData['wrongPassword']
        );

        // ASSERT
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('E-mail ou mot de passe incorrect');
        $this->expectExceptionCode(401);

        // ACT (after ASSERT for exceptions in PHPUnit)
        $this->authService->login($loginRequest);

    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-57  Verify email with valid token → VALID
    //
    // Expected result: ['success' => true, 'message' => '...']
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc57VerifyEmailWithValidTokenReturnsSuccess(): void
    {
        // ARRANGE
        $this->userRepository
            ->method('findByVerificationToken')
            ->willReturn([
                'id' => 1
            ]);
        $this->userRepository
            ->method('verifyEmail')
            ->with(1)
            ->willReturn(true);

        // ACT
        $result = $this->authService->verifyEmail('valid-token-abc123');

        // ASSERT
        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['message']);

    }


    // ────────────────────────────────────────────────────────────────────────
    // TC-58  Verify email with expired token → INVALID
    //
    // Expected result: VerificationException thrown
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc58VerifyEmailWithExpiredTokenThrowsException(): void
    {
        // ARRANGE
        $this->userRepository
            ->method('findByVerificationToken')
            ->willReturn(null);

        // ASSERT
        $this->expectException(VerificationException::class);
        $this->expectExceptionMessage('Lien invalide ou expiré');  // NOSONAR
        $this->expectExceptionCode(400);

        // ACT
        $this->authService->verifyEmail('expired-token-abc123');
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-60 Login with unverified account → INVALID
    //
    // Expected result: EmailNotVerifiedException thrown
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc60LoginWithUnverifiedAccountThrowsException(): void
    {
        // ARRANGE
        $this->userRepository
            ->method('findByEmail')
            ->with(self::TEST_EMAIL_OK)
            ->willReturn([
                'password'   => password_hash($this->validUserData['password'], PASSWORD_DEFAULT), // NOSONAR
                'email_verified' => false,
                'id'             => 1,
                'email'      => self::TEST_EMAIL_OK,
                'phone'      => '0607080910',
                'first_name'  => 'Toto',
                'last_name'   => 'TITI',
                'gender'         => null,
                'age'            => null,
                'created'        => null,
            ]);

        $loginRequest = new LoginRequest(
            $this->validUserData['email'],
            $this->validUserData['password']
        );

        // ASSERT
        $this->expectException(EmailNotVerifiedException::class);
        $this->expectExceptionMessage('Veuillez vérifier votre adresse e-mail avant de vous connecter');
        $this->expectExceptionCode(403);

        // ACT (after ASSERT for exceptions in PHPUnit)
        $this->authService->login($loginRequest);

    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-61 Falsified JWT Token → INVALID
    //
    // Expected result: AuthenticationException thrown
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc61FalsifiedJwtTokenThrowsException(): void
    {
        // ARRANGE
        $falsifiedToken = 'fake-token-abc123';

        // ASSERT
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Token invalide');
        $this->expectExceptionCode(401);

        // ACT (after ASSERT for exceptions in PHPUnit)
        $this->authService->verifyToken($falsifiedToken);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-62 Expired JWT Token → INVALID
    //
    // Expected result: AuthenticationException thrown
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc62ExpiredJwtTokenThrowsException(): void
    {
        // ARRANGE (generateToken)
        $expiredToken = \Firebase\JWT\JWT::encode(
            [
                'sub'   => 1,
                'email' => self::TEST_EMAIL_OK,
                'iat'   => time() - 7200,
                'exp'   => time() - 1,
            ],
            env('JWT_SECRET', 'your-secret-key'), // 'your-secret-key' is a fallback, not the true key
            'HS256'
        );

        // ASSERT
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Token invalide');
        $this->expectExceptionCode(401);

        // ACT (after ASSERT for exceptions in PHPUnit)
        $this->authService->verifyToken($expiredToken);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-63 Repeated Failed Login Attempts → BLOCKED
    //
    // Expected result: TooManyAttemptsException thrown after 5 failed attempts
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc63RepeatedFailedLoginAttemptsBlocksAccess(): void
    {
        // ARRANGE
        $this->userRepository
            ->method('countRecentAttempts')
            ->with($this->validUserData['email'], $this->anything())
            ->willReturn(5);

        $loginRequest = new LoginRequest(
            $this->validUserData['email'],
            $this->validUserData['password']
        );

        // ASSERT
        $this->expectException(TooManyAttemptsException::class);
        $this->expectExceptionMessage('Accès temporairement bloqué');
        $this->expectExceptionCode(429);

        // ACT
        $this->authService->login($loginRequest);
    }

    // ────────────────────────────────────────────────────────────────────────
    // TC-72 SQL Injection In E-mail Field → INVALID
    //
    // Expected result: InvalidArgumentException thrown, query never executed
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc72SqlInjectionInEMailFieldThrowsException(): void
    {
        // No ARRANGE nor mock, DTO tests itself

        // ASSERT
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        // ACT
        RegisterRequest::create("' OR 1=1--", 'Toto1234', 'Toto', 'TITI', '0607080910');
    }


    // ────────────────────────────────────────────────────────────────────────
    // TC-73 XSS Injection In First Name Field → INVALID
    //
    // Expected result: InvalidArgumentException thrown, query never executed
    // ────────────────────────────────────────────────────────────────────────
    #[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
    public function testTc73XssInjectionInFirstNameFieldThrowsException(): void
    {
        // No ARRANGE nor mock, DTO tests itself

        // ASSERT
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le prénom contient des caractères invalides');

        // ACT
        RegisterRequest::create("toto@titi.com", 'Toto1234', '<script>alert(1)</script>', 'TITI', '0607080910');
    }
}
