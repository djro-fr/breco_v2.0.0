# TESTING.md : Cas de test Breco

## Introduction

Ce document recense l'ensemble des **cas de test** du projet Breco.
Les tests couvrent les couches Frontend (Vitest, Vue Test Utils, Selenium) et Backend (PHPUnit),
organisés selon la pyramide des tests : unitaires → intégration → UI → E2E.

## Test pyramid

```text
           E2E (Selenium)
        UI (Vue Test Utils)
   Integration (Vitest + PHPUnit)
 Unit (Vitest + PHPUnit)...........
```

## Légende

- ✅ Valide
- ❌ Invalide
- 🔜 A implémenter

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

## Story 1: Inscription / Connexion

| ID | Type | Outil | Couche | Fichier test | User Story | Description | Données d'entrée | Résultat attendu | Technique | Statut |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| [x] TC-01 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Mot de passe vide (null) | password = null | Erreur : "Le mot de passe est requis" | Valeurs limites | ❌ INVALIDE |
| [x] TC-02 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Mot de passe vide (chaîne vide) | password = "" | Erreur : "Le mot de passe doit contenir au moins 8 caractères" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-03 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Mot de passe trop court (< 8 car.) | password = "toto" | Erreur : "Le mot de passe doit contenir au moins 8 caractères" | Valeurs limites | ❌ INVALIDE |
| [x] TC-04 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Mot de passe valide (8 car., majuscule + minuscule + chiffre) | password = "Toto1234" | Mot de passe accepté | Valeurs limites + Équivalence | ✅ VALIDE |
| [x] TC-05 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Mot de passe sans majuscule | password = "toto1234" | Erreur : "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-06a | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Mot de passe sans chiffre | password = "Tititoto" | Erreur : "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-06b | Unitaire | Vitest | Frontend | registerValidation.spec.ts | S1 - Inscription | Confirmation différente du mot de passe | password = "Toto1234" / password_confirmation = "Toto5678" | step1Valid retourne false | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-06c | Unitaire | Vitest | Frontend | registerValidation.spec.ts | S1 - Inscription | Confirmation identique au mot de passe | password = "Toto1234" / password_confirmation = "Toto1234" | step1Valid retourne true | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-07 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | E-mail vide | email = "" | Erreur : "L'email est requis" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-08 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | E-mail incorrect (format invalide) | email = "toto@titi" | Erreur : "Format d'e-mail invalide" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-09 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | E-mail avec format xxx@yyy.zzz | email = "toto@titi.com" | E-mail accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-10 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Téléphone vide | phone = "" | Erreur : "Format téléphone invalide (10 chiffres commençant par 0)" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-11 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Téléphone incorrect (trop court) | phone = "1234" | Erreur : "Format téléphone invalide (10 chiffres commençant par 0)" | Valeurs limites | ❌ INVALIDE |
| [x] TC-12 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Téléphone incorrect (format invalide) | phone = "+33612345678" | Erreur : "Format téléphone invalide (10 chiffres commençant par 0)" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-13 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Téléphone correct (10 chiffres commençant par 0) | phone = "0123456789" | Téléphone accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-14 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Prénom vide | firstName = "" | Erreur : "Le prénom contient des caractères invalides" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-15 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Prénom trop long (> 50 car.) | firstName = "Jean-Baptiste-Alexandre-Sébastien-Emmanuel-Christophe-Nicolas" | Erreur : "Le prénom contient des caractères invalides" | Valeurs limites | ❌ INVALIDE |
| [x] TC-16 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Prénom court (1 car.) | firstName = "A" | Prénom accepté | Valeurs limites + Équivalence | ✅ VALIDE |
| [x] TC-17 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Prénom avec chiffres | firstName = "R2D2" | Erreur : "Le prénom contient des caractères invalides" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-18 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Prénom avec accents (é,è,ë, ï) | firstName = "Noëmie" | Prénom accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-19 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Prénom avec apostrophe | firstName = "D’Angelo" | Prénom accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-20 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Prénom avec espaces | firstName = "Marie Line" | Prénom accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-21 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Prénom sans accents | firstName = "Sarah" | Prénom accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-22 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Nom de famille vide | lastName = "" | Erreur : "Le nom de famille contient des caractères invalides" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-23 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Nom de famille trop long (> 50 car.) | lastName = "Jean-Baptiste-Alexandre-Sébastien-Emmanuel-Christophe-Nicolas" | Erreur : "Le nom de famille contient des caractères invalides" | Valeurs limites | ❌ INVALIDE |
| [x] TC-24 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Nom de famille court (1 car.) | lastName = "A" | Nom de famille accepté | Valeurs limites + Équivalence | ✅ VALIDE |
| [x] TC-25 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Nom de famille avec chiffres | lastName = "R2D2" | Erreur : "Le nom de famille contient des caractères invalides" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-26 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Nom de famille avec accents (é,è,ë, ï) | lastName = "Noëmie" | Nom de famille accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-27 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Nom de famille avec apostrophe | lastName = "D’Agobert" | Nom de famille accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-28 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Nom de famille avec espaces | lastName = "Dupont Durand" | Nom de famille accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-29 | Unitaire | Vitest | Frontend | validationSchema.spec.ts | S1 - Inscription | Nom de famille sans accents | lastName = "Martineau" | Nom de famille accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-30 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Genre invalide | gender = "masculin" | Erreur "Le genre doit être : Homme, Femme ou Ne pas dire" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-31a | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Genre null | gender = null | Genre accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-31b | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Genre non renseigné | gender = undefined | Genre accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-31c | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Genre vide | gender = '' | Erreur "Genre invalide" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-32 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Genre parmi la liste ('Homme', 'Femme', 'Ne pas dire')  | gender = "Ne pas dire" | Genre accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-33a | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Code Postal invalide (trop long) | ZipCode = "123456" | Erreur "Code postal invalide" | Valeurs limites | ❌ INVALIDE |
| [x] TC-33b | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Code Postal invalide (lettres) | ZipCode = "T3100" | Erreur "Code postal invalide" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-33c | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Code Postal invalide (espaces) | ZipCode = "31 000" | Erreur "Code postal invalide" | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-34 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Code Postal null | zipCode = null | Code postal accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-35 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Code Postal | ZipCode = "12345" | Code postal accepté | Valeurs limites + Équivalence | ✅ VALIDE |
| [x] TC-36 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Conducteur booléen | driver = false | Choix valide | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-37 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Ville non renseignée | town undefined | Accepté car optionnel | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-38 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Ville trop courte (<2) | town = "Y" | Erreur : "La ville doit contenir au moins 2 caractères" | Valeurs limites | ❌ INVALIDE |
| [x] TC-39 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Ville trop longue (>100) | town = "Saint-Jean-de-la-Vallée-des-Charmes-Saint-Laurent-sur-Montagne-et-les-Bois-de-Fleurieu-dans-la-vallée-de-Dana" | Erreur : "La ville ne peut pas dépasser 100 caractères" | Valeurs limites | ❌ INVALIDE |
| [x] TC-40 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Ville | town = "Quimper" | Ville acceptée | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-41 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Modèle de voiture non renseigné | carModel = "" | Accepté car optionnel | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-42 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Modèle de voiture 1 car. | carModel = "A" | Modèle accepté (même si court) | Valeurs limites | ✅ VALIDE |
| [x] TC-43 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Modèle de voiture > 50 car. | carModel = "Ferrari Super-Ultra-Sport-Edition-Luxe-Generation-Turbo-Boost-Extreme-Speed" | Erreur "Le modèle ne peut pas dépasser 50 caractères" | Valeurs limites | ❌ INVALIDE |
| [x] TC-44 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Modèle de voiture | carModel = "Renault Clio" | Modèle accepté | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-45 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Couleur de voiture non renseignée | carColor undefined | Accepté car optionnel | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-46 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Couleur 1 car. | carColor = "X" | Couleur acceptée (même si courte) | Valeurs limites | ✅ VALIDE |
| [x] TC-47 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Couleur > 30 car. | carColor = "Jaune-orangé-soleil-doré-brillant-intense" | Erreur "La couleur ne peut pas dépasser 30 caractères" | Valeurs limites | ❌ INVALIDE |
| [x] TC-48 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Couleur de voiture | carColor = "Jaune" | Couleur acceptée | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-49 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Nombre de places non renseigné | carSeatNb undefined | Accepté car optionnel | Partitionnement d'équivalence | ✅ VALIDE |
| [x] TC-50 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Nombre de places = 0 | CarSeatNb = 0 | Erreur "Le nombre de places doit être au moins 1" | Valeurs limites | ❌ INVALIDE |
| [x] TC-51 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Nombre de places > 8 | CarSeatNb = 9 | Erreur "Le nombre de places ne peut pas dépasser 8" | Valeurs limites | ❌ INVALIDE |
| [x] TC-52 | Unitaire | Vitest | Frontend | userSchema.spec.ts | S1 - Inscription | Nombre de places  | CarSeatNb = 4 | Nombre de places accepté | Valeurs limites + Équivalence | ✅ VALIDE |
| [x] TC-52b | Intégration | Vitest + PHPUnit | Frontend + Backend | register.integration.spec.ts + AuthServiceTest.php | S1 - Inscription | Inscription avec données valides | email: "toto@titi.com", password: "Toto1234", firstName: "Toto", lastName: "TITI", phone: "0607080910" | {"success": true, "message": "Registration successful!", "requiresVerification": true} | Équivalence + Tableaux de décision | ✅ VALIDE |
| [x] TC-52c | Intégration | Vitest + PHPUnit | Frontend + Backend | register.integration.spec.ts + AuthServiceTest.php | S1 - Inscription | Inscription avec e-mail déjà en base | email: "toto@titi.com" (déjà en base) | {"error": "Email already in use"} | Équivalence + Tableaux de décision | ❌ INVALIDE |
| [x] TC-53 | Intégration | Vitest + PHPUnit | Frontend + Backend | login.integration.spec.ts + AuthServiceTest.php | S1 - Connexion | E-mail avec format xxx@yyy.zzz mais pas en base | email = "toto@tata.com" | E-mail inconnu, inscrivez-vous | Tableaux de décision | ❌ INVALIDE |
| [x] TC-54 | Intégration | Vitest + PHPUnit | Frontend + Backend | login.integration.spec.ts + AuthServiceTest.php | S1 - Connexion | E-mail avec format xxx@yyy.zzz, bien en base | email = "toto@titi.com" | E-mail accepté | Équivalence + Tableaux de décision | ✅ VALIDE |
| [x] TC-55 | Intégration | Vitest + PHPUnit | Frontend + Backend | login.integration.spec.ts + AuthServiceTest.php | S1 - Connexion | Mot de passe incorrect | password = "Chabada123" | Erreur : le mot de passe ne correspond pas à l'e-mail fourni | Tableaux de décision | ❌ INVALIDE |
| [x] TC-56 | Intégration | Vitest + PHPUnit | Frontend + Backend | Tests couverts par TC-54 | S1 - Connexion | Connexion avec identifiants valides et existants en base | email = "toto@titi.com" & password = "Toto1234" | Token JWT | Équivalence + Tableaux de décision | ✅ VALIDE |
| [x] TC-57 | Intégration | PHPUnit | Backend | AuthServiceTest.php | S1 - Vérification e-mail | Lien de vérification valide | token = valide en base | Compte activé, verified = true | Tableaux de décision | ✅ VALIDE |
| [x] TC-58 | Intégration | PHPUnit | Backend | AuthServiceTest.php | S1 - Vérification e-mail | Token de vérification expiré | token = expiré | Erreur : "Lien de vérification expiré" | Tableaux de décision | ❌ INVALIDE |
| [x] TC-59 | Intégration | PHPUnit | Backend | Couvert par TC-58 | S1 - Vérification e-mail | Token déjà utilisé | token = already_used | Erreur : "Lien déjà utilisé" | Tableaux de décision | ❌ INVALIDE |
| [x] TC-60 | Intégration | Vitest + PHPUnit | Frontend + Backend | login.integration.spec.ts + AuthServiceTest.php | S1 - Connexion | Connexion avec compte non vérifié | email = "toto@titi.com" & password = "Toto1234" & verified = false | Erreur : "Veuillez vérifier votre adresse e-mail" | Tableaux de décision | ❌ INVALIDE |
| [ ] TC-61 | Intégration | PHPUnit | Backend |  | S1 - Vérification token | Token JWT falsifié | Authorization: Bearer token_falsifié | Erreur 401 : "Token invalide" | Partitionnement d'équivalence | ❌ INVALIDE |
| [ ] TC-62 | Intégration | PHPUnit | Backend |  | S1 - Vérification token | Token JWT expiré | Authorization: Bearer token_expiré | Erreur 401 : "Token expiré" | Partitionnement d'équivalence | ❌ INVALIDE |
| [ ] TC-63 | Intégration | PHPUnit | Backend |  | S1 - Connexion | Tentatives répétées de connexion échouées [À implémenter] | 5+ tentatives avec mauvais mot de passe | Accès temporairement bloqué | Partitionnement d'équivalence | 🔜 À IMPLÉMENTER |
| [ ] TC-64 | Intégration | PHPUnit | Backend |  | S1 - Inscription | Champs véhicule fournis malgré driver=false | driver = false & carModel = "Clio" & carSeatNb = 4 | Champs véhicule ignorés | Tableaux de décision | ✅ VALIDE |
| [ ] TC-65 | UI | Vitest + vue/test-utils | Frontend |  | S1 - Inscription | Affichage messages d'erreur Zod en UI | email = "" soumis via formulaire | Message d'erreur affiché dans le DOM | Partitionnement d'équivalence | ❌ INVALIDE |
| [ ] TC-66 | UI | Vitest + vue/test-utils | Frontend |  | S1 - Inscription | Bouton Suivant désactivé si step1 invalide | Formulaire vide à l'étape 1 | Bouton Suivant disabled | Partitionnement d'équivalence | ❌ INVALIDE |
| [ ] TC-67 | UI | Vitest + vue/test-utils | Frontend |  | S1 - Inscription | Progression vers étape 2 si step1 valide | Tous champs étape 1 valides | Étape 2 affichée, barre de progression à 50% | Partitionnement d'équivalence | ✅ VALIDE |
| [ ] TC-68 | UI | Vitest + vue/test-utils | Frontend |  | S1 - Inscription | Affichage erreur confirmation mot de passe | password = "Toto1234" / password_confirmation = "Toto5678" | Message "Les mots de passe ne correspondent pas" visible dans le DOM | Partitionnement d'équivalence | ❌ INVALIDE |
| [ ] TC-69 | E2E | Selenium | Frontend + Backend |  | S1 - Inscription | Parcours inscription complet via formulaire | Tous champs valides saisis en UI | Message de succès affiché, e-mail envoyé | Partitionnement d'équivalence | ✅ VALIDE |
| [ ] TC-70 | E2E | Selenium | Frontend + Backend |  | S1 - Connexion | Parcours connexion + redirection | email = "toto@titi.com" & password = "Toto1234" saisis en UI | Redirection vers dashboard, token JWT stocké | Partitionnement d'équivalence | ✅ VALIDE |
| [ ] TC-71 | E2E | Selenium | Frontend + Backend |  | S1 - Inscription | Affichage étape 5 après inscription réussie | Inscription complète avec e-mail valide | Étape 5 "Vérifiez votre email" affichée | Partitionnement d'équivalence | ✅ VALIDE |
| [ ] TC-72 | Intégration | PHPUnit | Backend |  | S1 - Inscription | Injection SQL dans le champ e-mail | email = "' OR 1=1--" | Erreur : "Format d'e-mail invalide": requête non exécutée | Partitionnement d'équivalence | ❌ INVALIDE |
| [ ] TC-73 | Intégration | PHPUnit | Backend |  | S1 - Inscription | Injection XSS dans le champ prénom | firstName = "<script>alert(1)</script>" | Erreur : "Le prénom contient des caractères invalides": script non exécuté | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-74 | Intégration | Vitest | Frontend  | logout.integration.spec.ts | S1 - Déconnexion | Déconnexion : suppression du token et réinitialisation du store |  Utilisateur authentifié (token en store + localStorage) | isAuthenticated = false, token null, user null | Partitionnement d'équivalence | ✅ VALIDE |
| [ ] TC-75 | Intégration | PHPUnit | Backend |  | S1 - Connexion | Verrouillage de compte après N échecs | 10+ tentatives échouées sur le même compte | Compte verrouillé, erreur spécifique retournée | Partitionnement d'équivalence | 🔜 À IMPLÉMENTER |
| [x] TC-76 | Intégration | Vitest | Frontend | register.integration.spec.ts | S1 - Inscription | Backend indisponible lors de l'inscription | API répond 503 | Message d'erreur générique affiché, pas de crash | Partitionnement d'équivalence | ❌ INVALIDE |
| [x] TC-77 | Intégration | Vitest | Frontend | login.integration.spec.ts  | S1 - Connexion | Backend indisponible lors de la connexion | API répond 503 | Message d'erreur générique affiché, pas de crash | Partitionnement d'équivalence | ❌ INVALIDE |
| [ ] TC-78 | Intégration | PHPUnit | Backend |  | S1 - Connexion | Rate limiting global de l'API | 100+ requêtes/minute depuis la même IP | Erreur 429 : Too Many Requests | Partitionnement d'équivalence | 🔜 À IMPLÉMENTER |
| [ ] TC-79 | UI | Vitest + vue/test-utils | Frontend |  | S1 - Connexion | Expiration du token JWT en session active | Token JWT expiré, utilisateur sur /dashboard | Redirection automatique vers /login | Partitionnement d'équivalence | ❌ INVALIDE |
| [ ] TC-80 | UI | Vitest + vue/test-utils | Frontend |  | S1 - Connexion | Rafraîchissement de page en session authentifiée | Token JWT valide en localStorage, F5 sur /dashboard | Session maintenue, utilisateur reste connecté | Partitionnement d'équivalence | ✅ VALIDE |
| [ ] TC-81 | E2E | Selenium | Frontend |  | S1 - Connexion | Navigation directe vers route protégée sans token | Accès à /dashboard sans token JWT | Redirection vers /login | Partitionnement d'équivalence | ❌ INVALIDE |

