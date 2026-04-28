# TESTING.md: Breco Test Cases

## Introduction

This document lists all **test cases** for the Breco project.
Tests cover the Frontend (Vitest, Vue Test Utils, Selenium) and Backend (PHPUnit) layers,
organized according to the testing pyramid: unit → integration → UI → E2E.

## Test pyramid

```text
           E2E (Selenium)
        UI (Vue Test Utils)
   Integration (Vitest + PHPUnit)
Unit (Vitest + PHPUnit)...........
```

## Legend

- ✅ Valid
- ❌ Invalid
- 🔜 To implement

## Run the tests

```bash
# Frontend: Unit tests all files
cd frontend/breco && npm run test:unit
# Frontend: Unit tests 1 file
cd frontend/breco && npx vitest run src/__tests__/unit/xxx/xxx.spec.ts

# Frontend: Integration tests
npm run test:integration
# Frontend: Integration tests 1 file
cd frontend/breco && npx vitest run src/__tests__/integration/xxx/xxx.spec.ts

# Frontend: UI tests
npm run test:ui
# Frontend: UI tests 1 file
cd frontend/breco && npx vitest run src/__tests__/ui/xxx/xxx.spec.ts

# Frontend: E2E tests
npm run test:e2e
# Frontend: E2E tests 1 file
cd frontend/breco && npx vitest run src/__tests__/e2e/xxx/xxx.spec.ts

# Backend: PHPUnit
cd backend/breco
vendor/bin/phpunit --testdox --display-phpunit-notices
```

---

## Story 1: Registration / Login

