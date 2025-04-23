<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileActivityLog;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Events\FileStatusChanged;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $query = File::query();

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        //show files claimed by the current user
        $query->where('claimed_by', auth()->id());

        $files = $query->paginate(20);

        return view('files.index', compact('files'));
    }

    public function claimBatch(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'count' => 'required|integer|min:1|max:20',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Get unclaimed files up to the requested count
        $files = $order->files()
            ->where('status', 'unclaimed')
            ->limit($request->count)
            ->get();

        if ($files->isEmpty()) {
            return redirect()->back()->with('error', 'No unclaimed files available.');
        }

        foreach ($files as $file) {
            $file->update([
                'claimed_by' => auth()->id(),
                'status' => 'in_progress',
                'claimed_at' => now(),
            ]);

            // Log the activity
            FileActivityLog::create([
                'file_id' => $file->id,
                'user_id' => auth()->id(),
                'action' => 'claimed',
                'description' => 'File claimed for editing',
            ]);

            // Broadcast the event
            event(new FileStatusChanged(
                $file,
                'unclaimed',
                'in_progress',
                auth()->user()->name
            ));
            $order->updateStatusBasedOnFiles();
        }

        return redirect()->route('files.index', ['order_id' => $request->order_id])
            ->with('success', 'Successfully claimed ' . $files->count() . ' files.');
    }

    public function markCompleted(File $file)
    {
        // Check if user is authorized to complete this file
        if ($file->claimed_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to complete this file.');
        }

        $file->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Log the activity
        FileActivityLog::create([
            'file_id' => $file->id,
            'user_id' => auth()->id(),
            'action' => 'completed',
            'description' => 'File marked as completed',
        ]);

        // Broadcast the event
        event(new FileStatusChanged(
            $file,
            'in_progress',
            'completed',
            auth()->user()->name
        ));
        $file->order->updateStatusBasedOnFiles();

        return redirect()->back()->with('success', 'File marked as completed.');
    }

    public function show(File $file)
    {
        // Check if user is authorized to view this file
        if ($file->status !== 'unclaimed' && $file->claimed_by !== auth()->id() && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'You are not authorized to view this file.');
        }

        $activityLogs = $file->activityLogs()->with('user')->latest()->get();

        return view('files.show', compact('file', 'activityLogs'));
    }

    public function edit(File $file)
    {
        // Check if user is authorized to edit this file
        if ($file->claimed_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to edit this file.');
        }

        return view('files.edit', compact('file'));
    }

    public function update(Request $request, File $file)
    {
        // Check if user is authorized to update this file
        if ($file->claimed_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to update this file.');
        }

        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,gif,psd,ai',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            Storage::delete('public/' . $file->path);

            // Upload new file
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('orders/' . $file->order_id, 'public');

            $file->update([
                'path' => str_replace('public/', '', $path),
                'mime_type' => $uploadedFile->getMimeType(),
                'size' => $uploadedFile->getSize(),
            ]);

            // Log the activity
            FileActivityLog::create([
                'file_id' => $file->id,
                'user_id' => auth()->id(),
                'action' => 'edited',
                'description' => 'File updated with new version',
            ]);
        }

        return redirect()->route('files.show', $file)
            ->with('success', 'File updated successfully.');
    }

    public function editOnline(File $file)
    {
        // Only claimant or admin may edit
        if ($file->claimed_by !== auth()->id() && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Not authorized.');
        }
        return view('files.edit-online', compact('file'));
    }

    public function saveFromPhotopea(Request $request, File $file)
    {
        // Photopea sends: first ~2000 bytes JSON, then raw image bytes :contentReference[oaicite:4]{index=4}
        $stream = fopen('php://input', 'rb');
        $json   = fread($stream, 2000);
        $meta   = json_decode($json, true);
        $ver    = collect($meta['versions'])->firstWhere('format', 'psd');
        fseek($stream, strlen($json) + $ver['start']);
        $data   = fread($stream, $ver['size']);
        fclose($stream);

        // Overwrite the stored file
        $path = 'orders/' . $file->order_id . '/edited_' . $file->original_name;
        Storage::disk('public')->put($path, $data);
        $file->update(['path' => $path, 'status' => 'in_progress']);

        return response()->json(['message' => 'Saved successfully']);
    }
    public function download(File $file)
    {
        // Only allow admin users
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow download if the file is completed
        if (!$file->isCompleted()) {
            return redirect()->back()->with('error', 'Only completed files can be downloaded.');
        }

        $filePath = storage_path('app/public/' . $file->path);

        // Check if file exists
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $file->original_name);
    }
    public function employeeDownload(File $file)
    {
        return Storage::disk('public')->download($file->path, $file->original_name);
    }
}
