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
        padding: 12px 16px 30px;
        color: #1e293b;
    }

    /* Page Header */
    .sale-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        border-radius: 14px;
        padding: 18px 22px;
        color: #ffffff;
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.25);
        margin-bottom: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .sale-header-title {
        font-weight: 800;
        font-size: 21px;
        letter-spacing: -0.4px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
    }
    .sale-header-title .header-icon-box {
        width: 36px;
        height: 36px;
        background: rgba(99, 102, 241, 0.2);
        border: 1px solid rgba(99, 102, 241, 0.4);
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #a5b4fc;
        font-size: 16px;
    }

    .sale-header-subtitle {
        font-size: 12.5px;
        color: #94a3b8;
        margin-top: 3px;
        font-weight: 400;
    }

    /* Header Actions */
    .sale-hdr-btn-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sale-hdr-btn {
        padding: 8px 14px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none !important;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        white-space: nowrap;
    }
    .sale-hdr-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        filter: brightness(1.08);
    }
    .btn-add-sale {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff !important;
    }
    .btn-bookings {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: #ffffff !important;
    }
    .btn-returns {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff !important;
    }
    .btn-back-link {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(6px);
    }
    .btn-back-link:hover {
        background: rgba(255, 255, 255, 0.22);
    }

    /* Filter Box */
    .sale-filter-box {
        background: #ffffff;
        border-radius: 14px;
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        margin-bottom: 20px;
    }
    .sale-filter-box .form-label {
        font-weight: 700;
        font-size: 11.5px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .sale-filter-box .form-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 8px 12px;
        font-size: 13px;
        height: 40px;
        transition: all 0.2s ease;
        background-color: #fff;
    }
    .sale-filter-box .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }
    .btn-apply-filter {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #fff !important;
        font-weight: 700;
        font-size: 13px;
        height: 40px;
        border-radius: 8px;
        border: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-apply-filter:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }
    .btn-reset-filter {
        background: #f8fafc;
        color: #475569 !important;
        font-weight: 700;
        font-size: 13px;
        height: 40px;
        padding: 0 16px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-reset-filter:hover {
        background: #e2e8f0;
    }

    /* Table Container */
    .sale-table-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        padding: 16px 18px;
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
        padding: 11px 10px !important;
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
        padding: 9px 11px !important;
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
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
        font-size: 12px;
        display: inline-block;
        white-space: nowrap;
    }
    .sale-user-badge {
        background: #f8fafc;
        color: #334155;
        font-weight: 600;
        padding: 3px 7px;
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
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .status-sale {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .status-return {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecdd3;
    }

    /* Returned Row Highlight (Soft Light Red Background) */
    tr.sale-row-returned td,
    tr.sale-row-returned {
        background-color: #fff1f2 !important;
        border-bottom-color: #fecdd3 !important;
    }
    tr.sale-row-returned:hover td {
        background-color: #ffe4e6 !important;
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
        width: 250px !important;
        outline: none;
        transition: border 0.2s ease;
    }
    .dataTables_filter input:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    /* Mobile Responsive Optimizations */
    @media (max-width: 768px) {
        .sale-page-wrapper {
            padding: 8px 8px 24px;
        }
        .sale-header-card {
            padding: 14px 16px;
            margin-bottom: 14px;
        }
        .sale-header-title {
            font-size: 17px;
        }
        .sale-header-subtitle {
            font-size: 11.5px;
        }
        .sale-hdr-btn-group {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            margin-top: 4px;
        }
        .sale-hdr-btn {
            padding: 8px 10px;
            font-size: 12px;
            justify-content: center;
        }
        .sale-filter-box {
            padding: 14px 14px;
            margin-bottom: 14px;
        }
        .sale-table-card {
            padding: 12px 10px;
            border-radius: 10px;
        }
        div.dataTables_wrapper div.dataTables_length,
        div.dataTables_wrapper div.dataTables_filter {
            text-align: left !important;
            float: none !important;
            margin-bottom: 10px;
            width: 100%;
        }
        div.dataTables_wrapper div.dataTables_filter input {
            width: 100% !important;
            margin-left: 0 !important;
            margin-top: 4px;
        }
    }
</style>

<div class="sale-page-wrapper container-fluid">
    
    {{-- Header Section --}}
    <div class="sale-header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h1 class="sale-header-title">
                <span class="header-icon-box"><i class="fa-solid fa-cash-register"></i></span>
                Sales Ledger & History
            </h1>
            <div class="sale-header-subtitle">
                Overview of POS transactions, customer invoices, and sales returns
            </div>
        </div>
        <div class="sale-hdr-btn-group">
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
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label"><i class="fa-solid fa-calendar-day text-primary"></i> From Date & Time (12h)</label>
                <input type="text" id="filterFrom" class="form-control" placeholder="Select Date & Time (AM/PM)">
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label"><i class="fa-solid fa-calendar-check text-primary"></i> To Date & Time (12h)</label>
                <input type="text" id="filterTo" class="form-control" placeholder="Select Date & Time (AM/PM)">
            </div>
            @if(auth()->id() === 1 || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('super-admin'))
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label"><i class="fa-solid fa-user-tie text-primary"></i> Cashier / User</label>
                <select id="filterUser" class="form-control">
                    <option value="">All Cashiers & Users</option>
                    @foreach(\App\Models\User::all() as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                <button id="btnFilter" class="btn-apply-filter w-100">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <button id="btnReset" class="btn-reset-filter">
                    <i class="fa-solid fa-rotate-right"></i> Reset
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function() {
        // Initialize 12-hour Date & Time Picker
        var fpFrom = flatpickr("#filterFrom", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d-M-Y h:i K", // 12-hour format with AM/PM (e.g. 17-Aug-2026 08:30 AM)
            time_24hr: false,
            allowInput: true,
            defaultHour: 0,
            defaultMinute: 0
        });

        var fpTo = flatpickr("#filterTo", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            altInput: true,
            altFormat: "d-M-Y h:i K", // 12-hour format with AM/PM (e.g. 17-Aug-2026 11:59 PM)
            time_24hr: false,
            allowInput: true,
            defaultHour: 23,
            defaultMinute: 59
        });

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
            responsive: false,
            autoWidth: false,
            createdRow: function(row, data, dataIndex) {
                // If row status is Return
                if (data[11] && data[11].indexOf('status-return') !== -1) {
                    $(row).addClass('sale-row-returned');
                }
            },
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
            fpFrom.clear();
            fpTo.clear();
            $('#filterUser').val('');
            table.draw();
        });
    });
</script>
@endsection