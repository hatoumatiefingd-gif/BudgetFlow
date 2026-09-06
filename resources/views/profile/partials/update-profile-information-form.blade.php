<section>
<header>
<h2 class="text-lg font-medium text-gray-900">
           Informations du profil
</h2>
<p class="mt-1 text-sm text-gray-600">
           Modifiez votre nom et votre adresse e-mail.
</p>
</header>

   {{-- Formulaire permettant de modifier les informations du profil --}}
<form method="POST"
     action="{{ route('profile.update') }}"
     class="mt-6 space-y-6">
       @csrf
       @method('PATCH')

       {{-- Nom de l'utilisateur --}}
<div>
<x-input-label
               for="name"
               value="Nom"
           />
<x-text-input
               id="name"
               name="name"
               type="text"
               class="mt-1 block w-full"
               :value="old('name', $user->name)"
               required
               autofocus
               autocomplete="name"
           />
<x-input-error
               class="mt-2"
               :messages="$errors->get('name')"
           />
</div>

       {{-- Adresse e-mail --}}
<div>
<x-input-label
               for="email"
               value="Adresse e-mail"
           />
<x-text-input
               id="email"
               name="email"
               type="email"
               class="mt-1 block w-full"
               :value="old('email', $user->email)"
               required
               autocomplete="username"
           />
<x-input-error
               class="mt-2"
               :messages="$errors->get('email')"
           />
</div>

<div class="flex items-center gap-4">
<x-primary-button>
               Enregistrer les modifications
</x-primary-button>

           {{-- Message affiché après une modification réussie --}}
           @if (session('status') === 'profile-updated')
<p class="text-sm text-green-600">
                   Modifications enregistrées.
</p>
           @endif
</div>
</form>
</section>