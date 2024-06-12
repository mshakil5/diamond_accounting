<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="{{ asset('img/admin.jpg') }}" alt="User Image">
        <div>
            <p class="app-sidebar__user-name">{{auth()->user()->name}}</p>
            <p class="app-sidebar__user-designation">User</p>
        </div>
    </div>
    <ul class="app-menu">
        {{-- // sales entry not access --}}
        @if (auth()->user()->user_type != 3)
        <li><a class="app-menu__item" href="{{url('dashboard')}}"><i class="app-menu__icon fa fa-dashboard" id="dashboard"></i><span class="app-menu__label">Dashboard</span></a></li>
        @endif
        <li><a class="app-menu__item" href="{{url('setting')}}" id="setting"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Settings</span></a></li>


                {{-- // super admin access --}}
                @if (auth()->user()->user_type == 11)
                
                <!--analysis -->
            <li class="treeview" id="analysis"><a class="app-menu__item" href="#"  data-toggle="treeview"><i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label">Analysis</span><i class="treeview-indicator fa fa-angle-right"></i></a>
                <ul class="treeview-menu">
                    <li><a class="treeview-item" href="{{url('analysis/transaction')}}" id="transaction"><i class="icon fa fa-circle-o"></i> Transaction</a></li>
                    <li><a class="treeview-item" href="{{url('analysis/account')}}" id="accounts"><i class="icon fa fa-circle-o"></i> Accounts</a></li>
                    <li><a class="treeview-item" href="{{url('analysis/employee')}}" id="employees"><i class="icon fa fa-circle-o"></i> Employee</a></li>
                    <li><a class="treeview-item" href="{{url('analysis/update')}}" id="updateddata"><i class="icon fa fa-circle-o"></i> Updated Transaction</a></li>
                </ul>
            </li>



                    {{-- branch crate  --}}
                <li><a class="app-menu__item" href="{{url('branch')}}" id="branch"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Branch</span></a></li>



                    {{-- user/staff create  --}}
                <li id="alluser"><a class="app-menu__item" href="{{url('manage-admin')}}" id="role-register"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">User</span></a></li>    


                @endif

                {{-- //super admin|Data entry|Read full access  --}}
                @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 1 || auth()->user()->user_type == 2)

        <li><a class="app-menu__item" href="{{url('account')}}" id="account"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Account</span></a></li>

        



        <li class="treeview" id="employee"><a class="app-menu__item" href="#"  data-toggle="treeview"><i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label">Employee</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="{{url('employee')}}" id="addemployee"><i class="icon fa fa-circle-o"></i> Add Employee</a></li>
                <li><a class="treeview-item" href="{{url('salary')}}" id="addsalary"><i class="icon fa fa-circle-o"></i>Employee Salary</a></li>
                <li><a class="treeview-item" href="{{url('employee_history')}}" id="employee_history"><i class="icon fa fa-circle-o"></i> Manage Histry</a></li>
                <li><a class="treeview-item" href="{{url('ledger/employee-ledger')}}" id="employee-ledger"><i class="icon fa fa-circle-o"></i> Employee Ledger</a></li>
                <li><a class="treeview-item" href="{{url('ledger/employeetax')}}" id="employeetax-ledger"><i class="icon fa fa-circle-o"></i> Employee Tax Ledger</a></li>
            </ul>
        </li>

        <li><a class="app-menu__item" href="{{url('expense')}}" id="expense"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Expense</span></a></li>
        <li><a class="app-menu__item" href="{{url('income')}}" id="income"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Sales & Income</span></a></li>


        <li class="treeview" id="allasset"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label">Asset</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="{{url('asset')}}" id="addasset"><i class="icon fa fa-circle-o"></i> Add Asset</a></li>
                <li><a class="treeview-item" href="{{url('ledger/current_asset')}}" id="current_asset_ledger"><i class="icon fa fa-circle-o"></i> Current Asset Ledger</a></li>
                <li><a class="treeview-item" href="{{url('ledger/account_receivable')}}" id="receivable_ledger"><i class="icon fa fa-circle-o"></i> Receivable Ledger</a></li>
                <li><a class="treeview-item" href="{{url('ledger/asset_ledger')}}" id="asset_ledger"><i class="icon fa fa-circle-o"></i> Ledger</a></li>
            </ul>
        </li>

        <li class="treeview" id="liability"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label">Liability</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="{{url('liability')}}" id="addliability"><i class="icon fa fa-circle-o"></i> Add Liability</a></li>
                <li><a class="treeview-item" href="{{url('ledger/account_payable')}}" id="payable_ledger"><i class="icon fa fa-circle-o"></i> Payable Ledger</a></li>
                <li><a class="treeview-item" href="{{url('ledger/current_liability_ledger')}}" id="current_liability_ledger"><i class="icon fa fa-circle-o"></i>Current Liability Ledger</a></li>
                <li><a class="treeview-item" href="{{url('ledger/liability_ledger')}}" id="liability_ledger"><i class="icon fa fa-circle-o"></i>  Liability Ledger</a></li>
            </ul>
        </li>

        <!--<li><a class="app-menu__item" href="{{url('ownerequity')}}" id="ownerequity"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Owners Equity</span></a></li>-->
        
         <li class="treeview" id="allownerequity"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-th-list"></i><span class="app-menu__label">Owners Equity</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="{{url('ownerequity')}}" id="ownerequity"><i class="icon fa fa-circle-o"></i>Add Owners Equity </a></li>
                <li><a class="treeview-item" href="{{url('ledger/ownerequity')}}" id="oeledger"><i class="icon fa fa-circle-o"></i>Owner Equity Ledger</a></li>
            </ul>
        </li>
        
        <li><a class="app-menu__item" href="{{url('ledger/income_expense')}}" id="income_expense"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Ledger List</span></a></li>


        <li class="treeview" id="vat_ledger"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-th-list"></i><span class="app-menu__label">Vat Ledger</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="{{url('ledger/vat_receive')}}" id="vat_receive"><i class="icon fa fa-circle-o"></i> Vat Receivable </a></li>
                <li><a class="treeview-item" href="{{url('ledger/vat_payable')}}" id="vat_payable"><i class="icon fa fa-circle-o"></i> Vat Payable </a></li>
            </ul>
        </li>

        <li class="treeview" id="daybook"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-th-list"></i><span class="app-menu__label">Day Book</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="{{url('daycashbook')}}" id="daycashbook"><i class="icon fa fa-circle-o"></i> Cash </a></li>
                <li><a class="treeview-item" href="{{url('daybankbook')}}" id="daybankbook"><i class="icon fa fa-circle-o"></i> Bank </a></li>
            </ul>
        </li>
        @endif
{{-- //super admin|Read full access --}}
        @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 1)
        <li class="treeview" id="financial_statement"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label">Financial Statement </span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="{{url('cashflow')}}" id="cashflow"><i class="icon fa fa-circle-o"></i> Cash Flow</a></li>
                <li><a class="treeview-item" href="{{url('profit_loss')}}" id="profit_loss"><i class="icon fa fa-circle-o"></i> Profit & Loss </a></li>
                <li><a class="treeview-item" href="{{url('retained_earnings')}}" id="retained_earnings"><i class="icon fa fa-circle-o"></i> Retained Earnings </a></li>
                <li><a class="treeview-item" href="{{url('financialstatement')}}" id="balancesheet"><i class="icon fa fa-circle-o"></i> Balance Sheet</a></li>
            </ul>
        </li>
        @endif

        {{-- //super admin|Data entry|Read full access|sales entry  --}}
        @if (auth()->user()->user_type == 11 || auth()->user()->user_type == 1 || auth()->user()->user_type == 2 || auth()->user()->user_type == 3)
        <li><a class="app-menu__item" href="{{url('regular')}}" id="regular"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Regular Form</span></a></li>

        @endif
    </ul>
</aside>
