<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/


/**
 * Protected Api routes
 */

Route::group(['middleware' => ['oauth'],'prefix'=>'api/1.0'], function () {

    Route::put('users/{id}', [
        'uses'  => 'UsersController@update',
        'as'    => 'users_update_path'
    ])->where('id','[0-9]+');

    Route::delete('users/{id}', [
        'uses'  => 'UsersController@destroy',
        'as'    => 'users_delete_path'
    ])->where('id','[0-9]+');

    Route::get('users/{id}', [
        'uses'  => 'UsersController@show',
        'as'    => 'users_show_path'
    ])->where('id','[0-9]+');

    Route::get('users', [
        'uses'  => 'UsersController@index',
        'as'    => 'users_index_path'
    ]);

    /**
     * Pets oauth
     */
    Route::delete('pets/{id}', [
        'uses'  => 'PetsController@destroy',
        'as'    => 'pets_delete_path'
    ])->where('id','[0-9]+');

    Route::post('pets/{id}', [
        'uses'  => 'PetsController@update',
        'as'    => 'pets_update_path'
    ])->where('id','[0-9]+');

    Route::post('pets', [
        'uses'  => 'PetsController@store',
        'as'    => 'pets_store_path'
    ]);

    /**
     * Publications
     */
    Route::post('publications', [
        'uses'  => 'PublicationsController@store',
        'as'    => 'publications_store_path'
    ]);

    Route::post('publications/{id}', [
        'uses'  => 'PublicationsController@update',
        'as'    => 'publications_update_path'
    ])->where('id','[0-9]+');

    Route::delete('publications/{id}', [
        'uses'  => 'PublicationsController@destroy',
        'as'    => 'publications_delete_path'
    ])->where('id','[0-9]+');

    /**
     * Sites
     */
    Route::post('sites', [
        'uses'  => 'SitesController@store',
        'as'    => 'sites_store_path'
    ]);

    Route::post('sites/{id}', [
        'uses'  => 'SitesController@update',
        'as'    => 'sites_update_path'
    ])->where('id','[0-9]+');

    Route::delete('sites/{id}', [
        'uses'  => 'SitesController@destroy',
        'as'    => 'sites_delete_path'
    ])->where('id','[0-9]+');

    /**
     * Site Comments
     */
    Route::post('sitecomments', [
        'uses'  => 'SiteCommentsController@store',
        'as'    => 'site_comments_store_path'
    ]);

    Route::post('sitecomments/{id}', [
        'uses'  => 'SiteCommentsController@update',
        'as'    => 'site_comments_update_path'
    ])->where('id','[0-9]+');

    Route::delete('sitecomments/{id}', [
        'uses'  => 'SiteCommentsController@destroy',
        'as'    => 'site_comments_delete_path'
    ])->where('id','[0-9]+');

    /**
     * Address
     */
    Route::put('address/{id}', [
        'uses'  => 'AddressesController@update',
        'as'    => 'addresses_update_path'
    ])->where('id','[0-9]+');

    Route::post('address', [
        'uses'  => 'AddressesController@store',
        'as'    => 'addresses_store_path'
    ]);

    Route::delete('address/{id}', [
        'uses'  => 'AddressesController@destroy',
        'as'    => 'addresses_delete_path'
    ])->where('id','[0-9]+');
});


/**
 * Public api routes
 */
Route::group(['prefix'=>'api/1.0'], function () {

    /**
     * Oauth2 and User create routes
     */
    Route::post('oauth/access_token', function() {
        return Response::json(Authorizer::issueAccessToken());
    });

    Route::post('users', [
        'uses'  => 'UsersController@create',
        'as'    => 'users_create_path'
    ]);

    /**
     * Pets routes
     */
    Route::get('pets/{id}', [
        'uses'  => 'PetsController@show',
        'as'    => 'c'
    ])->where('id','[0-9]+');

    Route::get('pets', [
        'uses'  => 'PetsController@index',
        'as'    => 'pets_index_path'
    ]);

    /**
     * Partners routes
     */
    Route::get('partners/{id}', [
        'uses'  => 'PartnersController@show',
        'as'    => 'partners_show_path'
    ])->where('id','[0-9]+');

    Route::get('partners', [
        'uses'  => 'PartnersController@index',
        'as'    => 'partners_index_path'
    ]);

    /**
     * Publications routes
     */
    Route::get('publications', [
        'uses'  => 'PublicationsController@index',
        'as'    => 'publications_index_path'
    ]);

    Route::get('publications/{id}', [
        'uses'  => 'PublicationsController@show',
        'as'    => 'publications_show_path'
    ])->where('id','[0-9]+');


    /**
     * Sites Routes
     */
    Route::get('sites', [
        'uses'  => 'SitesController@index',
        'as'    => 'sites_index_path'
    ]);

    Route::get('sites/{id}', [
        'uses'  => 'SitesController@show',
        'as'    => 'sites_show_path'
    ])->where('id','[0-9]+');

    /**
     * SiteComments Routes
     */
    Route::get('sitecomments', [
        'uses'  => 'SiteCommentsController@index',
        'as'    => 'site_comments_index_path'
    ]);

    Route::get('sitecomments/{id}', [
        'uses'  => 'SiteCommentsController@show',
        'as'    => 'site_comments_show_path'
    ])->where('id','[0-9]+');

    /**
     * PetRaces Routes
     */
    Route::get('petraces/{id}', [
        'uses'  => 'PetRacesController@index',
        'as'    => 'pet_races_index_path'
    ])->where('id','[0-9]+');

    /**
     * PetTypes Routes
     */
    Route::get('pettypes', [
        'uses'  => 'PetTypesController@index',
        'as'    => 'pet_types_index_path'
    ]);

    /**
     * SiteCategory Routes
     */
    Route::get('sitecategory', [
        'uses'  => 'SiteCategoryController@index',
        'as'    => 'site_category_index_path'
    ]);

    /**
     * SiteTypes Routes
     */
    Route::get('sitetypes', [
        'uses'  => 'SiteTypesController@index',
        'as'    => 'site_types_index_path'
    ]);

    /**
     * PublicationCategory Routes
     */
    Route::get('publicationcategories', [
        'uses'  => 'PublicationCategoriesController@index',
        'as'    => 'publication_categories_index_path'
    ]);

    /**
     * PublicationTypes Routes
     */
    Route::get('publicationtypes', [
        'uses'  => 'PublicationTypesController@index',
        'as'    => 'publication_types_index_path'
    ]);

    /**
     * PublicationStatuses Routes
     */
    Route::get('publicationstatuses', [
        'uses'  => 'PublicationStatusesController@index',
        'as'    => 'publication_types_index_path'
    ]);

    /**
     * Locations
     */

    Route::get('locations', [
        'uses'  => 'LocationsController@index',
        'as'    => 'locations_index_path'
    ]);

    /**
     * Address
     */

    Route::get('address', [
        'uses'  => 'AddressesController@index',
        'as'    => 'address_index_path'
    ]);

    Route::get('address/{id}', [
        'uses'  => 'AddressesController@show',
        'as'    => 'address_show_path'
    ])->where('id','[0-9]+');
    
    /**
     * Upload route example
     */
    Route::post('upload', [
        'uses'  => 'UploadController@store',
        'as'    => 'upload_store_path'
    ]);

});
