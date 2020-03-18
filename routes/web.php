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

Route::group(['middleware' => 'auth'], function(){
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);
    Route::get('/in_progress', ['as' => 'inProgress', 'uses' => 'HomeController@inProgress']);

    Route::post('/address/list', ['as' => 'address.list.post', 'uses' => 'AddressController@getAddressList']);
    Route::post('/address/add', ['as' => 'address.add.post', 'uses' => 'AddressController@addAddress']);

    //Logout
    Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);

    Route::group(['middleware' => 'role:administrator'], function(){
        // Monitoring
        Route::get('/monitoring/requests', ['as' => 'monitoring.requests', 'uses' => 'MonitoringController@getRequests']);
        Route::get('/monitoring/meters', ['as' => 'monitoring.meters', 'uses' => 'MonitoringController@getMeters']);
        Route::get('/monitoring/missed-points/{id}', ['as' => 'monitoring.missed.points', 'uses' => 'MonitoringController@getMissedPoints'])
            ->where(['id' => '[0-9]+']);

        // Employers
        Route::get('/employer/create', ['as' => 'employer.create', 'uses' => 'EmployerController@getCreate']);
        Route::post('/employer/create', ['as' => 'employer.create.post', 'uses' => 'EmployerController@postCreate']);
        Route::get('/employer/list', ['as' => 'employer.list', 'uses' => 'EmployerController@getList']);
        Route::get('/employer/edit/{id}', ['as' => 'employer.edit', 'uses' => 'EmployerController@getEdit'])
            ->where(['id' => '[0-9]+']);
        Route::post('/employer/edit', ['as' => 'employer.edit.post', 'uses' => 'EmployerController@postEdit']);
        Route::get('/employer/delete/{id}', ['as' => 'employer.delete', 'uses' => 'EmployerController@getDelete'])
            ->where(['id' => '[0-9]+']);

        //Clients
        Route::post('/client/deletePermanently', ['as' => 'client.delete.permanently.post', 'uses' => 'ClientController@postDeletePermanently']);
        Route::get('/client/recover/{id}', ['as' => 'client.recover', 'uses' => 'ClientController@getRecover'])
            ->where(['id' => '[0-9]+']);

        //Notifications
        Route::get('/notifications/send', ['as' => 'notifications.send', 'uses' => 'NotificationController@getSend']);
        Route::post('/notifications/send', ['as' => 'notifications.send.post', 'uses' => 'NotificationController@postSend']);
    });

    Route::group(['middleware' => 'role:administrator.manager'], function(){
        //Clients
        Route::get('/client/create', ['as' => 'client.create', 'uses' => 'ClientController@getCreate']);
        Route::post('/client/create', ['as' => 'client.create.post', 'uses' => 'ClientController@postCreate']);
        Route::post('/client/delete', ['as' => 'client.delete.post', 'uses' => 'ClientController@postDelete']);
        Route::get('/client/list', ['as' => 'client.list', 'uses' => 'ClientController@getList']);
        Route::get('/client/view/{id}', ['as' => 'client.view', 'uses' => 'ClientController@getView'])
            ->where(['id' => '[0-9]+']);
        Route::get('/client/edit/{id}', ['as' => 'client.edit', 'uses' => 'ClientController@getEdit'])
            ->where(['id' => '[0-9]+']);
        Route::post('/client/postEdit', ['as' => 'client.edit.post', 'uses' => 'ClientController@postEdit']);
        Route::get('/other_meters/total_value', ['as' => 'ometers.total_value', 'uses' => 'OtherMeterController@getTotalValue']);
        Route::post('/other_meters/total_value', ['as' => 'ometers.total_value.post', 'uses' => 'OtherMeterController@postTotalValue']);
        Route::get('/other_meters/add_value/{user_id}', ['as' => 'ometers.add_value', 'uses' => 'OtherMeterController@getUserValue'])
            ->where(['id' => '[0-9]+']);
        Route::post('/other_meters/add_value', ['as' => 'ometers.add_value.post', 'uses' => 'OtherMeterController@postUserValue']);

        // Add Task
        Route::get('/task/create', ['as' => 'task.create', 'uses' => 'TaskController@getCreate']);
        Route::post('/task/create', ['as' => 'task.create.post', 'uses' => 'TaskController@postCreate']);
        Route::get('/task/getDelete/{id}', ['as' => 'task.delete', 'uses' => 'TaskController@getDelete'])
            ->where(['id' => '[0-9]+']);

        //Employers List
        Route::post('/employers/list', ['as' => 'employers.list.post', 'uses' => 'EmployerController@getEmployers']);

        // Rates
        Route::get('/rate/create', ['as' => 'rate.create', 'uses' => 'RateController@getCreate']);
        Route::post('/rate/create', ['as' => 'rate.create.post', 'uses' => 'RateController@postCreate']);
        Route::get('/rate/list', ['as' => 'rate.list', 'uses' => 'RateController@getList']);
        Route::post('/rate/delete', ['as' => 'rate.delete.post', 'uses' => 'RateController@postDelete']);

        // Reports
        Route::get('/report/general', ['as' => 'report.general', 'uses' => 'Reports\GeneralController@getReport']);
        Route::get('/report/consumption', ['as' => 'report.consumption', 'uses' => 'Reports\ConsumptionController@getReport']);
        Route::get('/report/staff', ['as' => 'report.staff', 'uses' => 'Reports\StaffController@getReport']);
        Route::get('/report/tatenergo', ['as' => 'report.tatenergo', 'uses' => 'Reports\GeneralController@getTatenergo']);
        Route::get('/report/other-meters', ['as' => 'report.other_meters', 'uses' => 'Reports\OtherMetersController@getReport']);
        Route::get('/report/askue-xml', ['as' => 'report.askue_xml', 'uses' => 'Reports\ASKUEXmlController@getXml']);
    });

    Route::group(['middleware' => 'role:administrator.technician'], function(){
        // Technician
        Route::get('/meters/add-control', ['as' => 'meters.add.control', 'uses' => 'MeterController@getAddControlMeter']);
        Route::post('/meters/add-control', ['as' => 'meters.add.control.post', 'uses' => 'MeterController@postAddControlMeter']);
        Route::get('/meters/structure/{id?}/{move?}', ['as' => 'meters.structure', 'uses' => 'MeterController@getStructure'])
            ->where(['id' => '[0-9]+', 'move' => '[0-9]+']);
        Route::get('/meters/registration/{id}', ['as' => 'meters.registration.id', 'uses' => 'MeterController@getMeterRegistration'])
            ->where(['id' => '[0-9]+']);
        Route::get('/meters/registration', ['as' => 'meters.registration', 'uses' => 'MeterController@getMetersForRegistration']);
        Route::post('/meters/registration', ['as' => 'meters.registration.post', 'uses' => 'MeterController@postMeterRegistration']);
        Route::get('/meters/removeFromDeferred/{id}', ['as' => 'meter.removeFromDeferred', 'uses' => 'MeterController@getRemoveFromDeferred'])
            ->where(['id' => '[0-9]+']);
        Route::get('/meters/resetParent/{id}', ['as' => 'meter.resetParent', 'uses' => 'MeterController@getResetParent'])
            ->where(['id' => '[0-9]+']);
        Route::post('/meters/move', ['as' => 'meter.move', 'uses' => 'MeterController@postMoveMeter']);
        Route::get('/meter/select-childs', ['as' => 'meter.select.childs', 'uses' => 'SelectChildsController@getMeters']);
    });

    Route::group(['middleware' => 'role:administrator.technician.manager'], function(){
        Route::get('/meters/history/{id}', ['as' => 'meter.history', 'uses' => 'MeterController@getMeterHistory'])
            ->where(['id' => '[0-9]+']);

        // Tasks
        Route::get('/tasks/{type?}', ['as' => 'tasks.list', 'uses' => 'TaskController@getList'])
            ->where('name', '[A-Za-z]+');
        Route::get('/task/setComplete/{id}', ['as' => 'task.setComplete', 'uses' => 'TaskController@setComplete'])
            ->where(['id' => '[0-9]+']);

        // Loss report
        Route::get('/report/loss/{id?}', ['as' => 'report.loss', 'uses' => 'Reports\LossController@getReport'])
            ->where(['id' => '[0-9]+']);
        Route::get('/report/loss_childs/{id?}', ['as' => 'report.loss.childs', 'uses' => 'Reports\LossController@getChildMeters'])
            ->where(['id' => '[0-9]+']);
        Route::get('/report/loss_points/{id?}', ['as' => 'report.loss.points', 'uses' => 'Reports\LossController@getPoints'])
            ->where(['id' => '[0-9]+']);
    });

    Route::group(['middleware' => 'role:user'], function(){
        // User
        Route::get('/user/info', ['as' => 'user.info', 'uses' => 'UserController@getInfo']);
        Route::get('/user/other-meters', ['as' => 'user.other_meters', 'uses' => 'UserController@getOther_meters']);
        Route::post('/user/other-meters', ['as' => 'user.other_meters.post', 'uses' => 'UserController@postOther_meters']);
    });

    Route::group(['middleware' => 'role:tozelesh'], function(){
        // Tozelesh
        Route::get('/map', ['as' => 'tozelesh.map', 'uses' => 'Tozelesh\MapController@getMap']);
        Route::get('/cancel_creation/{id}', ['as' => 'tozelesh.cancel.creation', 'uses' => 'Tozelesh\MapController@getCancelObjectCreation'])
            ->where(['id' => '[0-9]+']);
        Route::get('/report/{id?}', ['as' => 'report.tozelesh', 'uses' => 'Reports\TozeleshController@getReport'])
            ->where(['id' => '[0-9]+']);;
        Route::get('/meter_registration', ['as' => 'tozelesh.create.meter', 'uses' => 'Tozelesh\MeterRegistrationController@getMeterRegistration']);

        Route::post('/meter_registration', ['as' => 'tozelesh.create.meter.post', 'uses' => 'Tozelesh\MeterRegistrationController@postMeterRegistration']);
        Route::post('/save_lamps', ['as' => 'tozelesh.save.lamps', 'uses' => 'Tozelesh\MapController@postSaveLamps']);
        Route::post('/save_object', ['as' => 'tozelesh.save.object', 'uses' => 'Tozelesh\MapController@postSaveObject']);
        Route::post('/edit_object', ['as' => 'tozelesh.edit.object', 'uses' => 'Tozelesh\MapController@postEditObject']);
        Route::post('/delete_object', ['as' => 'tozelesh.delete.object', 'uses' => 'Tozelesh\MapController@postDeleteObject']);
    });

    Route::group(['middleware' => 'role:administrator.technician.tozelesh'], function(){
        Route::get('/meters/registration_progress/{id}', ['as' => 'meters.registration.progress', 'uses' => 'MeterController@getRegistrationProgress'])
            ->where(['id' => '[0-9]+']);

        Route::get('/meter_instruction/set/{id}/{set}', ['as' => 'meter.instruction.set', 'uses' => 'MeterInstructionController@getSetStatus'])
            ->where(['id' => '[0-9]+', 'set' => '[0-1]']);
        Route::get('/meter_instruction/refresh/{id}', ['as' => 'meters.instruction.refresh', 'uses' => 'MeterInstructionController@getRefreshValue'])
            ->where(['id' => '[0-9]+']);
        Route::get('/meter_instruction/check/{id}', ['as' => 'meters.instruction.check', 'uses' => 'MeterInstructionController@getCheckInstruction'])
            ->where(['id' => '[0-9]+']);

        Route::get('/meters/edit/{id}', ['as' => 'meter.edit', 'uses' => 'MeterController@getMeterEdit'])
            ->where(['id' => '[0-9]+']);
        Route::post('/meters/edit', ['as' => 'meter.edit.post', 'uses' => 'MeterController@postMeterEdit']);
    });

    Route::group(['middleware' => 'role:user.administrator'], function(){
        Route::get('/notifications/list', ['as' => 'user.notifications', 'uses' => 'NotificationController@getList']);
    });
});

Route::group(['middleware' => 'guest'], function(){
    // Login
    Route::get('/login', ['as' => 'login.form', 'uses' => 'Auth\LoginController@showLoginForm']);
    Route::post('/login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);

    // Password reset
    Route::post('/password/email', ['as' => 'password.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
    Route::get('/password/reset', ['as' => 'password.request', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
    Route::post('/password/reset', ['as' => 'password.reset', 'uses' => 'Auth\ResetPasswordController@reset']);
    Route::get('/password/reset/{token}', ['as' => 'password.reset.form', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
});

