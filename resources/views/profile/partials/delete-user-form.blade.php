<section>

    {{-- Titre de la suppression du compte --}}
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Supprimer le compte
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Une fois votre compte supprimé, toutes vos données seront définitivement supprimées.
            Avant de continuer, assurez-vous d’avoir conservé les informations importantes.
        </p>
    </header>


    {{-- Bouton d'ouverture de la confirmation --}}
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">

        Supprimer mon compte

    </x-danger-button>


    {{-- Fenêtre de confirmation --}}
    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable>

        <form
            method="POST"
            action="{{ route('profile.destroy') }}"
            class="p-6">

            @csrf
            @method('delete')


            <h2 class="text-lg font-medium text-gray-900">
                Voulez-vous vraiment supprimer votre compte ?
            </h2>


            <p class="mt-1 text-sm text-gray-600">
                Cette action est définitive. Entrez votre mot de passe pour confirmer la suppression.
            </p>


            <div class="mt-6">

                <x-input-label
                    for="password"
                    value="Mot de passe"
                    class="sr-only"
                />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Mot de passe"
                />

                <x-input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2"
                />

            </div>


            <div class="mt-6 flex justify-end">

                <x-secondary-button
                    x-on:click="$dispatch('close')">

                    Annuler

                </x-secondary-button>


                <x-danger-button class="ms-3">

                    Supprimer définitivement

                </x-danger-button>

            </div>

        </form>

    </x-modal>

</section>