<?php
use App\Livewire\Setup\Installer;
use Illuminate\Support\Facades\Route;

Route::get('/install', Installer::class)->name('installer');
