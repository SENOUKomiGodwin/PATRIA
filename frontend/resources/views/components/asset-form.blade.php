@props([
    'nom' => null,
    'categorie' => null,
    'etat' => null,
    'date_acquisition' => null,
    'valeur_acquisition' => null,
    'service' => null,
    'localisation' => null,
    'bouton_principal' => 'Créer le bien',
])

<!-- Section Identification -->
<h3 class="text-base font-semibold text-patria-green-text mb-6">Identification</h3>
<div class="space-y-6 mb-8">
    <div>
        <label class="block text-sm text-patria-green-text mb-1">Nom du bien *</label>
        <x-input type="text" placeholder="Ex. Toyota Hilux 2.4 D" value="{{ $nom }}" />
    </div>
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="block text-sm text-patria-green-text mb-1">Catégorie *</label>
            <x-select>
                <option value="">Sélectionner une catégorie</option>
                <option value="Véhicules" {{ $categorie == 'Véhicules' ? 'selected' : '' }}>Véhicules</option>
            </x-select>
        </div>
        <div>
            <label class="block text-sm text-patria-green-text mb-1">État *</label>
            <x-select>
                <option value="">Sélectionner un état</option>
                <option value="Bon" {{ $etat == 'Bon' ? 'selected' : '' }}>Bon</option>
            </x-select>
        </div>
    </div>
</div>

<!-- Separator -->
<div class="border-t border-patria-border mb-8"></div>

<!-- Section Acquisition et affectation -->
<h3 class="text-base font-semibold text-patria-green-text mb-6">Acquisition et affectation</h3>
<div class="grid grid-cols-2 gap-6 mb-8">
    <div>
        <label class="block text-sm text-patria-green-text mb-1">Date d’acquisition *</label>
        <x-input type="text" placeholder="jj/mm/aaaa" value="{{ $date_acquisition }}" />
    </div>
    <div>
        <label class="block text-sm text-patria-green-text mb-1">Valeur d’acquisition (FCFA) *</label>
        <x-input type="text" placeholder="0" value="{{ $valeur_acquisition }}" />
    </div>
    <div>
        <label class="block text-sm text-patria-green-text mb-1">Service affectataire *</label>
        <x-select>
            <option value="">Sélectionner un service</option>
            <option value="Service logistique" {{ $service == 'Service logistique' ? 'selected' : '' }}>Service logistique</option>
        </x-select>
    </div>
    <div>
        <label class="block text-sm text-patria-green-text mb-1">Localisation *</label>
        <x-select>
            <option value="">Sélectionner un lieu</option>
            <option value="Parc automobile · Bât. A" {{ $localisation == 'Parc automobile · Bât. A' ? 'selected' : '' }}>Parc automobile · Bât. A</option>
        </x-select>
    </div>
</div>

<!-- Footer -->
<div class="border-t border-patria-border pt-6">
    <p class="text-sm text-patria-text-secondary mb-4">* Champs obligatoires</p>
    <div class="flex justify-end gap-3">
        <a href="{{ route('assets.index') }}" class="px-4 py-2 border border-patria-border text-patria-green-text rounded-button hover:bg-patria-bg transition-colors">Annuler</a>
        <x-button variant="primary">{{ $bouton_principal }}</x-button>
    </div>
</div>
