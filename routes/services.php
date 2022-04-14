<?php

Route::get('/oauth/mywellness', 'MemberDashboardController@saveMemberMyWellnessToken')->name('member.device.mywellness.save');
Route::get('/oauth/fitbit', 'MemberDashboardController@saveMemberFitbitToken')->name('member.device.fitbit.save');
Route::get('/oauth/strava', 'MemberDashboardController@saveMemberStravaToken')->name('member.device.strava.save');

Route::group(['prefix' => 'member', 'middleware' => 'role:root|club_enterprise|club_admin|club_employee|member'], function () {

    Route::group(['prefix' => '{id}/devices', 'middleware' => 'role:club_admin|club_employee|club_enterprise|root'], function () {
        Route::get('/mywellness/oauth', 'MemberDashboardController@redirectToMyWellness')->name('member.device.mywellness.oauth');
        Route::get('/mywellness/revoke', 'MemberDashboardController@revokeMyWellnessAccess')->name('member.device.mywellness.revoke');

        Route::get('/fitbit/oauth', 'MemberDashboardController@redirectToFitbit')->name('member.device.fitbit.oauth');
        Route::get('/fitbit/revoke', 'MemberDashboardController@revokeFitbitAccess')->name('member.device.fitbit.revoke');

        Route::get('/strava/oauth', 'MemberDashboardController@redirectToStrava')->name('member.device.strava.oauth');
        Route::get('/strava/revoke', 'MemberDashboardController@revokeStravaAccess')->name('member.device.strava.revoke');
    });
});
