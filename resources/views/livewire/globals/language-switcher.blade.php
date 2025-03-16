<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $language;

    public function mount()
    {
        $this->language = auth()->user()?->language ?? session('locale', 'en');
    }

    public function updatedLanguage()
    {
        if (in_array($this->language,['en','fr'])) {
            session()->put('locale', $this->language);
            session()->flash('here',session('locale'));
//            if (auth()->check())
//            {
//                $user = auth()->user();
//                $user->language = $this->language;
//                $user->save();
//            }
            $this->redirect(request()->header('Referer'));
        }
    }
}; ?>

<div>
    <select id="language" wire:model.live="language">
        <option value="en">EN</option>
        <option value="fr">FR</option>
    </select>
</div>
