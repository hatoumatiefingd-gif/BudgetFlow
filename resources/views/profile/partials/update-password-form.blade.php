<section>
<header>
<h2 class="text-lg font-medium text-gray-900">
           Modifier le mot de passe
</h2>
<p class="mt-1 text-sm text-gray-600">
           Utilisez un mot de passe suffisamment long et sécurisé pour protéger votre compte.
</p>
</header>

   {{-- Formulaire permettant de modifier le mot de passe --}}
<form method="POST"
         action="{{ route('password.update') }}"
         class="mt-6 space-y-6">
       @csrf
       @method('PUT')

       {{-- Mot de passe actuel --}}
<div>
<x-input-label
               for="update_password_current_password"
               value="Mot de passe actuel"
           />
<x-text-input
               id="update_password_current_password"
               name="current_password"
               type="password"
               class="mt-1 block w-full"
               autocomplete="current-password"
           />
<x-input-error
               :messages="$errors->updatePassword->get('current_password')"
               class="mt-2"
           />
</div>

       {{-- Nouveau mot de passe --}}
<div>
<x-input-label
               for="update_password_password"
               value="Nouveau mot de passe"
           />
<x-text-input
               id="update_password_password"
               name="password"
               type="password"
               class="mt-1 block w-full"
               autocomplete="new-password"
           />
<x-input-error
               :messages="$errors->updatePassword->get('password')"
               class="mt-2"
           />
</div>

       {{-- Confirmation du nouveau mot de passe --}}
<div>
<x-input-label
               for="update_password_password_confirmation"
               value="Confirmer le nouveau mot de passe"
           />
<x-text-input
               id="update_password_password_confirmation"
               name="password_confirmation"
               type="password"
               class="mt-1 block w-full"
               autocomplete="new-password"
           />
<x-input-error
               :messages="$errors->updatePassword->get('password_confirmation')"
               class="mt-2"
           />
</div>

<div class="flex items-center gap-4">
<x-primary-button>
               Enregistrer le mot de passe
</x-primary-button>

           {{-- Message affiché lorsque le mot de passe a été modifié --}}
           @if (session('status') === 'password-updated')
<p class="text-sm text-green-600">
                   Mot de passe modifié avec succès.
</p>
           @endif
</div>
</form>
</section>