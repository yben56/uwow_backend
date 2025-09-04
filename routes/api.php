<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

# GET:/blogs -> index
# POST: /blogs -> store
# PUT/PATCH: /blogs/{blog} -> update
# DELETE: /blogs/{blog} -> destory
Route::apiResource('blogs', BlogController::class)->only(['index','store','update','destroy']);   

# GET: /blogs/serach -> search
Route::get('blogs/search', [BlogController::class, 'search']);

# PATCH: set active/inactive, ordering
Route::patch('blogs/{blog}/active', [BlogController::class, 'setActive']);
Route::patch('/blogs/{blog}/reorder', [BlogController::class, 'reorder']);