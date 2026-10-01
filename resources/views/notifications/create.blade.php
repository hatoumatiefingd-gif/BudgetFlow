<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une notification</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=3">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Ajouter une notification</h1>

        {{-- Formulaire envoyé à NotificationBudgetController@store --}}
        <form action="{{ route('notifications.store') }}" method="POST">
            {{-- Jeton de sécurité obligatoire pour les formulaires POST (protection CSRF) --}}
            @csrf

            <label>Titre</label>
            <input type="text" name="titre" required>

            <label>Message</label>
            <input type="text" name="message" required>

            <label>Type</label>
            <select name="type" required>
                <option value="Alerte">Alerte</option>
                <option value="Paiement">Paiement</option>
                <option value="Réussite">Réussite</option>
                <option value="Information">Information</option>
            </select>

            <label>Date</label>
            <input type="date" name="dateNotification" required>

            <button type="submit">Enregistrer</button>
        </form>

        <a href="{{ route('notifications.index') }}" class="back-link">
            ← Retour aux notifications
        </a>

    </div>
</div>

</body>
</html>