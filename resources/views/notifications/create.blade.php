<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une notification</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Ajouter une notification</h1>

        <form action="{{ route('notifications.store') }}" method="POST">
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