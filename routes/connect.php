<?php

Route::get('/concierge/connect/account/mywellness', 'ConnectController@mywellness');
Route::get('/concierge/connect/account/mywellness/success', 'ConnectController@mywellnessSuccess')->name('connect.account.mywellness.success');
Route::get('/concierge/connect/account/mywellness/error', 'ConnectController@mywellnessError')->name('connect.account.mywellness.error');

Route::get('/concierge/connect/account/fitbit', 'ConnectController@fitbit');
Route::get('/concierge/connect/account/fitbit/success', 'ConnectController@fitbitSuccess')->name('connect.account.fitbit.success');

Route::get('/concierge/connect/account/strava', 'ConnectController@strava');
Route::get('/concierge/connect/account/strava/success', 'ConnectController@stravaSuccess')->name('connect.account.strava.success');
