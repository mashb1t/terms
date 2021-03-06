<?php

use App\Models\Quiz;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('quizzes', 'quizzes/list')->name('quizzes');

    Route::group(['prefix' => 'quiz/{quiz}'], function () {
        Route::get('/', function (Quiz $quiz) {
            Gate::authorize('view', $quiz);

            return view('quizzes/view', [
                'quiz' => $quiz,
            ]);
        })->name('quiz.view');

    });

    Route::view('answer', 'answers.view')->name('answer');

    Route::view('questions', 'questions/list')->name('questions');
});
