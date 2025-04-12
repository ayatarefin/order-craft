<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Order;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'files' => 'required|file|mimes:zip',
        ]);

        // Create order
        $order = Order::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        // Process uploaded ZIP file
        if ($request->hasFile('files')) {
            $zipFile = $request->file('files');
            $tempPath = storage_path('app/temp') . '/' . Str::random(16);
            
            // Extract ZIP to temporary directory
            $zip = new ZipArchive;
            if ($zip->open($zipFile->path()) === TRUE) {
                $zip->extractTo($tempPath);
                $zip->close();
                
                // Create folder structure based on ZIP content
                $this->processFolder($tempPath, null, $order);
                
                // Clean up temp directory
                Storage::deleteDirectory('temp');
            }
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    private function processFolder($path, $parentFolderId, $order)
    {
        $dirName = basename($path);
        
        // Create folder in database if it's not the root temp folder
        $folder = null;
        if (strpos($path, 'temp/') !== false && $dirName !== basename(storage_path('app/temp'))) {
            $folder = Folder::create([
                'name' => $dirName,
                'path' => str_replace(storage_path('app/temp'), '', $path),
                'order_id' => $order->id,
                'parent_id' => $parentFolderId,
            ]);
        }
        
        $folderId = $folder ? $folder->id : $parentFolderId;
        
        // Process all files in this directory
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $filePath = $path . '/' . $file;
            
            if (is_dir($filePath)) {
                // Recursively process subdirectories
                $this->processFolder($filePath, $folderId, $order);
            } else {
                // Process files
                $mime = mime_content_type($filePath);
                // Only process image files
                if (strpos($mime, 'image/') === 0) {
                    $fileSize = filesize($filePath);
                    $fileName = Str::random(40) . '.' . pathinfo($file, PATHINFO_EXTENSION);
                    $relativePath = 'orders/' . $order->id . '/' . $fileName;
                    
                    // Move file to storage
                    Storage::put('public/' . $relativePath, file_get_contents($filePath));
                    
                    // Create file record in database
                    File::create([
                        'name' => $fileName,
                        'original_name' => $file,
                        'path' => $relativePath,
                        'mime_type' => $mime,
                        'size' => $fileSize,
                        'order_id' => $order->id,
                        'folder_id' => $folderId,
                        'status' => 'unclaimed',
                    ]);
                }
            }
        }
    }

    public function show(Order $order)
    {
        $rootFolders = $order->rootFolders;
        $unclaimedFiles = $order->files()->where('status', 'unclaimed')->count();
        $inProgressFiles = $order->files()->where('status', 'in_progress')->count();
        $completedFiles = $order->files()->where('status', 'completed')->count();
        $totalFiles = $order->files()->count();
        
        $employeeProgress = User::whereHas('claimedFiles', function($query) use ($order) {
            $query->where('order_id', $order->id);
        })->withCount([
            'claimedFiles as claimed_count' => function($query) use ($order) {
                $query->where('order_id', $order->id);
            },
            'claimedFiles as completed_count' => function($query) use ($order) {
                $query->where('order_id', $order->id)->where('status', 'completed');
            }
        ])->get();
        
        return view('orders.show', compact(
            'order', 
            'rootFolders', 
            'unclaimedFiles', 
            'inProgressFiles', 
            'completedFiles', 
            'totalFiles',
            'employeeProgress'
        ));
    }

    public function downloadCompleted(Order $order)
    {
        // Create a ZIP with the completed files in their original structure
        $zipName = 'order_' . $order->id . '_completed.zip';
        $zipPath = storage_path('app/temp/' . $zipName);
        
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $completedFiles = $order->files()->where('status', 'completed')->get();
            
            foreach ($completedFiles as $file) {
                $filePath = storage_path('app/public/' . $file->path);
                
                // Get relative path based on folder structure
                $relativePath = '';
                $currentFolder = $file->folder;
                
                while ($currentFolder) {
                    $relativePath = $currentFolder->name . '/' . $relativePath;
                    $currentFolder = $currentFolder->parent;
                }
                
                $zip->addFile($filePath, $relativePath . $file->original_name);
            }
            
            $zip->close();
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }
        
        return redirect()->back()->with('error', 'Could not create ZIP file.');
    }
}
