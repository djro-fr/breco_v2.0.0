<!-- backend\breco\templates\email\html\verification.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #4F46E5;
        }
        .header h1 {
            color: #4F46E5;
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px 0;
        }
        .content p {
            margin: 15px 0;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            padding: 14px 30px;
            background-color: #4F46E5;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .button:hover {
            background-color: #4338CA;
        }
        .link-box {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 4px;
            word-break: break-all;
            font-size: 14px;
            color: #6b7280;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .warning {
            color: #dc2626;
            font-size: 14px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚗 Breco</h1>
        </div>

        <div class="content">
            <h2>Bienvenue sur Breco, <?= h($userName) ?> !</h2>

            <p>Merci de vous être inscrit sur notre plateforme de covoiturage en Bretagne.</p>

            <p>Pour activer votre compte et commencer à utiliser Breco, veuillez cliquer sur le bouton ci-dessous :</p>

            <div style="text-align: center;">
                <a href="<?= $verificationUrl ?>" class="button">Valider mon compte</a>
            </div>

            <p>Ou copiez et collez ce lien dans votre navigateur :</p>
            <div class="link-box">
                <?= $verificationUrl ?>
            </div>

            <p class="warning">⚠️ Ce lien expirera dans 24 heures.</p>

            <p>Si vous n'avez pas créé de compte sur Breco, vous pouvez ignorer cet email en toute sécurité.</p>
        </div>

        <div class="footer">
            <p>À bientôt sur Breco !<br>L'équipe Breco</p>
            <p style="font-size: 12px; color: #9ca3af;">
                Cet email a été envoyé automatiquement, merci de ne pas y répondre.
            </p>
        </div>
    </div>
</body>
</html>
