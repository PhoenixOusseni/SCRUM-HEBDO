<?php

use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\SemaineController;
use App\Http\Controllers\StatistiqueController;
use Illuminate\Support\Facades\Route;

// ── Authentification ────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Mot de passe
Route::get('/password/modifier', [AuthController::class, 'showChangePassword'])->name('password.edit');
Route::put('/password/modifier', [AuthController::class, 'changePassword'])->name('password.change');

// Page d'accueil → canevas de la semaine courante
Route::get('/', [SemaineController::class, 'redirectCourante'])->name('home');

// Semaines (Canevas)
Route::get('/semaines', [SemaineController::class, 'index'])->name('semaines.index');
Route::post('/semaines', [SemaineController::class, 'store'])->name('semaines.store');
Route::get('/semaines/{semaine}', [SemaineController::class, 'show'])->name('semaines.show');
Route::post('/semaines/{semaine}/creer-suivante', [SemaineController::class, 'creerSuivante'])->name('semaines.creerSuivante');
Route::patch('/semaines/{semaine}/obstacles', [SemaineController::class, 'updateObstacles'])->name('semaines.obstacles');

// Activités
Route::post('/activites', [ActiviteController::class, 'store'])->name('activites.store');
Route::put('/activites/{activite}', [ActiviteController::class, 'update'])->name('activites.update');
Route::patch('/activites/{activite}/statut', [ActiviteController::class, 'updateStatut'])->name('activites.statut');
Route::delete('/activites/{activite}', [ActiviteController::class, 'destroy'])->name('activites.destroy');

// Statistiques
Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

// Employés
Route::get('/employes', [EmployeController::class, 'index'])->name('employes.index');
Route::get('/employes/creer', [EmployeController::class, 'create'])->name('employes.create');
Route::post('/employes', [EmployeController::class, 'store'])->name('employes.store');
Route::get('/employes/{employe}/modifier', [EmployeController::class, 'edit'])->name('employes.edit');
Route::put('/employes/{employe}', [EmployeController::class, 'update'])->name('employes.update');
Route::delete('/employes/{employe}', [EmployeController::class, 'destroy'])->name('employes.destroy');