---

## Story 2: Lieux préenregistrés

| ID    | Type | Outil | Couche | Fichier test | User Story | Description | Données d'entrée | Résultat attendu | Technique | Statut |
| ------- | --- | --- | --- | --- | --- | --- | --- | --- | --- | ---|
| [x] TC-82 | Unitaire | Vitest | Frontend | townSearch.spec.ts | S2 - Autocomplétion ville | n'entre pas de chaîne (champ vide) pour la ville | q="" | Autocomplétion non déclenchée, liste vide | Valeurs limites | ❌ INVALIDE |
| [x] TC-83 | Unitaire | Vitest | Frontend | townSearch.spec.ts | S2 - Autocomplétion ville | entre une ville avec une seule lettre | q='r' | Autocomplétion non déclenchée, liste vide | Valeurs limites | ❌ INVALIDE |
| [x] TC-84 | Unitaire | Vitest | Frontend | townSearch.spec.ts | S2 - Autocomplétion ville | entre une ville avec caractères spéciaux (@#<>?&+!) | q='@#<>?&+!' | Autocomplétion non déclenchée, liste vide | Équivalence | ❌ INVALIDE |
| [ ] TC-85 | Intégration | Vitest + PHPUnit | Frontend + Backend |  | S2 - Autocomplétion ville | appel API direct avec une lettre (bypass frontend) | search?q=r&limit=10 | Erreur : "La recherche doit contenir au moins 2 caractères". En frontend, autocomplétion non déclenchée, liste vide affichée sans message d'erreur | Valeurs limites | ❌ INVALIDE |
| [ ] TC-86 | Intégration | Vitest + PHPUnit | Frontend + Backend |  | S2 - Autocomplétion ville | entre deux lettres qui correspondent aux premières lettres d'une ville avec une seule réponse | search?q=ra&limit=10 | {   "success": true,   "data": [     {       "id": 228,       "name": "Rannée",       "postal_code": "35130",       "insee_code": "35235"     }   ],   "count": 1,   "query": "ra" } | Valeurs limites + Équivalence | ✅ VALIDE |
| [ ] TC-87 | Intégration | Vitest + PHPUnit | Frontend + Backend |  | S2 - Autocomplétion ville | entre deux lettres qui correspondent aux premières lettres d'une ville avec plusieurs réponses | search?q=re&limit=10 | { "success": true, "data": [ { "id": 229, "name": "Rédené", "postal_code": "29300", "insee_code": "29234" }, { "id": 230, "name": "Redon", "postal_code": "35600", "insee_code": "35236" }, { "id": 231, "name": "Rennes", "postal_code": "35000", "insee_code": "35238" }, { "id": 232, "name": "Retiers", "postal_code": "35240", "insee_code": "35239" } ], "count": 4, "query": "re" } | Valeurs limites + Équivalence | ✅ VALIDE |
| [ ] TC-88 | Intégration | Vitest + PHPUnit | Frontend + Backend |  | S2 - Autocomplétion ville | entre deux lettres qui ne correspondent à aucune premières lettres d'une ville | search?q=rz&limit=10 | {   "success": true,   "data": [],   "count": 0,   "query": "rz" } | Valeurs limites + Équivalence | ✅ VALIDE |
| [ ] TC-89 | Intégration | Vitest + PHPUnit | Frontend + Backend |  | S2 - Autocomplétion ville | entre une ville avec accents ou tirets | search?q=rann%C3%A9e&limit=10 | {   "success": true,   "data": [     {       "id": 228,       "name": "Rannée",       "postal_code": "35130",       "insee_code": "35235"     }   ],   "count": 1,   "query": "rannée" } | Valeurs limites + Équivalence | ✅ VALIDE |
| [ ] TC-90 | Intégration | PHPUnit | Backend |  | S2 - Autocomplétion ville | entre une ville avec caractères spéciaux (@#<>?&+!) | search?q=%40%23%3C%3E%3F%26%2B%21&limit=10 | Erreur 422 : "Caractères non autorisés dans la recherche" | Équivalence | ❌ INVALIDE |

---

**Last updated**: March 29, 2026
