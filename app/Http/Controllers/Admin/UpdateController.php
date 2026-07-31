<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UpdateController extends Controller
{
    protected $updateService;

    public function __construct(UpdateService $updateService)
    {
        $this->updateService = $updateService;
        $this->middleware('permission:manage_settings');
    }

    public function index()
    {
        $status = $this->updateService->getUpdateStatus();
        return view('admin.updates.index', compact('status'));
    }

    public function check(Request $request)
    {
        $updateInfo = $this->updateService->checkForUpdates();
        if ($updateInfo) {
            Cache::put('last_update_check', now()->toDateTimeString());
            return redirect()->route('admin.updates.index')
                ->with('success', '✅ Update available! Version ' . $updateInfo['version']);
        }
        return redirect()->route('admin.updates.index')
            ->with('info', '✅ Your system is up to date.');
    }

    public function install(Request $request)
    {
        $request->validate([
            'version' => 'required|string',
            'download_url' => 'required|url'
        ]);

        $result = $this->updateService->downloadAndInstall(
            $request->version,
            $request->download_url
        );

        if ($result['success']) {
            Cache::forget('update_available');
            return redirect()->route('admin.dashboard')
                ->with('success', $result['message']);
        }
        return redirect()->route('admin.updates.index')
            ->with('error', '❌ ' . $result['message']);
    }

    public function status()
    {
        return response()->json($this->updateService->getUpdateStatus());
    }
}