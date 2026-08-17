@extends('admin_panel.layout.app')
@section('content')
@php
    $fmt = function($val) {
        $val = (float)$val;
        return ($val == (int)$val) ? number_format($val, 0) : number_format($val, 2);
    };
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    .sale-page-wrapper {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        padding: 10px 15px 30px;
        color: #1e293b;
    }

    /* Page Header */
    .sale-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 16px;
        padding: 20px 24px;
        color: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
        margin-bottom: 24px;
    }

    .sale-header-title {
        font-weight: 800;
        font-size: 22px;
        letter-spacing: -0.5px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sale-header-subtitle {
        font-size: 13px;
        color: #94a3b8;
        margin-top: 4px;
    }

    /* Header Actions */
    .sale-hdr-btn {
        padding: 9px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        text-decoration: none!important;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .sale-hdr-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.18);
    }
    .btn-add-sale {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff!important;
    }
    .btn-bookings {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: #ffffff!important;
    }
    .btn-returns {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff!important;
    }
    .btn-back-link {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff!important;
        backdrop-filter: blur(8px);
    }
    .btn-back-link:hover {
        background: rgba(255, 255, 255, 0.22);
    }

    /* Stat Cards Grid */
    .sale-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .sale-stat-card {
        border-radius: 14px;
        padding: 18px 20px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 20px -4px rgba(0,0,0,0.12);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .sale-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px -4px rgba(0,0,0,0.2);
    }
    .sale-stat-card .stat-icon {
        position: absolute;
        right: 16px;
        bottom: 12px;
        font-size: 42px;
        opacity: 0.18;
    }
    .sale-stat-card .stat-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
    }
    .sale-stat-card .stat-value {
        font-size: 24px;
        font-weight: 800;
        margin-top: 6px;
        letter-spacing: -0.5px;
    }

    .card-opening { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }
    .card-today-sale { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
    .card-expense { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }
    .card-net-cash { background: linear-gradient(135deg, #0f172a 0%, #334155 100%); }

    /* Filter Box */
    .sale-filter-box {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }
    .sale-filter-box .form-label {
        font-weight: 600;
        font-size: 12px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 6px;
    }
    .sale-filter-box .form-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 8px 12px;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    .sale-filter-box .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .btn-apply-filter {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #fff!important;
        font-weight: 600;
        font-size: 13px;
        padding: 9px 18px;
        border-radius: 8px;
        border: none;
        transition: all 0.2s ease;
    }
    .btn-apply-filter:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }
    .btn-reset-filter {
        background: #f1f5f9;
        color: #475569!important;
        font-weight: 600;
        font-size: 13px;
        padding: 9px 18px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
    }
    .btn-reset-filter:hover {
        background: #e2e8f0;
    }

    /* Table Container */
    .sale-table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        padding: 18px 20px;
    }

    /* Styled Datatable */
    #productTable {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
    }
    #productTable thead th,
    .table#productTable thead th,
    table.dataTable thead th {
        background: #0f172a !important;
        color: #ffffff !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 12px 10px !important;
        border: none !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
    }
    #productTable thead th:first-child {
        border-top-left-radius: 8px !important;
        border-bottom-left-radius: 8px !important;
    }
    #productTable thead th:last-child {
        border-top-right-radius: 8px !important;
        border-bottom-right-radius: 8px !important;
    }
    table.dataTable thead>tr>th.sorting:before,
    table.dataTable thead>tr>th.sorting:after,
    table.dataTable thead>tr>th.sorting_asc:before,
    table.dataTable thead>tr>th.sorting_asc:after,
    table.dataTable thead>tr>th.sorting_desc:before,
    table.dataTable thead>tr>th.sorting_desc:after {
        color: #ffffff !important;
        opacity: 0.65 !important;
    }
    #productTable tbody td {
        padding: 10px 12px !important;
        font-size: 12.5px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9 !important;
        color: #334155;
    }
    #productTable tbody tr:hover td {
        background-color: #f8fafc !important;
    }
    #productTable tbody tr:last-child td {
        border-bottom: none !important;
    }

    /* Content Badges inside Table */
    .sale-invoice-chip {
        background: #eff6ff;
        color: #2563eb;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
        font-size: 12px;
        display: inline-block;
        white-space: nowrap;
        font-family: inherit;
    }
    .sale-user-badge {
        background: #f8fafc;
        color: #334155;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        font-size: 11.5px;
        display: inline-block;
        white-space: nowrap;
    }
    .sale-customer-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 12.5px;
        white-space: nowrap;
    }
    .sale-prod-row {
        font-size: 12px;
        color: #334155;
        line-height: 1.4;
        padding: 1px 0;
        font-weight: 500;
    }
    .sale-num-row {
        font-size: 12px;
        color: #475569;
        line-height: 1.4;
    }
    .sale-total-highlight {
        font-weight: 800;
        font-size: 13.5px;
        color: #059669;
        background: #ecfdf5;
        padding: 3px 9px;
        border-radius: 6px;
        border: 1px solid #a7f3d0;
        display: inline-block;
        white-space: nowrap;
    }
    .sale-date-badge {
        font-size: 11.5px;
        color: #64748b;
        white-space: nowrap;
        font-weight: 500;
    }

    /* Status Badges */
    .sale-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
    }
    .status-sale {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }
    .status-return {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    /* Custom Processing Indicator Style */
    div.dataTables_wrapper div.dataTables_processing {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 220px;
        margin-left: -110px;
        margin-top: -30px;
        text-align: center;
        padding: 16px;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid #cbd5e1;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border-radius: 12px;
        z-index: 1000;
        font-weight: 600;
        color: #1e293b;
    }

    /* Custom Search Input */
    .dataTables_filter input {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 6px 12px !important;
        font-size: 13px !important;
        width: 260px !important;
        outline: none;
        transition: border 0.2s ease;
    }
    .dataTables_filter input:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
</style>

<div class="sale-page-wrapper container-fluid">
    
    {{-- Header Section --}}
    <div class="sale-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h1 class="sale-header-title">
                <i class="fa-solid fa-cash-register text-indigo-400" style="color: #818cf8;"></i>
                Sales Ledger & History
            </h1>
            <div class="sale-header-subtitle">
                Overview of POS transactions, customer invoices, and sales returns
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('sale.add') }}" class="sale-hdr-btn btn-add-sale">
                <i class="fa-solid fa-circle-plus"></i> New Sale
            </a>
            <a href="{{ url('bookings') }}" class="sale-hdr-btn btn-bookings">
                <i class="fa-solid fa-bookmark"></i> All Bookings
            </a>
            <a href="{{ url('sale-returns') }}" class="sale-hdr-btn btn-returns">
                <i class="fa-solid fa-rotate-left"></i> Sale Returns
            </a>
            <a href="{{ url()->previous() }}" class="sale-hdr-btn btn-back-link">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="sale-filter-box">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label"><i class="fa-solid fa-calendar-day me-1 text-primary"></i> From Date</label>
                <input type="date" id="filterFrom" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fa-solid fa-calendar-check me-1 text-primary"></i> To Date</label>
                <input type="date" id="filterTo" class="form-control">
            </div>
            @if(auth()->id() === 1 || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('super-admin'))
            <div class="col-md-3">
                <label class="form-label"><i class="fa-solid fa-user-tie me-1 text-primary"></i> Cashier / User</label>
                <select id="filterUser" class="form-control">
                    <option value="">All Cashiers & Users</option>
                    @foreach(\App\Models\User::all() as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-3 d-flex gap-2">
                <button id="btnFilter" class="btn-apply-filter w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                <button id="btnReset" class="btn-reset-filter">
                    <i class="fa-solid fa-rotate-right me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="sale-table-card">
        <div class="table-responsive">
            <table id="productTable" class="table align-middle nowrap">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>User</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Products</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Net Bill</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#productTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('sale') }}",
                data: function (d) {
                    d.from_date = $('#filterFrom').val();
                    d.to_date = $('#filterTo').val();
                    d.filter_user = $('#filterUser').val();
                }
            },
            columns: [
                { data: 0, orderable: false, searchable: false }, // S.No
                { data: 1 }, // User
                { data: 2 }, // Invoice
                { data: 3 }, // Customer
                { data: 4, orderable: false, searchable: false }, // Products
                { data: 5, orderable: false, searchable: false }, // Qty
                { data: 6, orderable: false, searchable: false }, // Price
                { data: 7, orderable: false, searchable: false }, // Discount
                { data: 8, orderable: false, searchable: false }, // Total Row
                { data: 9 }, // Bill Amount
                { data: 10 }, // Date
                { data: 11 }, // Status
                { data: 12, orderable: false, searchable: false } // Action
            ],
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            order: [
                [2, 'desc'] // Order by Invoice No by default
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search Invoice, Customer, Item...",
                processing: '<div class="spinner-border text-indigo-600 text-primary mb-2" role="status" style="width: 2.2rem; height: 2.2rem;"></div><div class="fw-bold fs-6">Loading Sales...</div>'
            }
        });

        $('#btnFilter').on('click', function() {
            table.draw();
        });

        $('#btnReset').on('click', function() {
            $('#filterFrom').val('');
            $('#filterTo').val('');
            $('#filterUser').val('');
            table.draw();
        });
    });
</script>
@endsection