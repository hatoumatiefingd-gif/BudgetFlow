<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une notification</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=4">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Modifier une notification</h1>

        {{-- Messages d'erreur si la saisie est refusée --}}
        @include('partials.erreurs')

        {{-- Le formulaire envoie les modifications à NotificationBudgetController@update --}}
        <form action="{{ route('notifications.update', $notification->idNotification) }}" method="POST">
            @csrf
            {{-- Un formulaire HTML ne connaît que GET et POST : @method indique à Laravel que c'est un PUT --}}
            @method('PUT')

            <label>Titre</label>
            <input type="text" name="titre" value="{{ $notification->titre }}" required>

            <label>Message</label>
            <input type="text" name="message" value="{{ $notification->message }}" required>

            {{-- Le type actuel est présélectionné grâce à l'attribut "selected" --}}
            <label>Type</label>
            <select name="type" required>
                @foreach(['Alerte', 'Paiement', 'Réussite', 'Information'] as $type)
                    <option value="{{ $type }}" {{ $notification->type == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>

            <label>Date</label>
            <input type="date" name="dateNotification" value="{{ $notification->dateNotification }}" required>

            <button type="submit">Enregistrer les modifications</button>
        </form>

        <a href="{{ route('notifications.index') }}" class="back-link">
            ← Retour aux notifications
        </a>

    </div>
</div>

</body>
</html>
