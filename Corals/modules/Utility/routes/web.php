<?php

Route::group(['prefix' => 'utilities'], function () {
    //gallery
    Route::group(['prefix' => 'gallery', 'as' => 'gallery.'], function () {
        Route::get('{hashed_id}', ['as' => 'list', 'uses' => 'Gallery\GalleryController@gallery']);
        Route::post('{hashed_id}/upload', ['as' => 'upload', 'uses' => 'Gallery\GalleryController@galleryUpload']);
        Route::post('{media}/mark-as-featured', ['as' => 'mark-as-featured', 'uses' => 'Gallery\GalleryController@galleryItemFeatured']);
        Route::delete('{media}/delete', ['as' => 'delete', 'uses' => 'Gallery\GalleryController@galleryItemDelete']);
    });


    Route::delete('wishlist/{wishlist}', 'Wishlist\WishlistBaseController@destroy');

    Route::group(['prefix' => 'address'], function () {
        Route::post('locations/bulk-action', 'Address\LocationsController@bulkAction');
        Route::resource('locations', 'Address\LocationsController');
    });
    Route::post('tags/bulk-action', 'Tag\TagsController@bulkAction');
    Route::resource('tags', 'Tag\TagsController', ['except' => ['show']]);

    Route::post('categories/bulk-action', 'Category\CategoriesController@bulkAction');
    Route::post('attributes/bulk-action', 'Category\AttributesController@bulkAction');
    Route::resource('categories', 'Category\CategoriesController', ['except' => ['show']]);
    Route::resource('attributes', 'Category\AttributesController', ['except' => ['show']]);


    Route::post('ratings/bulk-action', 'Rating\RatingBaseController@bulkAction');
    Route::resource('ratings', 'Rating\RatingBaseController', ['except' => ['store', 'show', 'create']]);
    Route::post('ratings/{rating}/{status}', 'Rating\RatingBaseController@toggleStatus');

    Route::post('comments/bulk-action', 'Comment\CommentBaseController@bulkAction');
    Route::resource('comments', 'Comment\CommentBaseController', ['only' => ['index', 'destroy']]);

    //Common routes
    Route::get('categories/attributes/{product_id?}', 'Category\AttributesController@getCategoryAttributes');
    Route::post('newsletter/subscribe/', 'Common\PublicCommonController@subscribeNewsLetter');

    //Invite Friends
    Route::get('invite-friends', 'InviteFriends\InviteFriendsBaseController@getInviteFriendsForm');
    Route::post('invite-friends', 'InviteFriends\InviteFriendsBaseController@sendInvitation');

    Route::resource('seo-items', 'SEO\SEOItemController');

    Route::get('content-consent-settings/modal', 'ContentConsent\ContentConsentController@modal');
    Route::get('content-consent-settings', 'ContentConsent\ContentConsentController@index');
    Route::post('content-consent-settings', 'ContentConsent\ContentConsentController@setSettings');
    Route::get('content-consent-answer/{state}', 'ContentConsent\ContentConsentController@setContentConsentAnswer');
});
