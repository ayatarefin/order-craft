<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function show(Folder $folder)
    {
        $subfolders = $folder->children;
        
        // Filter files based on user role and claim status
        $filesQuery = $folder->files();
        
        if (!auth()->user()->isAdmin()) {
            $filesQuery->where(function($query) {
                $query->where('status', 'unclaimed')
                      ->orWhere('claimed_by', auth()->id());
            });
        }
        
        $files = $filesQuery->paginate(20);
        
        return view('folders.show', compact('folder', 'subfolders', 'files'));
    }
}