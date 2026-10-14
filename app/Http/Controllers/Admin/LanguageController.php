<?php

namespace App\Http\Controllers\Admin;

use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LanguageController extends BaseAdminController
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:languages,name'],
        ]);

        return $this->tryAction(function () use ($validated) {
            Language::create([
                'name' => $validated['name'],
                'code' => Str::slug($validated['name'], '_'),
            ]);
        }, 'Language added.', 'admin.settings.index', ['languages']);
    }

    public function destroy(Language $language): RedirectResponse
    {
        if ($language->is_default) {
            return back()->with('flash_error', 'The default language cannot be deleted.');
        }

        return $this->tryAction(function () use ($language) {
            $language->delete();
        }, 'Language removed.', 'admin.settings.index', ['languages']);
    }
}
