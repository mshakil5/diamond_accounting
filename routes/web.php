<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShareholdersController;

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
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');




//All accounts related  routes
// Route::prefix('admin')->group(function(){

    // redirect page if login / not login
    Route::get('/', 'App\Http\Controllers\Users\Admin\AdminController@index');


    // admin login/register 
    Route::get('/login', 'App\Http\Controllers\Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'App\Http\Controllers\Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::resource('/manage-admin','App\Http\Controllers\Auth\AdminRegisterController');

    // afterlogin dashboard 
    Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('admin.dashboard');

    // admin/user eidit/delete 
    Route::get('/role-edit/{id}', 'App\Http\Controllers\Auth\AdminRegisterController@registeredit');
    Route::put('/role-register-update/{id}', 'App\Http\Controllers\Auth\AdminRegisterController@registerupdate');
    Route::get('/role-delete/{id}', 'App\Http\Controllers\Auth\AdminRegisterController@registerdelete');
    //    analysis
    Route::get('analysis/transaction','App\Http\Controllers\DiagnosisController@getTransaction');
    Route::post('analysis_transaction_search','App\Http\Controllers\DiagnosisController@getTransactionSearch')->name('analysis_transaction_search');
    Route::post('analysis_transaction_user_search','App\Http\Controllers\DiagnosisController@getTransactionUserSearch')->name('analysis_transaction_user_search');
    Route::get('analysis/account','App\Http\Controllers\DiagnosisController@getAccount');
    Route::get('analysis/employee','App\Http\Controllers\DiagnosisController@getEmployee');
    Route::get('analysis/update','App\Http\Controllers\DiagnosisController@getUpdatedData');
Route::post('analysis/update/search','App\Http\Controllers\DiagnosisController@getUpdatedTransactionSearch')->name('analysis/update/search');
Route::post('analysis/update/user_search','App\Http\Controllers\DiagnosisController@getUpdatedTransactionUserSearch')->name('analysis/update/user_search');


        //setting
    Route::get('/setting', 'App\Http\Controllers\UserSettingController@index');
    Route::post('/setting', 'App\Http\Controllers\UserSettingController@update');
    Route::post('/changepassword', 'App\Http\Controllers\UserSettingController@changePassword');

    //old route
    Route::resource('branch', 'App\Http\Controllers\BranchController');
    Route::resource('user_role', 'App\Http\Controllers\UserRoleController');
    Route::resource('staff', 'App\Http\Controllers\StaffController');
    Route::resource('account', 'App\Http\Controllers\AccountController');
    Route::get('/account_delete/{id}', 'App\Http\Controllers\AccountController@deleteaccount');
    Route::resource('employee', 'App\Http\Controllers\EmployeeController');
    Route::get('/employee_delete/{id}', 'App\Http\Controllers\EmployeeController@deleteaccount');
    Route::resource('employee_history', 'App\Http\Controllers\EmployeeHistoryController');
    Route::resource('expense', 'App\Http\Controllers\ExpenseController');
    Route::post('search','App\Http\Controllers\ExpenseController@index')->name('search');
    
    //    for salary
    Route::resource('salary', 'App\Http\Controllers\SalaryController');
    Route::post('salary_search','App\Http\Controllers\SalaryController@index')->name('salary_search');
    //    for salary

    Route::resource('income', 'App\Http\Controllers\IncomeController');
    Route::post('income_search','App\Http\Controllers\IncomeController@index')->name('income_search');
    Route::resource('asset', 'App\Http\Controllers\AssetController');
    Route::post('asset_search','App\Http\Controllers\AssetController@index')->name('asset_search');
    Route::resource('liability', 'App\Http\Controllers\LiabilityController');
    Route::post('liability_search','App\Http\Controllers\LiabilityController@index')->name('liability_search');
    Route::resource('ownerequity', 'App\Http\Controllers\OwnerequityController');
    Route::post('oe_search','App\Http\Controllers\OwnerequityController@index')->name('oe_search');
    Route::resource('regular', 'App\Http\Controllers\RegularController');
    Route::post('regular_search','App\Http\Controllers\RegularController@index')->name('regular_search');
    Route::resource('depreciation', 'App\Http\Controllers\DepreciationController');

    Route::get('ledger/employee-ledger','App\Http\Controllers\LedgerController@getEmployeeList');
    Route::get('ledger/employee-ledger/{id}','App\Http\Controllers\LedgerController@getEmployeeledger');
    Route::post('employee_ledger_search','App\Http\Controllers\LedgerController@employeeSearch')->name('employee_ledger_search');
    
    Route::get('ledger/employeepayable','App\Http\Controllers\LedgerController@getEmployeePayableList');
    Route::get('ledger/employeepayable/{id}','App\Http\Controllers\LedgerController@getEmployeePayableledger');
        Route::post('employeePayable_ledger_search','App\Http\Controllers\LedgerController@getEmployeePayableledgerSearch')->name('employeePayable_ledger_search');
        //prepaid salary
    Route::get('ledger/prepaidemployee','App\Http\Controllers\LedgerController@getPrepaidEmployeeList');
    Route::get('ledger/prepaidemployee/{id}','App\Http\Controllers\LedgerController@getPrepaidEmployeeledger');
Route::post('prepaidEmployee_ledger_search','App\Http\Controllers\LedgerController@getPrepaidEmployeeledgerSearch')->name('prepaidEmployee_ledger_search');
        
    Route::get('ledger/employeetax','App\Http\Controllers\LedgerController@getEmployeeTax');
    Route::get('ledger/employeetax/{id}','App\Http\Controllers\LedgerController@getEmployeetaxledger');
Route::post('employeeTax_ledger_search','App\Http\Controllers\LedgerController@getEmployeetaxledgerSearch')->name('employeeTax_ledger_search');
    
    
    Route::get('ledger/asset_ledger','App\Http\Controllers\LedgerController@getAssetList');
    Route::get('ledger/asset_ledger/{id}','App\Http\Controllers\LedgerController@getAssetledger');
    Route::post('asset_ledger_search','App\Http\Controllers\LedgerController@fixedAssetSearch')->name('asset_ledger_search');
    Route::get('ledger/liability_ledger','App\Http\Controllers\LedgerController@getLiabilityList');
    Route::get('ledger/liability_ledger/{id}','App\Http\Controllers\LedgerController@getLiabilityledger');
    Route::post('liability_ledger_search','App\Http\Controllers\LedgerController@getLiabilitySearch')->name('liability_ledger_search');
    
    Route::get('ledger/current_liability_ledger','App\Http\Controllers\LedgerController@getCurrentLiabilityList');
    Route::get('ledger/current_liability_ledger/{id}','App\Http\Controllers\LedgerController@getCurrentLiabilityledger');
    Route::post('current_liability_ledger_search','App\Http\Controllers\LedgerController@getCurrentLiabilitySearch')->name('current_liability_ledger_search');
    
    Route::get('ledger/ownerequity','App\Http\Controllers\LedgerController@getOwnerEquity');
    Route::get('ledger/ownerequity/{id}','App\Http\Controllers\LedgerController@getOwnerEquityLedger');
    Route::post('capital_ledger_search','App\Http\Controllers\LedgerController@getOwnerEquitySearch')->name('capital_ledger_search');


    
    
    Route::get('ledger/account_payable','App\Http\Controllers\LedgerController@getAccountPayable');
    Route::get('ledger/account_payable/{id}','App\Http\Controllers\LedgerController@getAccPayableledger');
    Route::post('accPayable_ledger_search','App\Http\Controllers\LedgerController@getAccPayableSearch')->name('accPayable_ledger_search');
    Route::get('ledger/current_asset','App\Http\Controllers\LedgerController@getCurrentAsset');
    Route::get('ledger/current_asset/{id}','App\Http\Controllers\LedgerController@getCurrentAssetledger');
    Route::post('currentasset_ledger_search','App\Http\Controllers\LedgerController@currentAssetSearch')->name('currentasset_ledger_search');
    Route::get('ledger/account_receivable','App\Http\Controllers\LedgerController@getAccountReceivable');
    Route::get('ledger/account_receivable/{id}','App\Http\Controllers\LedgerController@getAccReceivableledger');
    Route::post('accReceivable_ledger_search','App\Http\Controllers\LedgerController@accReceivableSearch')->name('accReceivable_ledger_search');
    Route::get('ledger/income_expense','App\Http\Controllers\LedgerController@getledger');
    Route::get('ledger/income_expense/{id}','App\Http\Controllers\LedgerController@getIncomeorExpense');
    Route::post('incomeExpense_ledger_search','App\Http\Controllers\LedgerController@getIncomeExpenseSearch')->name('incomeExpense_ledger_search');
    
    Route::get('ledger/adv_income_expense','App\Http\Controllers\LedgerController@getadvanceledger');
    Route::get('ledger/adv_income_expense/{id}','App\Http\Controllers\LedgerController@getAdvIncomeorExpense');
    Route::post('advIncomeExpense_ledger_search','App\Http\Controllers\LedgerController@getAdvIncomeorExpenseSearch')->name('advIncomeExpense_ledger_search');
    
    Route::get('ledger/interest','App\Http\Controllers\LedgerController@interest');
    Route::post('interest_ledger_search','App\Http\Controllers\LedgerController@getInterestSearch')->name('interest_ledger_search');
    Route::get('ledger/depreciation','App\Http\Controllers\LedgerController@depreciation');
    Route::post('dep_ledger_search','App\Http\Controllers\LedgerController@getDepSearch')->name('dep_ledger_search');
    //    salary ledger
    Route::get('ledger/salary','App\Http\Controllers\LedgerController@getSalaryLedger');
    Route::post('salary_ledger_search','App\Http\Controllers\LedgerController@getSalaryLedgerSearch')->name('salary_ledger_search');
    //    salary ledger end
    
    
    Route::get('ledger/tax','App\Http\Controllers\LedgerController@getTaxPayableLedger');
    Route::post('tax_ledger_search','App\Http\Controllers\LedgerController@getTaxSearch')->name('tax_ledger_search');
    Route::get('ledger/sharepremium','App\Http\Controllers\LedgerController@getSharePremiumLedger');
    Route::post('sharepremium_ledger_search','App\Http\Controllers\LedgerController@getSharePremiumSearch')->name('sharepremium_ledger_search');

    Route::get('ledger/vat_receive','App\Http\Controllers\LedgerController@vatReceive');
    Route::post('receivable_vat_ledger_search','App\Http\Controllers\LedgerController@ReceivableVatSearch')->name('receivable_vat_ledger_search');
    Route::get('ledger/vat_payable','App\Http\Controllers\LedgerController@vatpayable');
    Route::post('payable_vat_ledger_search','App\Http\Controllers\LedgerController@PayableVatSearch')->name('payable_vat_ledger_search');
    Route::get('cashflow','App\Http\Controllers\CashFlowController@getCashFlow');
    Route::post('cashflow_search','App\Http\Controllers\CashFlowController@getCashFlow')->name('cashflow_search');
    Route::get('profit_loss','App\Http\Controllers\CashFlowController@getProfitLoss');
    Route::post('profit_loss_search','App\Http\Controllers\CashFlowController@getProfitLoss')->name('profit_loss_search');
    
    Route::get('retained_earnings','App\Http\Controllers\CashFlowController@getRetainedEarnings');
    Route::post('retained_earnings_search','App\Http\Controllers\CashFlowController@getRetainedEarnings')->name('retained_earnings_search');

    Route::get('financialstatement','App\Http\Controllers\FinancialStatementController@getFinancialStatement');
    Route::post('financial_statement_search','App\Http\Controllers\FinancialStatementController@getFinancialStatement')->name('financial_statement_search');




    Route::get('daycashbook','App\Http\Controllers\DayBookController@getDayBook');
    Route::post('daycashbook_search','App\Http\Controllers\DayBookController@getDayBookSearch')->name('daycashbook_search');
    Route::get('daybankbook','App\Http\Controllers\DayBookController@getBankBook');
    Route::post('daybankbook_search','App\Http\Controllers\DayBookController@getBankBookSearch')->name('daybankbook_search');
    
    
    // all diagonosis report 
    Route::post('account_diagnosis/{id}','App\Http\Controllers\DiagnosisController@getAccDiagonosis');
    Route::post('tran_diagnosis/{id}','App\Http\Controllers\DiagnosisController@getTranDiagonosis');

   

    
    Route::get('/shareholder', [ShareholdersController::class, 'index'])->name('admin.shareholder');
    Route::post('/shareholder', [ShareholdersController::class, 'store']);
    Route::get('/shareholder/{id}/edit', [ShareholdersController::class, 'edit']);
    Route::post('/shareholder-update', [ShareholdersController::class, 'update']);
    Route::get('/shareholder/{id}', [ShareholdersController::class, 'delete']);

    Route::get('/ledger/shareholder-ledger', [ShareholdersController::class, 'getShareholderList'])->name('shareholderLedgerList');

    Route::get('/ledger/shareholder-dividend-ledger/{id}', [ShareholdersController::class, 'getShareholderListDividendLedger'])->name('shareholderDividendLedger');
    Route::post('/ledger/shareholder-dividend-ledger/{id}', [ShareholdersController::class, 'getShareholderListDividendLedgerSearch'])->name('shareholderDividendLedgerSearch');
    



    Route::get('/ledger/shareholder-capital-ledger/{id}', [ShareholdersController::class, 'getShareholderCapitalLedger'])->name('shareholderCapitalLedger');

    Route::post('/ledger/shareholder-capital-ledger/{id}', [ShareholdersController::class, 'getShareholderCapitalLedgerSearch'])->name('shareholderCapitalLedgerSearch');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('admin.invoices');
    Route::post('/invoices', [InvoiceController::class, 'store']);
    Route::get('/invoices/{id}/edit', [InvoiceController::class, 'edit']);
    Route::post('/invoices-update', [InvoiceController::class, 'update']);

    Route::get('/invoices/show/{id}', [InvoiceController::class, 'show'])->name('invoices.show');



    

// });