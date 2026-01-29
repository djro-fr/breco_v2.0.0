# Error handling

## Custom error flow

```text
Backend returns { error: "E-mail déjà utilisé" }
                      ↓
         AuthRemoteDataSource.handleAxiosError()
                      ↓
    extractErrorMessage() → "E-mail déjà utilisé"
                      ↓
      new ValidationException("E-mail déjà utilisé")
                      ↓
          RegisterUseCase.execute()
       (let the exception pass)
                      ↓
        authStore.register()
         error.value = "E-mail déjà utilisé"
                      ↓
          RegisterPage.vue
      show globalError : "E-mail déjà utilisé"

```
