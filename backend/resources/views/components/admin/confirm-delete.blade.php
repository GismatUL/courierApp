{{-- Spec §2.1: deleting asks for confirmation ("Bəli" / "İmtina"). Works with the ConfirmsDeletion trait. --}}
<div x-data="{ show: $wire.entangle('deletingId').live }" x-show="show !== null" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 px-4" role="alertdialog" aria-modal="true" aria-labelledby="confirm-delete-title">
    <div x-show="show !== null" x-transition @click.outside="$wire.cancelDelete()" class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
        <h2 id="confirm-delete-title" class="text-base font-semibold">Silmək istədiyinizə əminsiniz?</h2>
        <p class="mt-1.5 text-sm text-muted">Bu əməliyyat geri qaytarıla bilməz.</p>
        <div class="mt-5 flex justify-end gap-2">
            <x-admin.button variant="secondary" wire:click="cancelDelete">İmtina</x-admin.button>
            <x-admin.button wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Bəli, sil</x-admin.button>
        </div>
    </div>
</div>
