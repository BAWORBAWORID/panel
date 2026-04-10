<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\Models\AdminAlert;
use Pterodactyl\Http\Controllers\Controller;
use Prologue\Alerts\Facades\Alert;

class AlertController extends Controller
{
    public function index(): View
    {
        $alerts = AdminAlert::with('creator')->latest()->paginate(20);
        return view('admin.alerts.index', compact('alerts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(AdminAlert::$validationRules);

        AdminAlert::where('active', true)->update(['active' => false]);

        AdminAlert::create([
            'title'        => $request->input('title'),
            'message'      => $request->input('message'),
            'type'         => $request->input('type'),
            'icon'         => $request->input('icon'),
            'position'     => $request->input('position'),
            'bg_color'     => $request->input('bg_color'),
            'border_color' => $request->input('border_color'),
            'text_color'   => $request->input('text_color'),
            'dismissable'  => (bool) $request->input('dismissable'),
            'active'       => true,
            'created_by'   => $request->user()->id,
        ]);

        Alert::success('Alert published successfully.')->flash();
        return redirect()->route('admin.alerts.index');
    }

    public function toggle(Request $request, AdminAlert $alert): RedirectResponse
    {
        if (!$alert->active) {
            AdminAlert::where('active', true)->update(['active' => false]);
            $alert->update(['active' => true]);
            Alert::success('Alert activated.')->flash();
        } else {
            $alert->update(['active' => false]);
            Alert::success('Alert deactivated.')->flash();
        }
        return redirect()->route('admin.alerts.index');
    }

    public function destroy(AdminAlert $alert): RedirectResponse
    {
        $alert->delete();
        Alert::success('Alert deleted.')->flash();
        return redirect()->route('admin.alerts.index');
    }
}
