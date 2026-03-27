# Error handling

This document describes how errors and success responses are propagated through Breco's
authentication layer. The chain follows a Clean Architecture pattern:
each layer has a single responsibility and passes the result up to the caller without transforming it.

Errors thrown by the PHP backend travel as HTTP responses through Axios, are intercepted by
`AuthRemoteDataSource`, converted into typed exceptions (`ValidationException`,
`AuthenticationException`...), and bubble up through the UseCase and the store
until they reach the Vue component.

Success responses follow the same path in reverse, with no exceptions involved.

## Localisation: changing error messages to French

Error messages are defined in the backend. To translate them, edit the following files:

### Backend

| File | Layer |
| ------ | ------- |
| `backend/breco/src/Service/Auth/AuthService.php` | Service |
| `backend/breco/src/Controller/Api/AuthController.php` | Controller |
| `backend/breco/src/Exception/AuthenticationException.php` | Exception |
| `backend/breco/src/Exception/EmailAlreadyInUseException.php` | Exception |
| `backend/breco/src/Exception/EmailNotVerifiedException.php` | Exception |
| `backend/breco/src/Exception/VerificationException.php` | Exception |

### Frontend: fallback messages only, kept in sync with the backend

| File | Layer |
| ------ | ------- |
| `frontend/breco/src/data/datasources/remote/AuthRemoteDataSource.ts` | DataSource |

## Custom error flow

### Success flow (requiresVerification)

```text
Backend returns { success: true, message: "Inscription réussie !", requiresVerification: true }
                      ↓
         AuthRemoteDataSource.register()
         returns AuthApiResponse as-is
                      ↓
         AuthRepositoryImpl.register()
         detects requiresVerification: true
         returns { requiresVerification: true, message: "..." }
                      ↓
         RegisterUseCase.execute()
         passes AuthOutput to store
                      ↓
         authStore.register()
         returns { requiresVerification: true, message: "..." }
                      ↓
         RegisterPage.vue
         result.requiresVerification → currentStep = 5
```

### Error flow (email already in use)

```text
Backend returns { error: "Cette adresse e-mail est déjà utilisée" } (HTTP 422)
                      ↓
         AuthRemoteDataSource.handleAxiosError()
                      ↓
         extractErrorMessage() → "Cette adresse e-mail est déjà utilisée"
                      ↓
         new ValidationException("Cette adresse e-mail est déjà utilisée")
                      ↓
         RegisterUseCase.execute()
         instanceof AppException → re-throws as-is
                      ↓
         authStore.register()
         error.value = "Cette adresse e-mail est déjà utilisée"
         re-throws
                      ↓
         RegisterPage.vue
         globalError → "Cette adresse e-mail est déjà utilisée"
```