| ID | Type | Tool | Layer | Test File | User Story | Description | Input Data | Expected Result | Actual Result | Gap | Input Validity |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| [x] TC-01 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Empty password (null) | password = null | Error: "Le mot de passe est requis" | As expected | None | ❌ INVALID |
| [x] TC-02 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Empty password (empty string) | password = "" | Error: "Le mot de passe doit contenir au moins 8 caractères" | As expected | None | ❌ INVALID |
| [x] TC-03 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Password too short (< 8 chars) | password = "toto" | Error: "Le mot de passe doit contenir au moins 8 caractères" | As expected | None | ❌ INVALID |
| [x] TC-04 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Valid password (8 chars, uppercase + lowercase + digit) | password = "Toto1234" | Password accepted | As expected | None | ✅ VALID |
| [x] TC-05 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Password without uppercase | password = "toto1234" | Error: "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre" | As expected | None | ❌ INVALID |
| [x] TC-06a | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Password without digit | password = "Tititoto" | Error: "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre" | As expected | None | ❌ INVALID |
| [x] TC-06b | Unit | Vitest | Frontend | registerValidation.spec.ts | S1 - Registration | Confirmation different from password | password = "Toto1234" / password_confirmation = "Toto5678" | step1Valid returns false | As expected | None | ❌ INVALID |
| [x] TC-06c | Unit | Vitest | Frontend | registerValidation.spec.ts | S1 - Registration | Confirmation identical to password | password = "Toto1234" / password_confirmation = "Toto1234" | step1Valid returns true | As expected | None | ✅ VALID |
| [x] TC-07 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Empty email | email = "" | Error: "L'email est requis" | As expected | None | ❌ INVALID |
| [x] TC-08 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Incorrect email (invalid format) | email = "toto@titi" | Error: "Format d'e-mail invalide" | As expected | None | ❌ INVALID |
| [x] TC-09 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Email with format xxx@yyy.zzz | email = "toto@titi.com" | E-mail accepted | As expected | None | ✅ VALID |
| [x] TC-10 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Empty phone number | phone = "" | Error: "Format téléphone invalide (10 chiffres commençant par 0)" | As expected | None | ❌ INVALID |
| [x] TC-11 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Incorrect phone (too short) | phone = "1234" | Error: "Format téléphone invalide (10 chiffres commençant par 0)" | As expected | None | ❌ INVALID |
| [x] TC-12 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Incorrect phone (invalid format) | phone = "+33612345678" | Error: "Format téléphone invalide (10 chiffres commençant par 0)" | As expected | None | ❌ INVALID |
| [x] TC-13 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Valid phone (10 digits starting with 0) | phone = "0123456789" | Téléphone accepted | As expected | None | ✅ VALID |
| [x] TC-14 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Empty first name | firstName = "" | Error: "Le prénom contient des caractères invalides" | As expected | None | ❌ INVALID |
| [x] TC-15 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | First name too long (> 50 chars) | firstName = "Jean-Baptiste-Alexandre-Sébastien-Emmanuel-Christophe-Nicolas" | Error: "Le prénom contient des caractères invalides" | As expected | None | ❌ INVALID |
| [x] TC-16 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Short first name (1 char) | firstName = "A" | First name accepted | As expected | None | ✅ VALID |
| [x] TC-17 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | First name with digits | firstName = "R2D2" | Error: "Le prénom contient des caractères invalides" | As expected | None | ❌ INVALID |
| [x] TC-18 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | First name with accented chars (é,è,ë,ï) | firstName = "Noëmie" | First name accepted | As expected | None | ✅ VALID |
| [x] TC-19 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | First name with apostrophe | firstName = "D'Angelo" | First name accepted | As expected | None | ✅ VALID |
| [x] TC-20 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | First name with spaces | firstName = "Marie Line" | First name accepted | As expected | None | ✅ VALID |
| [x] TC-21 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | First name without accents | firstName = "Sarah" | First name accepted | As expected | None | ✅ VALID |
| [x] TC-22 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Empty last name | lastName = "" | Error: "Le nom de famille contient des caractères invalides" | As expected | None | ❌ INVALID |
| [x] TC-23 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Last name too long (> 50 chars) | lastName = "Jean-Baptiste-Alexandre-Sébastien-Emmanuel-Christophe-Nicolas" | Error: "Le nom de famille contient des caractères invalides" | As expected | None | ❌ INVALID |
| [x] TC-24 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Short last name (1 char) | lastName = "A" | Last name accepted | As expected | None | ✅ VALID |
| [x] TC-25 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Last name with digits | lastName = "R2D2" | Error: "Le nom de famille contient des caractères invalides" | As expected | None | ❌ INVALID |
| [x] TC-26 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Last name with accented chars (é,è,ë,ï) | lastName = "Noëmie" | Last name accepted | As expected | None | ✅ VALID |
| [x] TC-27 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Last name with apostrophe | lastName = "D'Agobert" | Last name accepted | As expected | None | ✅ VALID |
| [x] TC-28 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Last name with spaces | lastName = "Dupont Durand" | Last name accepted | As expected | None | ✅ VALID |
| [x] TC-29 | Unit | Vitest | Frontend | validationSchema.spec.ts | S1 - Registration | Last name without accents | lastName = "Martineau" | Last name accepted | As expected | None | ✅ VALID |
| [x] TC-30 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Invalid gender | gender = "masculin" | Error: "Le genre doit être : Homme, Femme ou Ne pas dire" | As expected | None | ❌ INVALID |
| [x] TC-31a | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Null gender | gender = null | Gender accepted | As expected | None | ✅ VALID |
| [x] TC-31b | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Unspecified gender | gender = undefined | Gender accepted | As expected | None | ✅ VALID |
| [x] TC-31c | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Empty gender | gender = '' | Error: "Genre invalide" | As expected | None | ❌ INVALID |
| [x] TC-32 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Gender from allowed list ('Homme', 'Femme', 'Ne pas dire') | gender = "Ne pas dire" | Gender accepted | As expected | None | ✅ VALID |
| [x] TC-33a | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Invalid zip code (too long) | ZipCode = "123456" | Error: "Code postal invalide" | As expected | None | ❌ INVALID |
| [x] TC-33b | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Invalid zip code (letters) | ZipCode = "T3100" | Error: "Code postal invalide" | As expected | None | ❌ INVALID |
| [x] TC-33c | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Invalid zip code (spaces) | ZipCode = "31 000" | Error: "Code postal invalide" | As expected | None | ❌ INVALID |
| [x] TC-34 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Null zip code | zipCode = null | Zip Code accepted | As expected | None | ✅ VALID |
| [x] TC-35 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Zip code | ZipCode = "12345" | Zip Code accepted | As expected | None | ✅ VALID |
| [x] TC-36 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Driver boolean | driver = false | Valid choice | As expected | None | ✅ VALID |
| [x] TC-37 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Unspecified town | town undefined | Accepted because optional | As expected | None | ✅ VALID |
| [x] TC-38 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Town too short (<2) | town = "Y" | Error: "La ville doit contenir au moins 2 caractères" | As expected | None | ❌ INVALID |
| [x] TC-39 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Town too long (>100) | town = "Saint-Jean-de-la-Vallée-des-Charmes-Saint-Laurent-sur-Montagne-et-les-Bois-de-Fleurieu-dans-la-vallée-de-Dana" | Error: "La ville ne peut pas dépasser 100 caractères" | As expected | None | ❌ INVALID |
| [x] TC-40 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Town | town = "Quimper" | Town accepted | As expected | None | ✅ VALID |
| [x] TC-41 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Car model not specified | carModel = "" | Accepted because optional | As expected | None | ✅ VALID |
| [x] TC-42 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Car model 1 char | carModel = "A" | Car model accepted (even short) | As expected | None | ✅ VALID |
| [x] TC-43 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Car model > 50 chars | carModel = "Ferrari Super-Ultra-Sport-Edition-Luxe-Generation-Turbo-Boost-Extreme-Speed" | Error: "Le modèle ne peut pas dépasser 50 caractères" | As expected | None | ❌ INVALID |
| [x] TC-44 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Car model | carModel = "Renault Clio" | Car model accepted | As expected | None | ✅ VALID |
| [x] TC-45 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Car color not specified | carColor undefined | Accepted because optional | As expected | None | ✅ VALID |
| [x] TC-46 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Color 1 char | carColor = "X" | Car color accepted (even short) | As expected | None | ✅ VALID |
| [x] TC-47 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Color > 30 chars | carColor = "Jaune-orangé-soleil-doré-brillant-intense" | Error: "La couleur ne peut pas dépasser 30 caractères" | As expected | None | ❌ INVALID |
| [x] TC-48 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Car color | carColor = "Jaune" | Car color accepted | As expected | None | ✅ VALID |
| [x] TC-49 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Seat count not specified | carSeatNb undefined | Accepted because optional | As expected | None | ✅ VALID |
| [x] TC-50 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Seat count = 0 | CarSeatNb = 0 | Error: "Le nombre de places doit être au moins 1" | As expected | None | ❌ INVALID |
| [x] TC-51 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Seat count > 8 | CarSeatNb = 9 | Error: "Le nombre de places ne peut pas dépasser 8" | As expected | None | ❌ INVALID |
| [x] TC-52 | Unit | Vitest | Frontend | userSchema.spec.ts | S1 - Registration | Seat count | CarSeatNb = 4 | Car seats number accepted | As expected | None | ✅ VALID |
| [x] TC-52b | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | register.integration.spec.ts + AuthServiceTest.php | S1 - Registration | Registration with valid data | email: "toto@titi.com", password: "Toto1234", firstName: "Toto", lastName: "TITI", phone: "0607080910" | {"success": true, "message": "Registration successful!", "requiresVerification": true} | As expected | None | ✅ VALID |
| [x] TC-52c | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | register.integration.spec.ts + AuthServiceTest.php | S1 - Registration | Registration with email already in database | email: "toto@titi.com" (already in database) | {"error": "Email already in use"} | As expected | None | ❌ INVALID |
| [x] TC-53 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | login.integration.spec.ts + AuthServiceTest.php | S1 - Login | Email with format xxx@yyy.zzz but not in database | email = "toto@tata.com" | Error: "E-mail inconnu, inscrivez-vous" | As expected | None | ❌ INVALID |
| [x] TC-54 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | login.integration.spec.ts + AuthServiceTest.php | S1 - Login | Email with format xxx@yyy.zzz, present in database | email = "toto@titi.com" | E-mail accepted | As expected | None | ✅ VALID |
| [x] TC-55 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | login.integration.spec.ts + AuthServiceTest.php | S1 - Login | Incorrect password | password = "Chabada123" | Error: "Le mot de passe ne correspond pas à l'e-mail fourni" | As expected | None | ❌ INVALID |
| [x] TC-56 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | Tests covered by TC-54 | S1 - Login | Login with valid credentials existing in database | email = "toto@titi.com" & password = "Toto1234" | Token JWT | As expected | None | ✅ VALID |
| [x] TC-57 | Unit | PHPUnit | Backend | AuthServiceTest.php | S1 - Email Verification | Valid verification link | token = valid in database | Account activated, verified = true | As expected | None | ✅ VALID |
| [x] TC-58 | Unit | PHPUnit | Backend | AuthServiceTest.php | S1 - Email Verification | Expired verification token | token = expired | Error: "Lien de vérification expiré" | As expected | None | ❌ INVALID |
| [x] TC-59 | Unit | PHPUnit | Backend | Covered by TC-58 | S1 - Email Verification | Already used token | token = already_used | Error: "Lien déjà utilisé" | As expected | None | ❌ INVALID |
| [x] TC-60 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | login.integration.spec.ts + AuthServiceTest.php | S1 - Login | Login with unverified account | email = "toto@titi.com" & password = "Toto1234" & verified = false | Error: "Veuillez vérifier votre adresse e-mail" | As expected | None | ❌ INVALID |
| [x] TC-61 | Unit | PHPUnit | Backend | AuthServiceTest.php | S1 - Token Verification | Forged JWT token | Authorization: Bearer token_falsified | Error 401: "Token invalide" | As expected | None | ❌ INVALID |
| [x] TC-62 | Unit | PHPUnit | Backend | AuthServiceTest.php | S1 - Token Verification | Expired JWT token | Authorization: Bearer token_expired | Error 401: "Token expiré" | As expected | None | ❌ INVALID |
| [x] TC-63 | Unit | PHPUnit | Backend | AuthServiceTest.php | S1 - Login | Repeated failed login attempts | 5+ attempts with wrong password | Access temporarily blocked | As expected | None | ❌ INVALID |
| [x] TC-64 | UI | Vitest + vue/test-utils | Frontend | RegisterPage.spec.ts | S1 - Registration | Vehicle fields provided despite driver=false | driver = false & carModel = "Clio" & carSeatNb = 4 | Ignored vehicle fields | As expected | None | ✅ VALID |
| [x] TC-65 | UI | Vitest + vue/test-utils | Frontend | RegisterPage.spec.ts | S1 - Registration | Empty email submitted via form | Display Zod error messages in UI | Error message displayed in the DOM | As expected | None | ❌ INVALID |
| [x] TC-66 | UI | Vitest + vue/test-utils | Frontend | RegisterPage.spec.ts | S1 - Registration | Next button disabled when step1 invalid | Empty form at step 1 | Next button disabled | As expected | None | ❌ INVALID |
| [x] TC-67 | UI | Vitest + vue/test-utils | Frontend | RegisterPage.spec.ts | S1 - Registration | Progress to step 2 when step1 valid | All step 1 fields valid | Step 2 displayed | As expected | None | ✅ VALID |
| [x] TC-68 | UI | Vitest + vue/test-utils | Frontend | RegisterPage.spec.ts | S1 - Registration | Display password confirmation error | password = "Toto1234" / password_confirmation = "Toto5678" | Message "Les mots de passe ne correspondent pas" visible in DOM | As expected | None | ❌ INVALID |
| [x] TC-69 | E2E | Selenium | Frontend + Backend | Tc69.register.e2e.spec | S1 - Registration | Complete registration flow via form | All valid fields entered in UI | Success message displayed, email sent | As expected | None | ✅ VALID |
| [x] TC-70 | E2E | Selenium | Frontend + Backend | Tc70.login.e2e.spec.ts | S1 - Login | Login flow + redirect | email = "toto@titi.com" & password = "Toto1234" entered in UI | Redirection to dashboard, JWT token stored | As expected | None | ✅ VALID |
| [x] TC-71 | E2E | Selenium | Frontend + Backend | Covered by TC-69 | S1 - Registration | Display step 5 after successful registration | Complete registration with valid email | Step 5 "Vérifiez votre email" displayed | As expected | None | ✅ VALID |
| [x] TC-72 | Unit | PHPUnit | Backend | AuthServiceTest.php | S1 - Registration | SQL injection in email field | email = "' OR 1=1--" | Error: "Format d'e-mail invalide", query not executed | As expected | None | ❌ INVALID |
| [x] TC-73 | Unit | PHPUnit | Backend | AuthServiceTest.php | S1 - Registration | XSS injection in first name field | firstName = "<script>alert(1)</script>" | Error: "Le prénom contient des caractères invalides", script not executed | As expected | None | ❌ INVALID |
| [x] TC-74 | Integration | Vitest | Frontend | logout.integration.spec.ts | S1 - Logout | Logout: token deletion and store reset | Authenticated user (token in store + localStorage) | isAuthenticated = false, token null, user null | As expected | None | ✅ VALID |
| [x] TC-75 | Unit | PHPUnit | Backend | AuthServiceTest.php | S1 - Login | Account lock after N failed attempts | 10+ failed attempts on the same account | Account locked, specific error returned | As expected | None | ❌ INVALID |
| [x] TC-76 | Integration | Vitest | Frontend | register.integration.spec.ts | S1 - Registration | Backend unavailable during registration | API responds 503 | Generic error message displayed, no crash | As expected | None | ❌ INVALID |
| [x] TC-77 | Integration | Vitest | Frontend | login.integration.spec.ts | S1 - Login | Backend unavailable during login | API responds 503 | Generic error message displayed, no crash | As expected | None | ❌ INVALID |
| [ ] TC-78 | Unit | PHPUnit | Backend | (Rate limiting global à implémenter au niveau middleware CakePHP) | S1 - Login | Global API rate limiting | 100+ requests/minute from the same IP | Error 429: Too Many Requests | To implement | To implement | 🔜 TO IMPLEMENT |
| [x] TC-79 | Integration | Vitest | Frontend | routerGuard.spec.ts | S1 - Login | JWT token expiry during active session | Expired JWT token, user on /dashboard | Automatic redirection to /login | As expected | None | ❌ INVALID |
| [x] TC-80 | Integration | Vitest | Frontend | routerGuard.spec.ts | S1 - Login | Page refresh in authenticated session | Valid JWT token in localStorage, F5 on /dashboard | Session kept, user remains logged in | As expected | None | ✅ VALID |
| [x] TC-81 | E2E | Selenium | Frontend | Tc81.protected-route.e2e.spec.ts | S1 - Login | Direct navigation to protected route without token | Access to /dashboard without JWT token | Redirection to /login | As expected | None | ❌ INVALID |

