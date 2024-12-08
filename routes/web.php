<?php

use App\Http\Controllers\DataMigrationController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollPaymentController;
use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => ['auth']], function () {

    Route::get('/',  [App\Http\Controllers\ReportController::class, 'dashboard']);
    Route::get('/users', [App\Http\Controllers\HomeController::class, 'users']);

    Route::resource('/patient', \App\Http\Controllers\PatientController::class);
    Route::resource('/doctor', \App\Http\Controllers\DoctorController::class);
    Route::resource('/medicine_name', \App\Http\Controllers\MedicineNameController::class);
    Route::resource('/pharmacy', \App\Http\Controllers\PharmacyController::class);
    Route::resource('/patient_medicine', \App\Http\Controllers\PatientMedicineController::class);
    Route::resource('/floor', \App\Http\Controllers\FloorController::class);
    Route::resource('/patient_ipd', \App\Http\Controllers\PatientIPDController::class);
    Route::resource('/patient_lab', \App\Http\Controllers\PatientLabController::class);
    Route::resource('/lab_department', \App\Http\Controllers\LabDepartmentController::class);
    Route::resource('/patient_pharmacy_medicine', \App\Http\Controllers\PatientPharmacyMedicineController::class);
    Route::resource('/supplier', \App\Http\Controllers\SupplierController::class);
    Route::resource('/main_department', \App\Http\Controllers\MainLabDepartmentController::class);
    Route::resource('/holiday', \App\Http\Controllers\HolidayController::class);
    Route::resource('/attendance', \App\Http\Controllers\AttendanceController::class);
    Route::resource('/laboratory_patient_lab', \App\Http\Controllers\LaboratoryPatientLabController::class);
    Route::resource('/employee', \App\Http\Controllers\EmployeeController::class);

    Route::post('/attendance_report', [App\Http\Controllers\AttendanceController::class, 'attendance_report'])->name('attendance.report');
    Route::post('/saveJustifyReason', [App\Http\Controllers\AttendanceController::class, 'saveJustifyReason']);
    Route::get('/approveAttendance', [App\Http\Controllers\AttendanceController::class, 'approveAttendance']);

    Route::get('/patient_reception_medicine', [App\Http\Controllers\PatientPharmacyMedicineController::class, 'patient_reception_medicine']);
    Route::get('/reception_patient_labs', [App\Http\Controllers\LaboratoryPatientLabController::class, 'reception_patient_labs']);
    Route::get('complete_medicine/{id}', [App\Http\Controllers\PatientPharmacyMedicineController::class, 'complete_medicine']);
    Route::get('uncomplete_medicine/{id}', [App\Http\Controllers\PatientPharmacyMedicineController::class, 'uncomplete_medicine']);

    Route::get('/my_patients', [App\Http\Controllers\PatientController::class, 'my_patients'])->name('my_patients');
    Route::post('/patient_vital_sign', [App\Http\Controllers\PatientController::class, 'patient_vital_sign'])->name('patient_vital_sign');
    Route::get('/my_patients_medicines', [App\Http\Controllers\PatientController::class, 'my_patients_medicines'])->name('my_patients_medicines');
    Route::get('/my_patients_lab_ipd', [App\Http\Controllers\PatientController::class, 'my_patients_lab_ipd'])->name('my_patients_lab_ipd');
    Route::get('getRooms', [App\Http\Controllers\FloorController::class, 'getRooms']);
    Route::get('getBeds', [App\Http\Controllers\FloorController::class, 'getBeds']);
    Route::get('getPatientMedicines', [App\Http\Controllers\PatientMedicineController::class, 'getPatientMedicines']);
    Route::get('previewPatientMedicines', [App\Http\Controllers\PatientPharmacyMedicineController::class, 'previewPatientMedicines']);
    Route::post('/save_requested_medicine', [App\Http\Controllers\PharmacyController::class, 'save_requested_medicine'])->name('save_requested_medicine');
    Route::get('getMedicines', [App\Http\Controllers\MedicineNameController::class, 'getMedicines']);
    Route::get('getPatientMedicinesForEdit/{id}', [App\Http\Controllers\PatientMedicineController::class, 'getPatientMedicinesForEdit']);
    Route::get('pharmacyEditPatientMedicines', [App\Http\Controllers\PatientPharmacyMedicineController::class, 'pharmacyEditPatientMedicines']);
    Route::get('laboratoryGetPatientLabs', [App\Http\Controllers\PatientLabController::class, 'laboratoryGetPatientLabs']);
    Route::get('previewPatientLabTests', [App\Http\Controllers\LaboratoryPatientLabController::class, 'previewPatientLabTests']);
    Route::get('getPatientLabsForEdit/{id}', [App\Http\Controllers\PatientLabController::class, 'getPatientLabsForEdit']);
    Route::get('printVitalSignOfPatient', [App\Http\Controllers\PatientController::class, 'printVitalSignOfPatient']);


    /// Reports ......//////
    Route::get('date_wise_procurement_report/', [\App\Http\Controllers\ReportController::class, 'date_wise_procurement_report']);
    Route::get('date_wise_sale_report/', [\App\Http\Controllers\ReportController::class, 'date_wise_sale_report']);
    Route::get('available_stock_report/', [\App\Http\Controllers\ReportController::class, 'available_stock_report']);
    Route::get('short_pharmacy_report/', [\App\Http\Controllers\ReportController::class, 'short_pharmacy_report']);
    Route::get('expired_medicine_report/', [\App\Http\Controllers\ReportController::class, 'expired_medicine_report']);
    Route::get('pharmacy_percentage_report/', [\App\Http\Controllers\ReportController::class, 'pharmacy_percentage_report']);
    Route::get('requested_medicine_report/', [\App\Http\Controllers\ReportController::class, 'requested_medicine_report']);
    Route::get('medication_report/', [\App\Http\Controllers\ReportController::class, 'medication_report']);
    Route::get('laboratory_sale_report/', [\App\Http\Controllers\ReportController::class, 'laboratory_sale_report']);
    Route::get('laboratory_tests_report/', [\App\Http\Controllers\ReportController::class, 'laboratory_tests_report']);
    Route::get('ipd_patient_report/', [\App\Http\Controllers\ReportController::class, 'ipd_patient_report']);
    Route::get('overview_report/', [\App\Http\Controllers\ReportController::class, 'overview_report'])->name('overview_report');
    Route::get('general_profits_report/', [\App\Http\Controllers\ReportController::class, 'general_profits_report']);
    Route::get('new_general_profits_report/', [\App\Http\Controllers\ReportController::class, 'new_general_profits_report']);
    Route::get('cumulative_report/', [\App\Http\Controllers\ReportController::class, 'cumulative_report']);
    Route::get('registered_all_patient_report/', [\App\Http\Controllers\ReportController::class, 'registered_all_patient_report']);
    Route::get('registered_in_door_patient_report/', [\App\Http\Controllers\ReportController::class, 'registered_in_door_patient_report']);
    Route::get('registered_out_door_patient_report/', [\App\Http\Controllers\ReportController::class, 'registered_out_door_patient_report']);
    Route::get('returned_medicines_report/', [\App\Http\Controllers\ReportController::class, 'returned_medicines_report']);
    Route::get('manual_expired_medicines_report/', [\App\Http\Controllers\ReportController::class, 'manual_expired_medicines_report']);
    Route::get('OPD_fee_report/', [\App\Http\Controllers\ReportController::class, 'OPD_fee_report']);
    Route::get('referral_report/', [\App\Http\Controllers\ReportController::class, 'referral_report']);
    Route::get('employee_percentage_report/', [\App\Http\Controllers\ReportController::class, 'employee_percentage_report']);


    Route::post('register_user', [App\Http\Controllers\HomeController::class, 'register_user'])->name('register_user');
    Route::post('edit_user/{id}', [App\Http\Controllers\HomeController::class, 'edit_user'])->name('edit_user');
    Route::get('deactivate_user/{id}', [App\Http\Controllers\HomeController::class, 'deactivate_user'])->name('deactivate_user');
    Route::get('activate_user/{id}', [App\Http\Controllers\HomeController::class, 'activate_user'])->name('activate_user');
    Route::get('delete_user/{id}', [App\Http\Controllers\HomeController::class, 'delete_user'])->name('delete_user');
    Route::get('/set_permission/{id}', [App\Http\Controllers\HomeController::class, 'set_permission']);
    Route::post('save_permissions', [App\Http\Controllers\HomeController::class, 'save_permissions']);
    Route::get('change_password', [App\Http\Controllers\HomeController::class, 'change_password']);
    Route::post('save_change_password', [App\Http\Controllers\HomeController::class, 'save_change_password']);


    Route::get('activate_employee/{id}', [App\Http\Controllers\EmployeeController::class, 'activate_employee'])->name('activate_employee');
    Route::get('deactivate_employee/{id}', [App\Http\Controllers\EmployeeController::class, 'deactivate_employee'])->name('deactivate_employee');
    Route::get('getEmployeeProfile/{id}', [App\Http\Controllers\EmployeeController::class, 'getEmployeeProfile']);
    Route::get('getPercentage/{id}',  [App\Http\Controllers\EmployeeController::class, 'getPercentage']);
    Route::post('setPercentage',  [App\Http\Controllers\EmployeeController::class, 'setPercentage']);


    //// Search Routes.................. ///
    //    Route::post('/search_my_patient', [App\Http\Controllers\DoctorController::class, 'search_my_patient'])->name('search_my_patient');
    Route::match(['get', 'post'], 'search_my_patient', [App\Http\Controllers\DoctorController::class, 'search_my_patient'])->name('search_my_patient');;

    Route::post('/search_medicine_patient', [App\Http\Controllers\PatientMedicineController::class, 'search_medicine_patient'])->name('search_medicine_patient');
    Route::match(['get', 'post'], '/search_reception_medicine_patient', [App\Http\Controllers\PatientPharmacyMedicineController::class, 'search_reception_medicine_patient'])->name('search_reception_medicine_patient');
    Route::match(['get', 'post'], '/search_reception_lab_patient', [App\Http\Controllers\PatientLabController::class, 'search_reception_lab_patient'])->name('search_reception_lab_patient');

    Route::get('/search_medicine', [App\Http\Controllers\PharmacyController::class, 'search_medicine'])->name('search_medicine');
    Route::match(['get', 'post'], '/search_laboratory_lab_patients', [App\Http\Controllers\PatientLabController::class, 'search_laboratory_lab_patients'])->name('search_laboratory_lab_patients');
    Route::match(['get', 'post'], 'search_reception_ipd_patient', [App\Http\Controllers\PatientIPDController::class, 'search_reception_ipd_patient'])->name('search_reception_ipd_patient');
    Route::match(['get', 'post'], 'search_patient_list', [App\Http\Controllers\PatientController::class, 'search_patient_list'])->name('search_patient_list');


    Route::post('/setSupplierMultipleMedicine', [App\Http\Controllers\PharmacyController::class, 'setSupplierMultipleMedicine']);

    Route::get('dischargePatient/{id}', [App\Http\Controllers\PatientIPDController::class, 'dischargePatient']);
    Route::get('/recent_entries_lab_patients', [App\Http\Controllers\LaboratoryPatientLabController::class, 'recent_entries_lab_patients']);
    Route::post('/recent_entries_lab_patients_search', [App\Http\Controllers\LaboratoryPatientLabController::class, 'recent_entries_lab_patients_search']);
    Route::get('delete_patient_test/{id}', [App\Http\Controllers\LaboratoryPatientLabController::class, 'delete_patient_test']);
    Route::get('download_lab_files/{id}', [App\Http\Controllers\LaboratoryPatientLabController::class, 'download_lab_files']);
    Route::get('patient_invoice/{id}', [App\Http\Controllers\PatientController::class, 'patient_invoice']);
    Route::get('/return_medicine/{id}', [App\Http\Controllers\PharmacyController::class, 'return_medicine']);
    Route::get('/undo_return_medicine/{id}', [App\Http\Controllers\PharmacyController::class, 'undo_return_medicine']);
    Route::get('/expire_this_medicine/{id}', [App\Http\Controllers\PharmacyController::class, 'expire_this_medicine']);
    Route::get('/undo_expire_this_medicine/{id}', [App\Http\Controllers\PharmacyController::class, 'undo_expire_this_medicine']);


    Route::resource('/PO', \App\Http\Controllers\PurchaseOrderController::class);
    Route::post('/searchPO', [\App\Http\Controllers\PurchaseOrderController::class, 'searchPO']);
    Route::get('getPOImages/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'getPOImages']);
    Route::post('po_actions', [App\Http\Controllers\PurchaseOrderController::class, 'po_actions']);
    Route::post('po_reject', [App\Http\Controllers\PurchaseOrderController::class, 'po_reject']);
    Route::post('approveMultiplePos', [App\Http\Controllers\PurchaseOrderController::class, 'approveMultiplePos']);
    Route::get('approvedPOs', [App\Http\Controllers\PurchaseOrderController::class, 'approvedPOs']);
    Route::get('unapprovedPOs', [App\Http\Controllers\PurchaseOrderController::class, 'unapprovedPOs']);
    Route::get('rejectedPOs', [App\Http\Controllers\PurchaseOrderController::class, 'rejectedPOs'])->name('PO.rejectedList');
    // In your web.php or api.php routes file
    Route::post('/po_status', [App\Http\Controllers\PurchaseOrderController::class, 'status'])->name('po.status');

    // Expenses
    Route::resource('/expenses', \App\Http\Controllers\ExpenseController::class)->only(['index', 'store', 'destroy']);
    Route::get('/expenses/search', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.search');
    Route::get('expenses/{id}/files', [\App\Http\Controllers\ExpenseController::class, 'files']);
    Route::delete('expenses/files', [\App\Http\Controllers\ExpenseController::class, 'deleteFile'])->name('expense-files-delete');

    Route::prefix('expense-categories')->group(function () {
        Route::get('/', [ExpenseCategoryController::class, 'index'])->name('expense_categories.index');
        Route::post('{id?}', [ExpenseCategoryController::class, 'store'])->name('expense_categories.store');
        Route::delete('{id}', [ExpenseCategoryController::class, 'destroy'])->name('expense_categories.destroy');
    });

    // Incomes
    Route::resource('/incomes', \App\Http\Controllers\IncomeController::class)->only(['index', 'store', 'destroy']);

    Route::prefix('income-categories')->group(function () {
        Route::get('/', [IncomeCategoryController::class, 'index'])->name('income_categories.index');
        Route::post('{id?}', [IncomeCategoryController::class, 'store'])->name('income_categories.store');
        Route::delete('{id}', [IncomeCategoryController::class, 'destroy'])->name('income_categories.destroy');
    });

    // Old POs
    Route::resource('/Old_PO', \App\Http\Controllers\OldPurchaseOrderController::class);
    Route::post('/searchPO_old', [\App\Http\Controllers\OldPurchaseOrderController::class, 'searchPO']);

    // Dashboard
    Route::get('get_daily_patient_count', [App\Http\Controllers\ReportController::class, 'get_daily_patient_count']);
    Route::get('get_monthly_patient_count', [App\Http\Controllers\ReportController::class, 'get_monthly_patient_count']);
    Route::get('get_daily_based_income_data', [App\Http\Controllers\ReportController::class, 'get_daily_based_income_data']);
    Route::get('get_monthly_based_income_data', [App\Http\Controllers\ReportController::class, 'get_monthly_based_income_data']);

    Route::get('set_permissions_from_lab', [App\Http\Controllers\ReportController::class, 'set_permissions_from_lab']);


    //For developers only
    //Clear Cache facade value:
    Route::get('/clear-cache', function () {
        $exitCode = Artisan::call('cache:clear');
        return '<h1>Cache facade value cleared</h1>';
    });

    //Reoptimized class loader:
    Route::get('/optimize', function () {
        $exitCode = Artisan::call('optimize');
        return '<h1>Reoptimized class loader</h1>';
    });

    //Route cache:
    Route::get('/route-cache', function () {
        $exitCode = Artisan::call('route:cache');
        return '<h1>Routes cached</h1>';
    });

    //Clear Route cache:
    Route::get('/route-clear', function () {
        $exitCode = Artisan::call('route:clear');
        return '<h1>Route cache cleared</h1>';
    });

    //Clear View cache:
    Route::get('/view-clear', function () {
        $exitCode = Artisan::call('view:clear');
        return '<h1>View cache cleared</h1>';
    });

    //Clear Config cache:
    Route::get('/config-cache', function () {
        $exitCode = Artisan::call('config:cache');
        return '<h1>Clear Config cleared</h1>';
    });

    //Clear Config cache:
    Route::get('/view-cache', function () {
        $exitCode = Artisan::call('view:cache');
        return '<h1>View caches!</h1>';
    });

    // Route to list all payrolls
    Route::get('/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');

    // Route to show the form to create a new payroll
    Route::get('/payrolls/create', [PayrollController::class, 'create'])->name('payrolls.create');

    // Route to store the newly created payroll and payroll items
    Route::post('/payrolls', [PayrollController::class, 'store'])->name('payrolls.store');

    // Route to show the details of a specific payroll
    Route::get('/payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');

    // Route to show the form to edit an existing payroll (if needed)
    Route::get('/payrolls/{payroll}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit');

    // Route to update the existing payroll
    Route::put('/payrolls/{payroll}', [PayrollController::class, 'update'])->name('payrolls.update');

    // Route to delete a payroll
    Route::delete('/payrolls/{payroll}', [PayrollController::class, 'destroy'])->name('payrolls.destroy');

    // Payroll Payments
    Route::get('/payroll-payments', [PayrollPaymentController::class, 'index'])->name('payroll_payments.index');
    Route::post('/search-payroll-payments', [PayrollPaymentController::class, 'search'])->name('payroll_payments.search');

    Route::get('/getPayrollDetails', [PayrollPaymentController::class, 'getPayrollDetails'])->name('payroll_payments.getPayrollDetails');

    Route::post('/payrolls/payments', [PayrollPaymentController::class, 'store'])->name('payroll_payments.store');

    Route::delete('/payrolls/payments/{id}', [PayrollPaymentController::class, 'destroy'])->name('payroll_payments.destroy');

    Route::get('data-migration', [DataMigrationController::class, 'index']);

    Route::post('/payroll_status', [PayrollController::class, 'status'])->name('payroll.status');

    Route::get('/payroll_payments/show', [PayrollPaymentController::class, 'show'])->name('payroll_payments.show');

    Route::prefix('employee-attendance')->controller(EmployeeAttendanceController::class)->group(function () {
        Route::get('/', 'index')->name('attendance.index');
        Route::post('/import', 'import')->name('attendance.import');
        Route::post('/adjust', 'adjust')->name('attendance.adjust');
    });
});

Auth::routes(['register' => true]);
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
