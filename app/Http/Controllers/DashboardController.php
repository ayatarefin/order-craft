<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            // Admin dashboard
            $pendingOrders = Order::where('status', 'pending')->count();
            $inProgressOrders = Order::where('status', 'in_progress')->count();
            $completedOrders = Order::where('status', 'completed')->count();
            
            $totalFiles = File::count();
            $unclaimedFiles = File::where('status', 'unclaimed')->count();
            $inProgressFiles = File::where('status', 'in_progress')->count();
            $completedFiles = File::where('status', 'completed')->count();
            
            $employeeStats = User::where('role', '!=', 'admin')
                ->withCount([
                    'claimedFiles',
                    'claimedFiles as completed_files_count' => function($query) {
                        $query->where('status', 'completed');
                    }
                ])
                ->get();
            
            $recentOrders = Order::latest()->limit(5)->get();
            
            return view('dashboard.admin', compact(
                'pendingOrders',
                'inProgressOrders',
                'completedOrders',
                'totalFiles',
                'unclaimedFiles',
                'inProgressFiles',
                'completedFiles',
                'employeeStats',
                'recentOrders'
            ));
        } else {
            // Employee dashboard
            $claimedFiles = $user->claimedFiles()
                ->where('status', 'in_progress')
                ->with('order')
                ->latest()
                ->paginate(10);
            
            $completedFiles = $user->claimedFiles()
                ->where('status', 'completed')
                ->count();
            
            $availableOrders = Order::whereHas('files', function($query) {
                $query->where('status', 'unclaimed');
            })->get();
            
            return view('dashboard.employee', compact(
                'claimedFiles',
                'completedFiles',
                'availableOrders'
            ));
        }
    }
}