---

## Story 2: Pre-registered Locations

| ID | Type | Tool | Layer | Test File | User Story | Description | Input Data | Expected Result | Actual Result | Gap | Input Validity |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| [x] TC-82 | Unit | Vitest | Frontend | townSearch.spec.ts | S2 - Town Autocomplete | enters no string (empty field) for town | q="" | Autocompletion not triggered, empty list | As expected | None | ❌ INVALID |
| [x] TC-83 | Unit | Vitest | Frontend | townSearch.spec.ts | S2 - Town Autocomplete | enters a town with a single letter | q='r' | Autocompletion not triggered, empty list | As expected | None | ❌ INVALID |
| [x] TC-84 | Unit | Vitest | Frontend | townSearch.spec.ts | S2 - Town Autocomplete | enters a town with special characters (@#<>?&+!) | q='@#<>?&+!' | Autocompletion not triggered, empty list | As expected | None | ❌ INVALID |
| [x] TC-85 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | townSearch.integration.spec.ts + TownSearchServiceTest | S2 - Town Autocomplete | direct API call with one letter (bypass frontend) | search?q=r&limit=10 | Error: "La recherche doit contenir au moins 2 caractères". In the Frontend, autocompletion not triggered, empty list displayed without error message | As expected | None | ❌ INVALID |
| [x] TC-86 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | townSearch.integration.spec.ts + TownSearchServiceTest | S2 - Town Autocomplete | enters two letters matching the start of a town name with a single result | search?q=ra&limit=10 | { "success": true, "data": [ { "id": 228, "name": "Rannée", "postal_code": "35130", "insee_code": "35235" } ], "count": 1, "query": "ra" } | As expected | None | ✅ VALID |
| [x] TC-87 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | townSearch.integration.spec.ts + TownSearchServiceTest | S2 - Town Autocomplete | enters two letters matching the start of a town name with multiple results | search?q=re&limit=10 | { "success": true, "data": [ { "id": 229, "name": "Rédené", "postal_code": "29300", "insee_code": "29234" }, { "id": 230, "name": "Redon", "postal_code": "35600", "insee_code": "35236" }, { "id": 231, "name": "Rennes", "postal_code": "35000", "insee_code": "35238" }, { "id": 232, "name": "Retiers", "postal_code": "35240", "insee_code": "35239" } ], "count": 4, "query": "re" } | As expected | None | ✅ VALID |
| [x] TC-88 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | townSearch.integration.spec.ts + TownSearchServiceTest | S2 - Town Autocomplete | enters two letters matching no town's starting letters | search?q=rz&limit=10 | { "success": true, "data": [], "count": 0, "query": "rz" } | As expected | None | ✅ VALID |
| [x] TC-89 | Integration + Unit | Vitest + PHPUnit | Frontend + Backend | townSearch.integration.spec.ts + TownSearchServiceTest | S2 - Town Autocomplete | enters a town with accents or hyphens | search?q=rann%C3%A9e&limit=10 | { "success": true, "data": [ { "id": 228, "name": "Rannée", "postal_code": "35130", "insee_code": "35235" } ], "count": 1, "query": "rannée" } | As expected | None | ✅ VALID |
| [x] TC-90 | Unit | PHPUnit | Backend | TownSearchServiceTest | S2 - Town Autocomplete | enters a town with special characters (@#<>?&+!) | search?q=%40%23%3C%3E%3F%26%2B%21&limit=10 | Error: "Caractères non autorisés dans la recherche" | As expected | None | ❌ INVALID |

---

**Last updated**: April 28, 2026
