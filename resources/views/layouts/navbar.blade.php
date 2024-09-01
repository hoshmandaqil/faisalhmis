{{-- User Permission is shared on all views and have writen in AuthServiceProvider.php and globalFunction.php --}}
<nav class="navbar navbar-expand-lg custom-navbar">
    <button class="navbar-toggler" data-toggle="collapse" data-target="#retailAdminNavbar" type="button"
        aria-controls="retailAdminNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon">
            <i></i>
            <i></i>
            <i></i>
        </span>
    </button>
    <div class="collapse navbar-collapse" id="retailAdminNavbar">
        <ul class="navbar-nav m-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}">
                    <i class="icon-devices_other nav-icon"></i>
                    Dashboard
                </a>
            </li>

            @if (in_array('patient_list', $user_permissions) || in_array('doctor_menu', $user_permissions))
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle" id="appsDropdown" data-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-anchor nav-icon"></i>
                        Doctors
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="appsDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('my_patients') }}">My Patients</a>
                        </li>
                        @if (in_array('doctor_sale_medicine', $user_permissions))
                            <li>
                                <a class="dropdown-item" href="{{ route('my_patients_medicines') }}">My Patients
                                    (Pharmacy)</a>
                            </li>
                        @endif
                        @if (in_array('doctor_sale_ipd', $user_permissions))
                            <li>
                                <a class="dropdown-item" href="{{ route('my_patients_lab_ipd') }}">My Patients (Lab &
                                    IPD)</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (in_array('pharmacy_menu', $user_permissions))
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle" id="appsDropdown" data-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-dehaze nav-icon"></i>
                        Pharmacy
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="appsDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('patient_pharmacy_medicine.index') }}">Sale
                                Medicine</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('pharmacy.index') }}">Procurement</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array('reception_menu', $user_permissions))
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle @if (\Request::is('patient')) active-page @endif"
                        id="appsDropdown" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="icon-card_travel nav-icon"></i>
                        Reception
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="appsDropdown">
                        {{-- <li> --}}
                        {{-- <a class="dropdown-item" href="chat.html">In-Patients</a> --}}
                        {{-- </li> --}}
                        <li>
                            <a class="dropdown-item" href="{{ route('patient.index') }}">Patient Registration</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ url('patient_reception_medicine') }}">Pharmacy
                                Patients</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ url('reception_patient_labs') }}">Laboratory Patients</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('patient_ipd.index') }}">IPD Patients</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array('lab_menu', $user_permissions))

                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle @if (\Request::is('laboratory')) active-page @endif"
                        id="appsDropdown" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="icon-target nav-icon"></i>
                        Laboratory
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="appsDropdown">
                        {{-- <li> --}}
                        {{-- <a class="dropdown-item" href="chat.html">In-Patients</a> --}}
                        {{-- </li> --}}
                        <li>
                            <a class="dropdown-item" href="{{ route('patient_lab.index') }}">Laboratory Patients</a>
                        </li>
                        @if (in_array('delete_lab', $user_permissions))
                            <li>
                                <a class="dropdown-item" href="{{ url('recent_entries_lab_patients') }}">Recent Tests
                                    (Duplicate) </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (in_array('hr_menu', $user_permissions))
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle" id="appsDropdown" data-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-user-plus nav-icon"></i>
                        HR
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="appsDropdown">

                        <li>
                            <a class="dropdown-item" href="{{ route('employee.index') }}">Employees Management</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array('PO_menu', $user_permissions) || in_array('PO Creation', $user_permissions))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('PO.index') }}">
                        <i class="icon-attach_money nav-icon"></i>
                        Purchase Order
                    </a>
                </li>
            @endif
            {{-- @if (in_array('PO_menu', $user_permissions) || in_array('PO Creation', $user_permissions)) --}}
            @if (in_array('payroll_menu', $user_permissions))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('payrolls.index') }}">
                        <i class="icon-attach_money nav-icon"></i>
                        Payrolls
                    </a>
                </li>
            @endif
            {{-- @endif --}}
            {{-- @if (in_array('PO_menu', $user_permissions) || in_array('PO Creation', $user_permissions)) --}}
            @if (in_array('expense_menu', $user_permissions))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('expenses.index') }}">
                        <i class="icon-attach_money nav-icon"></i>
                        Expenses
                    </a>
                </li>
            @endif
            {{-- @endif --}}

            @if (in_array('attendance_menu', $user_permissions))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('attendance.index') }}">
                        <i class="icon-spellcheck nav-icon"></i>
                        E-Attendance
                    </a>
                </li>
            @endif

            @if (in_array('setting_view', $user_permissions))
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle" id="appsDropdown" data-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-settings nav-icon"></i>
                        Setting
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="appsDropdown">
                        {{-- <li> --}}
                        {{-- <a class="dropdown-item" href="{{route('doctor.index')}}">Doctors</a> --}}
                        {{-- </li> --}}
                        @if (in_array('user_list', $user_permissions))
                            <li>
                                <a class="dropdown-item" href="{{ url('users') }}">Users</a>
                            </li>
                        @endif
                        @if (in_array('floor_list', $user_permissions))
                            <li>
                                <a class="dropdown-item" href="{{ route('floor.index') }}">Floors</a>
                            </li>
                        @endif
                        @if (in_array('lab_list', $user_permissions))
                            <li>
                                <a class="dropdown-item" href="{{ route('lab_department.index') }}">Lab
                                    Departments</a>
                            </li>
                        @endif
                        @if (in_array('supplier_list', $user_permissions))
                            <li>
                                <a class="dropdown-item" href="{{ route('supplier.index') }}">Suppliers</a>
                            </li>
                        @endif
                        @if (in_array('holiday_menu', $user_permissions))
                            <li>
                                <a class="dropdown-item" href="{{ route('holiday.index') }}">Holidays</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif
            @if (in_array('reports_view', $user_permissions))
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle" id="appsDropdown" data-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-list nav-icon"></i>
                        Reports
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="appsDropdown">
                        <li>
                            <a class="dropdown-toggle sub-nav-link" id="submenuDropdown" data-toggle="dropdown"
                                href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                Reception Report
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="submenuDropdown">

                                @if (in_array('datewise_sale_report', $user_permissions))
                                    <li>
                                        <a class="dropdown-item" href="{{ url('date_wise_sale_report') }}">Pharmacy
                                            Sale
                                            Report</a>
                                    </li>
                                @endif

                                <li>
                                    <a class="dropdown-item" href="{{ url('laboratory_sale_report') }}">Diagnosis
                                        Report</a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('ipd_patient_report') }}">
                                        IPD Patients Report
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('registered_patient_report') }}">
                                        Registered Patients Report
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('OPD_fee_report') }}">
                                        OPD Patients Report
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('referral_report') }}">
                                        Referral Report
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('employee_percentage_report') }}">
                                        Employees Percentage Report
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li>
                            <a class="dropdown-toggle sub-nav-link" id="submenuDropdown" data-toggle="dropdown"
                                href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                Medicine Reports
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="submenuDropdown">
                                @if (in_array('datewise_procurement_report', $user_permissions))
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ url('date_wise_procurement_report') }}">DateWise
                                            Procurement Report</a>
                                    </li>
                                @endif

                                @if (in_array('available_stock_report', $user_permissions))
                                    <li>
                                        <a class="dropdown-item" href="{{ url('available_stock_report') }}">Available
                                            Stock Report</a>
                                    </li>
                                @endif

                                <li>
                                    <a class="dropdown-item" href="{{ url('returned_medicines_report') }}">Returned
                                        Medicines Report</a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ url('manual_expired_medicines_report') }}">Expired Medicines
                                        Report</a>
                                </li>

                            </ul>
                        </li>
                        <li>
                            <a class="dropdown-toggle sub-nav-link" id="submenuDropdown" data-toggle="dropdown"
                                href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                Pharmacy Profit Reports
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="submenuDropdown">
                                <li>
                                    <a class="dropdown-item" href="chat.html">Submenu 1</a>
                                </li>
                                @if (in_array('pharmacy_percentage_report', $user_permissions))
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ url('pharmacy_percentage_report') }}">Pharmacy
                                            Percentage Report</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        <li>
                            <a class="dropdown-toggle sub-nav-link" id="submenuDropdown" data-toggle="dropdown"
                                href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                Pharmacy Admin Reports
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="submenuDropdown">
                                @if (in_array('short_pharmacy_report', $user_permissions))
                                    <li>
                                        <a class="dropdown-item" href="{{ url('short_pharmacy_report') }}">Short
                                            Pharmacy
                                            Report</a>
                                    </li>
                                @endif

                                @if (in_array('expired_medicine_report', $user_permissions))
                                    <li>
                                        <a class="dropdown-item" href="{{ url('expired_medicine_report') }}">Expired
                                            Medicine Report</a>
                                    </li>
                                @endif

                                @if (in_array('request_medicine_report', $user_permissions))
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ url('requested_medicine_report') }}">Requested
                                            Medicine Report</a>
                                    </li>
                                @endif
                                @if (in_array('medication_report', $user_permissions))
                                    <li>
                                        <a class="dropdown-item" href="{{ url('medication_report') }}">Medication
                                            Report</a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        <li>
                            <a class="dropdown-toggle sub-nav-link" id="submenuDropdown" data-toggle="dropdown"
                                href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                Laboratory Report
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="submenuDropdown">

                                <li>
                                    <a class="dropdown-item" href="{{ url('laboratory_tests_report') }}">Laboratory
                                        Tests
                                        Report
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a class="dropdown-toggle sub-nav-link" id="submenuDropdown" data-toggle="dropdown"
                                href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                General Reports
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="submenuDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ url('general_profits_report') }}">
                                        General Report
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ url('new_general_profits_report') }}">
                                        Optimized General Report
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ url('cumulative_report') }}">
                                        Cumulative Report
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>

            @endif
        </ul>
    </div>

</nav>
