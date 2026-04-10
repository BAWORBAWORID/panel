<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Http\Requests\Admin\AdminFormRequest;
use Pterodactyl\Contracts\Repository\SettingsRepositoryInterface;

class ThemeController extends Controller
{
    public function __construct(private SettingsRepositoryInterface $settings)
    {
    }

    /**
     * Menampilkan halaman UI Theme Designer.
     */
    public function index(): View
    {
        return view('admin.theme.index', [
            'settings' => $this->settings
        ]);
    }

    /**
     * Menyimpan pengaturan warna yang dikirim dari form Designer.
     */
    public function update(AdminFormRequest $request): RedirectResponse
    {
        // Looping semua input yang berawalan 'palette_' dan simpan ke database
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            if (str_starts_with($key, 'palette_')) {
                $this->settings->set('ow_theme::' . $key, $value);
            }
        }

        // Redirect kembali ke halaman designer dengan pesan sukses
        return redirect()->route('admin.theme.index')->with('success', 'Theme colors updated successfully!');
    }
}
