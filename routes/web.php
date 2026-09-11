<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/login', 'pages::auth.login')->middleware('guest')->name('login');
Route::livewire('/logout', 'logout')->name('logout');

Route::middleware(['auth'])->group(function(){
    Route::livewire('/', 'pages::dashboard')->middleware('permission:Dashboard-access')->name('dashboard');

    // permission routes
    // Route::livewire('/permission', 'pages::permission.list')->middleware('permission:Permission-list')->name('list-permission');
    // Route::livewire('/permission/create', 'pages::permission.create')->middleware('permission:Permission-create')->name('create-permission');
    // Route::livewire('/permission/{id}/edit', 'pages::permission.edit')->middleware('permission:Permission-edit')->name('edit-permission');

    // roles routes
    Route::livewire('/roles', 'pages::role.list')->middleware('permission:Role-list')->name('list-role');
    Route::livewire('/roles/create', 'pages::role.create')->middleware('permission:Role-create')->name('create-role');
    Route::livewire('/roles/{id}/edit', 'pages::role.edit')->middleware('permission:Role-edit')->name('edit-role');

    // user routes
    Route::livewire('/users', 'pages::user.list')->middleware('permission:User-list')->name('list-user');
    Route::livewire('/user/create', 'pages::user.create')->middleware('permission:User-create')->name('create-user');

    // customer routes
    Route::livewire('/customer', 'pages::customer.list-customer')->middleware('permission:Customer-list')->name('list-customer');
    Route::livewire('/customer/create', 'pages::customer.create-customer')->middleware('permission:Customer-create')->name('create-customer');
    Route::livewire('/customer/{id}/edit', 'pages::customer.edit-customer')->middleware('permission:Customer-edit')->name('edit-customer');

    // process_sequence routes
    Route::livewire('/process_sequence', 'pages::process_sequence.list-process_sequence')->middleware('permission:ProcessSequence-list')->name('list-process_sequence');
    Route::livewire('/process_sequence/create', 'pages::process_sequence.create-process_sequence')->middleware('permission:ProcessSequence-create')->name('create-process_sequence');
    Route::livewire('/process_sequence/{id}/edit', 'pages::process_sequence.edit-process_sequence')->middleware('permission:ProcessSequence-edit')->name('edit-process_sequence');
    // Route::livewire('/process_sequence/insert', 'pages::process_sequence.insert-process_sequence')->middleware('permission:ProcessSequence-insert')->name('insert-process_sequence');

    // order routes
    Route::livewire('/order', 'pages::order.list-order')->middleware('permission:Order-list')->name('list-order');
    Route::livewire('/order/create', 'pages::order.create-order')->middleware('permission:Order-create')->name('create-order');
    Route::livewire('/order/{id}/edit', 'pages::order.edit-order')->middleware('permission:Order-edit')->name('edit-order');

    // process sequence order routes
    Route::livewire('/process_sequence_order', 'pages::process_sequence_order.list')->middleware('permission:ProcessSequenceOrder-list')->name('list-ProcessSequenceOrder');
    Route::livewire('/process_sequence_order/{orderId}/edit', 'pages::process_sequence_order.edit')->middleware('permission:ProcessSequenceOrder-edit')->name('edit-ProcessSequenceOrder');

    // progress order routes
    Route::livewire('/progress_order', 'pages::progress_order.list')->middleware('permission:ProgressOrder-list')->name('list-ProgressOrder');
    Route::livewire('/progress_order/create', 'pages::progress_order.create')->middleware('permission:ProgressOrder-create')->name('create-ProgressOrder');
    Route::livewire('/progress_order/{id}/edit', 'pages::progress_order.edit')->middleware('permission:ProgressOrder-edit')->name('edit-ProgressOrder');

});
