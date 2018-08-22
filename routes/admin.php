<?php
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 后台公共路由部分
|
*/
Route::get('/','admin\IndexController@layout')->name('admin.layout');
Route::group(['namespace'=>'Admin','prefix'=>'admin'],function (){
    //登录
    Route::get('/login','IndexController@login')->name('admin.login')->middleware('web');
    //审批工作流管理
    Route::resource('/workflows', 'WorkflowController');
    Route::get('/workflow/design', 'WorkflowController@design')->name('workflows.design');
    Route::get('/workflow/designShow', 'WorkflowController@designShow')->name('workflows.designShow');

    //表单设计管理
    Route::resource('/designs','DesignController',['only' => [
        'index','show'
    ]]);

    //节点管理
    Route::resource('/process','ProcessController',['only' => [
        'index', 'store'
    ]]);

    //审批管理
    Route::resource('/approvals','ApprovalController')->middleware(['checkauth','web']);
    Route::get('/approval/lists','ApprovalController@lists')->middleware('checkauth')->name('approvals.lists');

    //待我审批
    Route::resource('/applys','ApplyController',['only' => [
        'index'
    ]])->middleware(['checkauth','web']);
});


