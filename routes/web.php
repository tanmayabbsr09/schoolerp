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
    return view('welcome');
});

/* ROUTES */
Auth::routes();



/* POST/GET */
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/admission', 'StudentController@admission')->name('admission');
Route::post('/admission-save', 'StudentController@admission')->name('admission-save');
Route::get('/admission-list', 'StudentController@admissionList')->name('admission-list');
Route::post('/admission-list', 'StudentController@admissionList')->name('admission-list');
Route::get('/admission/{id}', 'StudentController@admission');
Route::get('/admission-delete/{id}', 'StudentController@admissionDelete');

Route::get('/master-fees', 'MasterformsController@feesCategory')->name('master-fees');
Route::post('/master-fees-save', 'MasterformsController@feesCategory')->name('master-fees-save');
Route::post('/cat-save', 'MasterformsController@feesCategory')->name('cat-save');
Route::post('/subcat-save', 'MasterformsController@feesCategory')->name('subcat-save');

Route::get('/payment/{id}', 'StudentController@feesPayment')->name('payment');
Route::post('/payment-save', 'StudentController@feesPayment')->name('payment-save');

Route::get('/employee', 'MasterformsController@addUser')->name('employee');
Route::post('/employee-save', 'MasterformsController@addUser')->name('employee-save');
Route::get('/employee-list', 'MasterformsController@userList')->name('employee-list');
Route::get('/employee-edit/{id}', 'MasterformsController@addUser')->name('employee-edit');

Route::get('/get-payment-overview/{sid}', 'StudentController@paymentOverview')->name('get-payment-overview');
Route::get('/get-payment-overview-by-year-id/{sid}/{year}', 'StudentController@paymentOverview')->name('get-payment-overview');

Route::get('/event', 'MasterformsController@eventManagement')->name('event');
Route::post('/event-save', 'MasterformsController@eventManagement')->name('event-save');
Route::get('/event-list', 'MasterformsController@eventListing')->name('employee-list');
Route::get('/event-edit/{id}', 'MasterformsController@eventManagement')->name('event-edit');
Route::get('/event-delete/{id}', 'MasterformsController@trashEvents')->name('event-delete');

Route::get('/library', 'MasterformsController@libraryManagement')->name('library');
Route::post('/library-save', 'MasterformsController@libraryManagement')->name('library-save');
Route::get('/library-list', 'MasterformsController@libraryListing')->name('employee-list');
Route::get('/library-edit/{id}', 'MasterformsController@libraryManagement')->name('library-edit');
Route::get('/library-delete/{id}', 'MasterformsController@trashEvents')->name('library-delete');

Route::get('/book-issue', 'BookIssueController@bookIssue')->name('book-issue');
Route::post('/book-issue-save', 'BookIssueController@bookIssue')->name('book-issue-save');
Route::get('/book-issue-list', 'BookIssueController@bookissueListing')->name('book-issue-list');
Route::get('/library-invoice/{id}', 'BookIssueController@bookissueListing')->name('library-invoice');
Route::post('/library-return-date-save', 'BookIssueController@saveReturnDate')->name('library-return-date-save');


Route::get('/voucher-issue', 'MasterformsController@voucherManagement')->name('voucher-issue');
Route::post('/voucher-save', 'MasterformsController@voucherManagement')->name('voucher-save');
Route::get('/voucher-list', 'MasterformsController@voucherListing')->name('voucher-list');
/*Route::get('/library-invoice/{id}', 'BookIssueController@bookissueListing')->name('library-invoice');
Route::post('/library-return-date-save', 'BookIssueController@saveReturnDate')->name('library-return-date-save');*/

Route::get('/cron-inouts', 'CronController@calculateInout')->name('voucher-list');
Route::get('/money-details', 'CronController@moneyflowDetails');

Route::get('/hostel-room', 'HostelController@hostelRoomAdd')->name('hostel-room');
Route::post('/hostel-room-save', 'HostelController@hostelRoomAdd')->name('hostel-room-save');
Route::get('/hostel-allot', 'HostelController@allotHostel')->name('hostel-allot');
Route::post('/hostel-allot-save', 'HostelController@allotHostel')->name('hostel-allot-save');
Route::get('/hostel-allot-list', 'HostelController@hostelAllotLists')->name('hostel-allot-list');
/*Route::get('/book-issue-list', 'BookIssueController@bookissueListing')->name('book-issue-list');
Route::get('/library-invoice/{id}', 'BookIssueController@bookissueListing')->name('library-invoice');*/



/*Route::get('/library-edit/{id}', 'BookIssueController@libraryManagement')->name('library-edit');
Route::get('/library-delete/{id}', 'BookIssueController@trashEvents')->name('library-delete');
*/
/* AJAX */
Route::post('/ajax-get-subcategories', 'MasterformsController@getSubcategories');
Route::post('/ajax-get-paid-details', 'StudentController@ajaxGetPaidDetails');
Route::post('/get-student-list-by-class-id', 'AjaxController@ajaxGetStudentListByStudentId');
Route::post('/get-book-list-by-class-id', 'AjaxController@ajaxGetBookListByClassId');
Route::post('/validate-hostel-roomno', 'HostelController@validateHostelRoomNo');
Route::post('/get-rooms-by-hostelid', 'HostelController@ajaxGetRoomsByHostelId');