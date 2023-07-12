<?php

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
    return view('index');
});

Auth::routes();

Route::get("home", function () {
    return redirect(action("OrganizationController@index"));
});

Route::get('app/dashboard', function () {
    return redirect(action("OrganizationController@index"));
})->name('dashboard');

Route::get('app/organizations/{organization}/dashboard', 'OrganizationController@dashboard');
Route::get(
    'app/organizations/{organization}/reset-token',
    'OrganizationController@resetToken'
);
Route::get(
    'app/organizations/{organization}/dashboard/{token}',
    function (\App\Organization $organization, string $token) {

        if ($organization->dashboard_token != $token) {
            abort(403);
        }

        return view("organization.dashboard", array("organization" => $organization));
    }
)->name("organization.public.dashboard");
Route::resource('app/organizations', 'OrganizationController');
Route::resource("app/organizations.user", "OrganizationUserController");
Route::resource('app/servers', 'ServerController');
