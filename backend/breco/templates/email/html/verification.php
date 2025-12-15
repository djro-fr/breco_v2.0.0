<!-- backend\breco\templates\email\html\verification.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

        <!-- Header -->
        <div style="text-align: center; padding-bottom: 20px; border-bottom: 2px solid #4F46E5;">
            <h1 style="color: #4F46E5; margin: 0; font-size: 28px;">🚗 Breco</h1>
        </div>

        <!-- Content -->
        <div style="padding: 30px 0;">
            <h2 style="color: #333; font-size: 22px;">Bienvenue sur Breco, <?= h($userName) ?> !</h2>

            <p style="margin: 15px 0; font-size: 16px;">
                Merci de vous être inscrit sur notre plateforme de covoiturage en Bretagne.
            </p>

            <p style="margin: 15px 0; font-size: 16px;">
                Pour activer votre compte et commencer à utiliser Breco, veuillez cliquer sur le bouton ci-dessous :
            </p>

            <!-- Button -->
            <div style="text-align: center; margin: 25px 0;">
                <a href="<?= h($verificationUrl) ?>"
                   style="display: inline-block; padding: 14px 30px; background-color: #4F46E5; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                    Valider mon compte
                </a>
            </div>

            <p style="margin: 15px 0; font-size: 16px;">
                Ou copiez et collez ce lien dans votre navigateur :
            </p>

            <!-- Link box -->
            <div style="background-color: #f9fafb; padding: 15px; border-radius: 4px; word-break: break-all; font-size: 14px; color: #6b7280; margin: 15px 0;">
                <?= h($verificationUrl) ?>
            </div>

            <!-- Warning-->
            <p style="margin: 20px 0; font-size: 16px; color: #dc2626; font-weight: bold; padding: 12px; background-color: #fef2f2; border-left: 4px solid #dc2626; border-radius: 4px;">
                ⚠️ Ce lien expirera dans 24 heures.
            </p>

            <p style="margin: 15px 0; font-size: 16px; color: #6b7280;">
                Si vous n'avez pas créé de compte sur Breco, vous pouvez ignorer cet email en toute sécurité.
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">
            <p style="margin: 10px 0;">À bientôt sur Breco !<br>L'équipe Breco</p>
            <p style="font-size: 12px; color: #9ca3af; margin: 10px 0;">
                Cet email a été envoyé automatiquement, merci de ne pas y répondre.
            </p>
        </div>

    </div>
</body>
</html>
