<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThemeController extends Controller
{
    public function index()
    {
        $settings = ThemeSetting::pluck('value', 'key')->toArray();
        $activePresetId = (int) ThemeSetting::where('key', 'active_theme_preset')->value('value');
        $presets = \App\Models\ThemePreset::all();
        return view('admin.theme.index', compact('settings', 'presets', 'activePresetId'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            ThemeSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear active preset since manual changes were made
        ThemeSetting::updateOrCreate(
            ['key' => 'active_theme_preset'],
            ['value' => null]
        );

        Cache::forget('theme_settings');

        return redirect()->back()->with('success', 'Theme settings updated successfully.');
    }

    public function applyPreset(Request $request)
    {
        $preset = \App\Models\ThemePreset::findOrFail($request->preset_id);
        
        foreach ($preset->settings as $key => $value) {
            ThemeSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Set active preset
        ThemeSetting::updateOrCreate(
            ['key' => 'active_theme_preset'],
            ['value' => $preset->id]
        );

        Cache::forget('theme_settings');

        return redirect()->back()->with('success', 'Theme preset applied successfully.');
    }

    public function storePreset(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $settings = ThemeSetting::where('key', '!=', 'active_theme_preset')
            ->pluck('value', 'key')
            ->toArray();

        $preset = \App\Models\ThemePreset::create([
            'name' => $request->name,
            'settings' => $settings,
            'is_default' => false,
        ]);

        // Set as active
        ThemeSetting::updateOrCreate(
            ['key' => 'active_theme_preset'],
            ['value' => $preset->id]
        );

        return redirect()->back()->with('success', 'Theme saved as new preset.');
    }

    public function editPreset($id)
    {
        $preset = \App\Models\ThemePreset::findOrFail($id);

        if ($preset->is_default) {
            return redirect()->back()->with('error', 'Cannot edit default presets.');
        }

        $settings = $preset->settings;
        return view('admin.theme.edit_preset', compact('preset', 'settings'));
    }

    public function updatePreset(Request $request, $id)
    {
        $preset = \App\Models\ThemePreset::findOrFail($id);

        if ($preset->is_default) {
            return redirect()->back()->with('error', 'Cannot update default presets.');
        }

        // If coming from the edit form, use the request data
        if ($request->has('primary_color')) {
            $data = $request->except(['_token', '_method']);
            $preset->update(['settings' => $data]);
            return redirect()->route('admin.theme.index')->with('success', 'Preset updated successfully.');
        }

        // Fallback for the "Update with current settings" button (if we keep it, or remove it)
        // For now, let's assume the user only wants the dedicated edit page.
        // But to be safe, if no data provided, use current global settings? 
        // No, let's stick to the form submission.
        
        return redirect()->back()->with('error', 'No settings provided.');
    }

    public function destroyPreset($id)
    {
        $preset = \App\Models\ThemePreset::findOrFail($id);

        if ($preset->is_default) {
            return redirect()->back()->with('error', 'Cannot delete default presets.');
        }

        $preset->delete();

        return redirect()->back()->with('success', 'Preset deleted successfully.');
    }
}
