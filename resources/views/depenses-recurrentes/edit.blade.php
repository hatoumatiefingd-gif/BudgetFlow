<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une dépense récurrente</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=3">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Modifier une dépense récurrente</h1>

        {{-- Formulaire envoyé à DepenseRecurrenteController@update --}}
        <form action="{{ route('depenses-recurrentes.update', $recurrente->idDepenseRecurrente) }}" method="POST">

            @csrf
            {{-- @method('PUT') : Laravel traite ce formulaire comme une modification --}}
            @method('PUT')

            <label>Nom de la dépense</label>
            <input
                type="text"
                name="nomDepenseRecurrente"
                value="{{ $recurrente->nomDepenseRecurrente }}"
                required
            >

            <label>Montant</label>
            <input
                type="number"
                step="0.01"
                name="montant"
                value="{{ $recurrente->montant }}"
                required
            >

            <label>Catégorie</label>
            <select name="idCategorie" required>

                {{-- La catégorie actuelle est sélectionnée par défaut --}}
                @foreach($categories as $categorie)

                    <option
                        value="{{ $categorie->idCategorie }}"
                        {{ $categorie->idCategorie == $recurrente->idCategorie ? 'selected' : '' }}
                    >
                        {{ ucfirst($categorie->nomCategorie) }}
                    </option>

                @endforeach

            </select>

            <label>Fréquence</label>
            {{-- La fréquence actuelle est sélectionnée par défaut --}}
            <select name="frequence" required>

                <option
                    value="Mensuel"
                    {{ $recurrente->frequence == 'Mensuel' ? 'selected' : '' }}
                >
                    Mensuel
                </option>

                <option
                    value="Hebdomadaire"
                    {{ $recurrente->frequence == 'Hebdomadaire' ? 'selected' : '' }}
                >
                    Hebdomadaire
                </option>

                <option
                    value="Annuel"
                    {{ $recurrente->frequence == 'Annuel' ? 'selected' : '' }}
                >
                    Annuel
                </option>

            </select>

            <label>Prochaine date</label>
            <input
                type="date"
                name="prochaineDate"
                value="{{ $recurrente->prochaineDate }}"
                required
            >

            <button type="submit">
                Enregistrer les modifications
            </button>

        </form>

        <a href="{{ route('depenses-recurrentes.index') }}" class="back-link">
            ← Retour
        </a>

    </div>
</div>

</body>
</html>