@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --rm-bg: #f1f4f9;
  --rm-surface: #ffffff;
  --rm-border: #e9edf2;
  --rm-border-lt: #f1f4f9;
  --rm-text: #0b1a33;
  --rm-text-sec: #54657e;
  --rm-text-muted: #8896ab;
  --rm-accent: #2b7fff;
  --rm-accent-drk: #1a6ae8;
  --rm-success: #0fae6b;
  --rm-warning: #f59e0b;
  --rm-danger: #e54545;
  --rm-radius: 14px;
  --rm-radius-sm: 9px;
  --rm-shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
  --rm-shadow-lg: 0 8px 30px rgba(0,0,0,.07), 0 3px 12px rgba(0,0,0,.04);
  --rm-shadow-xl: 0 20px 60px rgba(0,0,0,.10), 0 8px 24px rgba(0,0,0,.06);
  --rm-font: 'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif;
}

.rm-page * { font-family: var(--rm-font); }
.rm-page { background: var(--rm-bg); min-height: 100vh; padding-bottom: 2.5rem; }

/* HEADER */
.rm-hdr {
  position: relative;
  background: linear-gradient(135deg, #0b1a33 0%, #162d50 50%, #1a4d8c 100%);
  border-radius: var(--rm-radius);
  padding: 1.3rem 2rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--rm-shadow-xl);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  overflow: hidden;
}
.rm-hdr::before {
  content: ''; position: absolute; inset: 0;
  background: radial-gradient(ellipse 60% 50% at 10% 90%, rgba(43,127,255,.18) 0%, transparent 100%),
              radial-gradient(ellipse 40% 40% at 90% 10%, rgba(43,127,255,.1) 0%, transparent 100%);
  pointer-events: none;
}
.rm-hdr > * { position: relative; z-index: 1; }
.rm-hdr h2 { font-size: 1.35rem; font-weight: 800; color: #fff; letter-spacing: -.4px; margin: 0; display: flex; align-items: center; gap: .65rem; }
.rm-hdr h2 i { font-size: 1.4rem; color: #60a5fa; }
.rm-hdr .hdr-sub { color: #94a3b8; font-size: .8rem; font-weight: 500; }

/* METRIC CARDS */
.rm-stat-card {
  background: var(--rm-surface);
  border: 1px solid var(--rm-border);
  border-radius: var(--rm-radius);
  padding: 1.1rem 1.25rem;
  box-shadow: var(--rm-shadow);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform .25s ease, box-shadow .25s ease;
}
.rm-stat-card:hover { transform: translateY(-2px); box-shadow: var(--rm-shadow-lg); }
.rm-stat-icon {
  width: 46px; height: 46px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.25rem; flex-shrink: 0;
}
.rm-icon-blue { background: #eef2ff; color: #3b5bb3; }
.rm-icon-green { background: #ecfdf5; color: #059669; }
.rm-icon-orange { background: #fff7ed; color: #ea580c; }
.rm-icon-purple { background: #faf5ff; color: #9333ea; }

.rm-stat-title { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--rm-text-muted); margin-bottom: 2px; }
.rm-stat-value { font-size: 1.2rem; font-weight: 800; color: var(--rm-text); margin: 0; line-height: 1.2; }
.rm-stat-desc { font-size: .72rem; color: var(--rm-text-sec); }

/* TABS */
.rm-nav-tabs {
  display: flex;
  gap: .5rem;
  background: #e2e8f0;
  padding: .35rem;
  border-radius: var(--rm-radius);
  margin-bottom: 1.5rem;
  overflow-x: auto;
}
.rm-nav-tabs .nav-link {
  border: none;
  border-radius: var(--rm-radius-sm);
  padding: .6rem 1.25rem;
  font-size: .84rem;
  font-weight: 700;
  color: var(--rm-text-sec);
  background: transparent;
  transition: all .2s ease;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: .5rem;
}
.rm-nav-tabs .nav-link:hover { color: var(--rm-text); background: rgba(255,255,255,.5); }
.rm-nav-tabs .nav-link.active {
  background: #ffffff;
  color: var(--rm-accent-drk);
  box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

/* BUTTONS & CARDS */
.rm-card { background: var(--rm-surface); border: 1px solid var(--rm-border); border-radius: var(--rm-radius); box-shadow: var(--rm-shadow); }
.rm-card-body { padding: 1.5rem; }

.rm-btn {
  display: inline-flex; align-items: center; gap: .4rem; border-radius: var(--rm-radius-sm);
  font-weight: 600; font-size: .78rem; transition: all .25s ease; cursor: pointer;
  text-decoration: none; border: none; padding: .45rem 1.1rem;
}
.rm-btn-primary { background: linear-gradient(135deg, var(--rm-accent) 0%, var(--rm-accent-drk) 100%); color: #fff; }
.rm-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(43,127,255,.25); color: #fff; }
.rm-btn-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; }
.rm-btn-success:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,185,129,.25); color: #fff; }
.rm-btn-ghost { background: #f1f5f9; border: 1px solid #cbd5e1; color: var(--rm-text); }
.rm-btn-ghost:hover { background: #e2e8f0; color: var(--rm-text); }
.rm-btn-sm { font-size: .74rem; padding: .35rem .85rem; }

/* TABLES */
.rm-tbl-wrap { overflow-x: auto; border: 1px solid var(--rm-border); border-radius: var(--rm-radius-sm); }
.rm-tbl, table.dataTable { width: 100% !important; border-collapse: separate; border-spacing: 0; font-size: .83rem; }
.rm-tbl thead th, table.dataTable thead th { background: #f8fafc; font-size: .67rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--rm-text-muted); padding: .65rem .75rem; border-bottom: 2px solid var(--rm-border); text-align: left; white-space: nowrap; }
.rm-tbl tbody td, table.dataTable tbody td { padding: .65rem .75rem; border-bottom: 1px solid var(--rm-border-lt); vertical-align: middle; }
.rm-tbl tbody tr:hover, table.dataTable tbody tr:hover { background: #fafbfc; }

.rm-badge { font-size: .72rem; font-weight: 700; padding: .25rem .6rem; border-radius: 6px; display: inline-block; }
.rm-badge-success { background: #dcfce7; color: #15803d; }
.rm-badge-warning { background: #fef3c7; color: #b45309; }
.rm-badge-danger { background: #fee2e2; color: #b91c1c; }

/* MODALS & INPUTS */
.rm-lbl { font-size: .78rem; font-weight: 600; color: var(--rm-text-sec); margin-bottom: .3rem; display: block; }
.rm-fld { border: 1.5px solid var(--rm-border); border-radius: var(--rm-radius-sm); padding: .45rem .75rem; font-size: .84rem; font-weight: 500; color: var(--rm-text); background: var(--rm-surface); width: 100%; outline: none; transition: all .2s ease; }
.rm-fld:focus { border-color: var(--rm-accent); box-shadow: 0 0 0 3px rgba(43,127,255,.1); }

#rawMaterialTable input.form-control, #rawMaterialTable select.form-select {
  font-size: .82rem;
  border-color: #cbd5e1;
  border-radius: 6px;
  padding: .35rem .55rem;
}
#rawMaterialTable input.form-control:focus, #rawMaterialTable select.form-select:focus {
  border-color: #2b7fff;
  box-shadow: 0 0 0 2.5px rgba(43, 127, 255, 0.15);
}
#rawMaterialTable tbody tr:hover {
  background-color: #f8fafc;
}

@media print {
  .no-print, .rm-hdr, .rm-nav-tabs, .rm-stat-card, .no-print * { display: none !important; }
  .rm-page { background: #fff !important; padding: 0 !important; }
  .tab-pane { display: block !important; opacity: 1 !important; }
  .rm-card { border: none !important; box-shadow: none !important; }
  #print-area, #print-area * { visibility: visible; }
}
</style>

<div class="rm-page">
<div class="container-fluid px-3 px-md-4 py-3">

  <!-- HEADER -->
  <div class="rm-hdr">
    <div>
      <h2><i class="bi bi-box-seam me-2"></i> Raw Material Management</h2>
      <span class="hdr-sub">Manage raw materials inventory, purchases, vendor list and vendor ledger statement.</span>
    </div>
    <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">
      <button class="rm-btn rm-btn-primary" data-bs-toggle="modal" data-bs-target="#materialModal" onclick="clearMaterialForm()">
        <i class="bi bi-plus-circle me-1"></i> Add Raw Material
      </button>
      <button class="rm-btn rm-btn-success" data-bs-toggle="modal" data-bs-target="#purchaseModal" onclick="clearPurchaseForm()">
        <i class="bi bi-cart-plus me-1"></i> New Material Purchase
      </button>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" style="border:none; border-radius:var(--rm-radius-sm); font-size:.86rem;">
    <strong><i class="bi bi-check-circle me-1"></i> Success:</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  @if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show mb-3" style="border:none; border-radius:var(--rm-radius-sm); font-size:.86rem;">
    <strong><i class="bi bi-exclamation-triangle me-1"></i> Alert:</strong> {{ $errors->first() }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <!-- SUMMARY CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
      <div class="rm-stat-card">
        <div class="rm-stat-icon rm-icon-blue"><i class="bi bi-boxes"></i></div>
        <div>
          <div class="rm-stat-title">Total Materials</div>
          <h3 class="rm-stat-value">{{ $totalMaterials }}</h3>
          <span class="rm-stat-desc text-danger">{{ $alertMaterials }} Low Stock Alert</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
      <div class="rm-stat-card">
        <div class="rm-stat-icon rm-icon-green"><i class="bi bi-cart-check"></i></div>
        <div>
          <div class="rm-stat-title">Purchases Total</div>
          <h3 class="rm-stat-value">Rs {{ number_format($totalPurchaseAmount, 0) }}</h3>
          <span class="rm-stat-desc text-success">{{ count($purchases) }} Purchases Recorded</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
      <div class="rm-stat-card">
        <div class="rm-stat-icon rm-icon-blue" style="background: #e0f2fe; color: #0284c7;"><i class="bi bi-truck"></i></div>
        <div>
          <div class="rm-stat-title">Material Out (DC)</div>
          <h3 class="rm-stat-value">{{ $totalOutsCount }}</h3>
          <span class="rm-stat-desc text-primary">Kitchen / Factory Out</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
      <div class="rm-stat-card">
        <div class="rm-stat-icon rm-icon-purple"><i class="bi bi-people"></i></div>
        <div>
          <div class="rm-stat-title">Material Vendors</div>
          <h3 class="rm-stat-value">{{ $totalVendors }}</h3>
          <span class="rm-stat-desc">Registered Vendors</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2-4" style="flex: 0 0 auto; width: 20%;">
      <div class="rm-stat-card">
        <div class="rm-stat-icon rm-icon-orange"><i class="bi bi-wallet2"></i></div>
        <div>
          <div class="rm-stat-title">Vendor Payable</div>
          <h3 class="rm-stat-value {{ $totalPayableBalance > 0 ? 'text-danger' : 'text-success' }}">
            Rs {{ number_format($totalPayableBalance, 0) }}
          </h3>
          <span class="rm-stat-desc">Net Payable Balance</span>
        </div>
      </div>
    </div>
  </div>

  <!-- NAVIGATION TABS -->
  <ul class="nav rm-nav-tabs" id="rawMaterialTabs" role="tablist">
    <li class="nav-item">
      <button class="nav-link {{ $activeTab === 'materials' ? 'active' : '' }}" id="materials-tab" data-bs-toggle="tab" data-bs-target="#tab-materials" type="button">
        <i class="bi bi-box-seam me-1"></i> Raw Material List
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link {{ $activeTab === 'purchases' ? 'active' : '' }}" id="purchases-tab" data-bs-toggle="tab" data-bs-target="#tab-purchases" type="button">
        <i class="bi bi-cart-plus me-1"></i> Raw Material Purchase
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link {{ $activeTab === 'out' ? 'active' : '' }}" id="out-tab" data-bs-toggle="tab" data-bs-target="#tab-out" type="button">
        <i class="bi bi-truck me-1"></i> Raw Material Out (DC)
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link {{ $activeTab === 'vendors' ? 'active' : '' }}" id="vendors-tab" data-bs-toggle="tab" data-bs-target="#tab-vendors" type="button">
        <i class="bi bi-people me-1"></i> Raw Material Vendors
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link {{ $activeTab === 'ledger' ? 'active' : '' }}" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#tab-ledger" type="button">
        <i class="bi bi-journal-text me-1"></i> Vendor Ledger
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link {{ $activeTab === 'stock_report' ? 'active' : '' }}" id="stock-report-tab" data-bs-toggle="tab" data-bs-target="#tab-stock-report" type="button">
        <i class="bi bi-file-earmark-bar-graph me-1"></i> Stock Report
      </button>
    </li>
  </ul>

  <!-- TAB CONTENT -->
  <div class="tab-content" id="rawMaterialTabsContent">

    <!-- 1. RAW MATERIAL LIST TAB -->
    <div class="tab-pane fade {{ $activeTab === 'materials' ? 'show active' : '' }}" id="tab-materials">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-cubes me-2 text-primary"></i>Raw Material Inventory Items</h5>
            <button class="rm-btn rm-btn-primary rm-btn-sm" data-bs-toggle="modal" data-bs-target="#materialModal" onclick="clearMaterialForm()">
              <i class="fa fa-plus me-1"></i> Add New Item
            </button>
          </div>

          <div class="rm-tbl-wrap">
            <table id="materialsTable" class="rm-tbl align-middle">
              <thead>
                <tr>
                  <th style="width: 45px;">S.No</th>
                  <th>Item Code</th>
                  <th>Material Name</th>
                  <th>Unit</th>
                  <th>Current Stock</th>
                  <th>Last Rate (Rs)</th>
                  <th>Stock Value (Rs)</th>
                  <th>Alert Qty</th>
                  <th>Status</th>
                  <th style="width: 110px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($materials as $index => $mat)
                @php
                  $isLowStock = $mat->stock_qty <= $mat->alert_qty;
                  $stockVal = $mat->stock_qty * $mat->unit_price;
                @endphp
                <tr>
                  <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                  <td><span class="badge bg-light text-dark border">{{ $mat->item_code ?? 'RM-'.str_pad($mat->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                  <td><strong class="text-dark" style="font-size: .9rem;">{{ $mat->name }}</strong></td>
                  <td><span class="badge bg-secondary">{{ $mat->unit }}</span></td>
                  <td>
                    <span class="fw-bold {{ $isLowStock ? 'text-danger' : 'text-success' }}" style="font-size: .95rem;">
                      {{ number_format($mat->stock_qty, 2) }} {{ $mat->unit }}
                    </span>
                  </td>
                  <td class="fw-semibold">Rs {{ number_format($mat->unit_price, 2) }}</td>
                  <td class="fw-bold text-dark">Rs {{ number_format($stockVal, 2) }}</td>
                  <td class="text-muted">{{ number_format($mat->alert_qty, 2) }}</td>
                  <td>
                    @if($isLowStock)
                      <span class="rm-badge rm-badge-danger"><i class="fa fa-exclamation-triangle me-1"></i> Low Stock</span>
                    @else
                      <span class="rm-badge rm-badge-success"><i class="fa fa-check me-1"></i> In Stock</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <button class="btn btn-sm btn-outline-primary py-1 px-2 btn-edit-material"
                              data-id="{{ $mat->id }}"
                              data-code="{{ $mat->item_code }}"
                              data-name="{{ $mat->name }}"
                              data-unit="{{ $mat->unit }}"
                              data-price="{{ $mat->unit_price }}"
                              data-stock="{{ $mat->stock_qty }}"
                              data-alert="{{ $mat->alert_qty }}"
                              data-note="{{ $mat->note }}"
                              title="Edit">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <a href="{{ route('raw_materials.material.delete', $mat->id) }}" class="btn btn-sm btn-outline-danger py-1 px-2" onclick="return confirm('Delete this raw material?')" title="Delete">
                        <i class="bi bi-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    <!-- 2. RAW MATERIAL PURCHASES TAB -->
    <div class="tab-pane fade {{ $activeTab === 'purchases' ? 'show active' : '' }}" id="tab-purchases">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-shopping-cart me-2 text-success"></i>Raw Material Purchases History</h5>
            <button class="rm-btn rm-btn-success rm-btn-sm" data-bs-toggle="modal" data-bs-target="#purchaseModal" onclick="clearPurchaseForm()">
              <i class="fa fa-shopping-cart me-1"></i> New Material Purchase
            </button>
          </div>

          <div class="rm-tbl-wrap">
            <table id="purchasesTable" class="rm-tbl align-middle">
              <thead>
                <tr>
                  <th style="width: 45px;">#</th>
                  <th>Purchase No</th>
                  <th>Date</th>
                  <th>Vendor</th>
                  <th>Items Purchased</th>
                  <th>Subtotal</th>
                  <th>Net Amount</th>
                  <th>Paid Amount</th>
                  <th>Due Amount</th>
                  <th>Status</th>
                  <th style="width: 120px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($purchases as $index => $pur)
                <tr>
                  <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                  <td><strong class="text-primary">{{ $pur->purchase_no }}</strong></td>
                  <td>{{ \Carbon\Carbon::parse($pur->purchase_date)->format('d-M-Y') }}</td>
                  <td><strong>{{ $pur->vendor->name ?? 'N/A' }}</strong></td>
                  <td>
                    <ul class="mb-0 ps-3 small text-muted">
                      @foreach($pur->items as $item)
                        <li>{{ $item->rawMaterial->name ?? 'Item' }}: {{ number_format($item->qty, 2) }} {{ $item->unit }} @ Rs {{ number_format($item->unit_price, 2) }}</li>
                      @endforeach
                    </ul>
                  </td>
                  <td>Rs {{ number_format($pur->subtotal, 2) }}</td>
                  <td class="fw-bold text-dark">Rs {{ number_format($pur->net_amount, 2) }}</td>
                  <td class="text-success fw-bold">Rs {{ number_format($pur->paid_amount, 2) }}</td>
                  <td class="text-danger fw-bold">Rs {{ number_format($pur->due_amount, 2) }}</td>
                  <td>
                    @if($pur->payment_status === 'paid')
                      <span class="rm-badge rm-badge-success">Paid</span>
                    @elseif($pur->payment_status === 'partial')
                      <span class="rm-badge rm-badge-warning">Partial</span>
                    @else
                      <span class="rm-badge rm-badge-danger">Unpaid</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 btn-edit-purchase"
                              data-purchase="{{ json_encode([
                                  'id' => $pur->id,
                                  'purchase_no' => $pur->purchase_no,
                                  'vendor_id' => $pur->vendor_id,
                                  'purchase_date' => $pur->purchase_date,
                                  'discount' => $pur->discount,
                                  'extra_cost' => $pur->extra_cost,
                                  'paid_amount' => $pur->paid_amount,
                                  'note' => $pur->note,
                                  'items' => $pur->items->map(function($item) {
                                      return [
                                          'raw_material_id' => $item->raw_material_id,
                                          'qty' => $item->qty,
                                          'unit_price' => $item->unit_price,
                                          'unit' => $item->unit,
                                          'line_total' => $item->line_total
                                      ];
                                  })
                              ]) }}"
                              title="Edit Purchase">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <a href="{{ route('raw_materials.purchase.invoice', $pur->id) }}" class="btn btn-sm btn-outline-info py-1 px-2" title="Print Invoice">
                        <i class="bi bi-printer"></i>
                      </a>
                      <a href="{{ route('raw_materials.purchase.delete', $pur->id) }}" class="btn btn-sm btn-outline-danger py-1 px-2" onclick="return confirm('Delete this purchase and revert stock/ledger?')" title="Delete">
                        <i class="bi bi-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    <!-- 3. RAW MATERIAL OUT (DC) TAB -->
    <div class="tab-pane fade {{ $activeTab === 'out' ? 'show active' : '' }}" id="tab-out">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-truck me-2 text-primary"></i>Raw Material Out / Issuance (Delivery Challans)</h5>
              <span class="text-muted small">Track materials issued to Kitchen, Bakery, Sweet Factory, or Departments</span>
            </div>
            <button class="rm-btn rm-btn-primary rm-btn-sm" data-bs-toggle="modal" data-bs-target="#outModal" onclick="clearOutForm()">
              <i class="fa fa-plus me-1"></i> New Material Out (DC)
            </button>
          </div>

          <div class="rm-tbl-wrap">
            <table id="outsTable" class="rm-tbl align-middle">
              <thead>
                <tr>
                  <th style="width: 40px;">#</th>
                  <th>DC No</th>
                  <th>Date</th>
                  <th>Issued To / Location</th>
                  <th>Taken By (Person)</th>
                  <th>Items Issued</th>
                  <th class="text-end">Total Value (Rs)</th>
                  <th>Notes / Remarks</th>
                  <th>Created By</th>
                  <th class="text-center" style="width: 150px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($outs as $index => $o)
                @php
                  $dcTotal = (float)($o->total_amount ?: $o->items->sum(function($it) {
                      return ($it->qty ?: 0) * ($it->unit_price ?: ($it->rawMaterial?->unit_price ?? 0));
                  }));
                @endphp
                <tr>
                  <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                  <td>
                    <a href="{{ route('raw_materials.out.dc', $o->id) }}" class="fw-bold text-primary text-decoration-none">
                      {{ $o->issue_no }}
                    </a>
                  </td>
                  <td>{{ \Carbon\Carbon::parse($o->out_date)->format('d-M-Y') }}</td>
                  <td>
                    <span class="badge bg-light text-dark border font-mono">
                      <i class="fa fa-building text-primary me-1"></i>{{ $o->location }}
                    </span>
                  </td>
                  <td>
                    <strong><i class="fa fa-user text-secondary me-1"></i>{{ $o->taken_by }}</strong>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                      <span class="rm-badge rm-badge-success">{{ $o->items->count() }} Item(s)</span>
                      <button type="button" class="btn btn-sm btn-outline-info py-0 px-1 btn-quick-view-out"
                              data-out="{{ json_encode([
                                  'id' => $o->id,
                                  'issue_no' => $o->issue_no,
                                  'out_date' => \Carbon\Carbon::parse($o->out_date)->format('d-M-Y'),
                                  'location' => $o->location,
                                  'taken_by' => $o->taken_by,
                                  'notes' => $o->notes,
                                  'created_by' => $o->creator->name ?? 'Admin',
                                  'total_amount' => $dcTotal,
                                  'items' => $o->items->map(function($it) {
                                      $p = (float)($it->unit_price ?: ($it->rawMaterial?->unit_price ?? 0));
                                      return [
                                          'name' => $it->rawMaterial->name ?? 'Item',
                                          'unit' => $it->unit ?? ($it->rawMaterial?->unit ?? 'KG'),
                                          'qty' => (float)$it->qty,
                                          'unit_price' => $p,
                                          'line_total' => (float)($it->line_total ?: ($it->qty * $p)),
                                          'item_note' => $it->item_note ?? '-'
                                      ];
                                  })
                              ]) }}"
                              title="Quick View Items">
                        <i class="bi bi-eye"></i> View
                      </button>
                    </div>
                  </td>
                  <td class="text-end fw-bold text-dark" style="font-size: .9rem;">
                    Rs {{ number_format($dcTotal, 2) }}
                  </td>
                  <td class="text-muted small">{{ \Illuminate\Support\Str::limit($o->notes ?? '-', 30) }}</td>
                  <td class="text-muted small">{{ $o->creator->name ?? 'Admin' }}</td>
                  <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                      <button type="button" class="btn btn-sm btn-outline-info py-1 px-2 btn-quick-view-out"
                              data-out="{{ json_encode([
                                  'id' => $o->id,
                                  'issue_no' => $o->issue_no,
                                  'out_date' => \Carbon\Carbon::parse($o->out_date)->format('d-M-Y'),
                                  'location' => $o->location,
                                  'taken_by' => $o->taken_by,
                                  'notes' => $o->notes,
                                  'created_by' => $o->creator->name ?? 'Admin',
                                  'total_amount' => $dcTotal,
                                  'items' => $o->items->map(function($it) {
                                      $p = (float)($it->unit_price ?: ($it->rawMaterial?->unit_price ?? 0));
                                      return [
                                          'name' => $it->rawMaterial->name ?? 'Item',
                                          'unit' => $it->unit ?? ($it->rawMaterial?->unit ?? 'KG'),
                                          'qty' => (float)$it->qty,
                                          'unit_price' => $p,
                                          'line_total' => (float)($it->line_total ?: ($it->qty * $p)),
                                          'item_note' => $it->item_note ?? '-'
                                      ];
                                  })
                              ]) }}"
                              title="Quick View">
                        <i class="bi bi-eye"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 btn-edit-out"
                              data-out="{{ json_encode([
                                  'id' => $o->id,
                                  'issue_no' => $o->issue_no,
                                  'out_date' => $o->out_date,
                                  'location' => $o->location,
                                  'taken_by' => $o->taken_by,
                                  'notes' => $o->notes,
                                  'items' => $o->items->map(function($it) {
                                      return [
                                          'raw_material_id' => $it->raw_material_id,
                                          'qty' => (float)$it->qty,
                                          'unit_price' => (float)($it->unit_price ?: ($it->rawMaterial?->unit_price ?? 0)),
                                          'unit' => $it->unit,
                                          'item_note' => $it->item_note
                                      ];
                                  })
                              ]) }}"
                              title="Edit DC">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <a href="{{ route('raw_materials.out.dc', $o->id) }}" class="btn btn-sm btn-outline-dark py-1 px-2" title="Print Delivery Challan (DC)">
                        <i class="bi bi-printer"></i>
                      </a>
                      <a href="{{ route('raw_materials.out.delete', $o->id) }}" class="btn btn-sm btn-outline-danger py-1 px-2" onclick="return confirm('Delete this Material Out DC and revert stock?')" title="Delete">
                        <i class="bi bi-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    <!-- 4. RAW MATERIAL VENDORS TAB -->
    <div class="tab-pane fade {{ $activeTab === 'vendors' ? 'show active' : '' }}" id="tab-vendors">
      <div class="rm-card">
        <div class="rm-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-users me-2 text-purple"></i>Raw Material Vendors Directory</h5>
            <div>
              <button class="rm-btn rm-btn-primary rm-btn-sm" data-bs-toggle="modal" data-bs-target="#vendorModal" onclick="clearVendorForm()">
                <i class="fa fa-user-plus me-1"></i> Add Vendor
              </button>
              <button class="rm-btn rm-btn-success rm-btn-sm" data-bs-toggle="modal" data-bs-target="#vendorPaymentModal">
                <i class="fa fa-money me-1"></i> Record Vendor Payment
              </button>
            </div>
          </div>

          <div class="rm-tbl-wrap">
            <table id="vendorsTable" class="rm-tbl align-middle">
              <thead>
                <tr>
                  <th style="width: 45px;">#</th>
                  <th>Vendor Name</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Opening Balance</th>
                  <th>Current Closing Balance</th>
                  <th>Address</th>
                  <th style="width: 140px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($vendors as $index => $v)
                <tr>
                  <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                  <td><strong class="text-dark" style="font-size: .9rem;">{{ $v->name }}</strong></td>
                  <td>{{ $v->phone ?? '-' }}</td>
                  <td>{{ $v->email ?? '-' }}</td>
                  <td class="text-muted">Rs {{ number_format($v->opening_balance, 2) }}</td>
                  <td>
                    <span class="fw-bold {{ $v->closing_balance > 0 ? 'text-danger' : 'text-success' }}" style="font-size: .95rem;">
                      Rs {{ number_format($v->closing_balance, 2) }}
                    </span>
                  </td>
                  <td class="text-muted small">{{ Str::limit($v->address, 30) }}</td>
                  <td>
                    <div class="d-flex gap-1">
                      <button type="button" class="btn btn-sm btn-outline-success py-1 px-2 btn-pay-vendor"
                              data-id="{{ $v->id }}"
                              data-name="{{ $v->name }}"
                              data-balance="{{ $v->closing_balance }}"
                              title="Record Payment">
                        <i class="bi bi-cash-stack me-1"></i> Pay
                      </button>
                      <a href="{{ route('raw_materials.index', ['tab' => 'ledger', 'ledger_vendor_id' => $v->id]) }}" class="btn btn-sm btn-outline-info py-1 px-2" title="View Ledger">
                        <i class="bi bi-journal-text me-1"></i> Ledger
                      </a>
                      <button class="btn btn-sm btn-outline-primary py-1 px-2 btn-edit-vendor"
                              data-id="{{ $v->id }}"
                              data-name="{{ $v->name }}"
                              data-phone="{{ $v->phone }}"
                              data-email="{{ $v->email }}"
                              data-address="{{ $v->address }}"
                              data-opening="{{ $v->opening_balance }}"
                              title="Edit Vendor">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <a href="{{ route('raw_materials.vendor.delete', $v->id) }}" class="btn btn-sm btn-outline-danger py-1 px-2" onclick="return confirm('Delete this vendor?')" title="Delete">
                        <i class="bi bi-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    <!-- 4. VENDOR LEDGER TAB -->
    <div class="tab-pane fade {{ $activeTab === 'ledger' ? 'show active' : '' }}" id="tab-ledger">
      <div class="rm-card">
        <div class="rm-card-body">
          
          <!-- FILTER FORM -->
          <form action="{{ route('raw_materials.index') }}" method="GET" class="row g-2 mb-4 p-3 bg-light rounded border align-items-end no-print">
            <input type="hidden" name="tab" value="ledger">
            <div class="col-md-4">
              <label class="rm-lbl">Select Vendor</label>
              <select name="ledger_vendor_id" class="rm-fld" onchange="this.form.submit()">
                <option value="">-- All Vendors Ledger --</option>
                @foreach($vendors as $v)
                  <option value="{{ $v->id }}" {{ $selectedVendorId == $v->id ? 'selected' : '' }}>
                    {{ $v->name }} (Balance: Rs {{ number_format($v->closing_balance, 0) }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="rm-lbl">From Date</label>
              <input type="date" name="ledger_date_from" value="{{ $ledgerDateFrom }}" class="rm-fld">
            </div>
            <div class="col-md-2">
              <label class="rm-lbl">To Date</label>
              <input type="date" name="ledger_date_to" value="{{ $ledgerDateTo }}" class="rm-fld">
            </div>
            <div class="col-md-4 d-flex gap-2">
              <button type="submit" class="btn btn-sm btn-primary fw-bold flex-fill"><i class="fa fa-filter me-1"></i> Filter</button>
              <button type="button" onclick="printVendorLedgerDirect()" class="btn btn-sm btn-dark fw-bold" title="Print 80mm Thermal Receipt"><i class="bi bi-printer me-1"></i> Print (80mm)</button>
              <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary fw-bold" title="Print Standard A4"><i class="bi bi-file-earmark-pdf"></i></button>
            </div>
          </form>
          <iframe id="printLedgerIframe" style="display:none; width:0; height:0; border:none;"></iframe>

          <!-- LEDGER STATEMENT HEADER -->
          <div id="print-area">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
              <div>
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-book me-2 text-primary"></i>Raw Material Vendor Ledger Statement</h5>
                @if($selectedVendor)
                  <span class="text-muted">Vendor: <strong>{{ $selectedVendor->name }}</strong> | Phone: {{ $selectedVendor->phone ?? 'N/A' }}</span>
                @else
                  <span class="text-muted">Showing all vendor ledger transactions</span>
                @endif
              </div>
              @if($selectedVendor)
              <div class="text-end">
                <span class="rm-lbl">Current Payable Balance:</span>
                <h4 class="fw-bold {{ $selectedVendor->closing_balance > 0 ? 'text-danger' : 'text-success' }} mb-0">
                  Rs {{ number_format($selectedVendor->closing_balance, 2) }}
                </h4>
              </div>
              @endif
            </div>

            <div class="rm-tbl-wrap">
              <table id="ledgerTable" class="rm-tbl align-middle">
                <thead>
                  <tr>
                    <th style="width: 45px;">#</th>
                    <th>Date</th>
                    <th>Vendor</th>
                    <th>Reference / Inv #</th>
                    <th>Description</th>
                    <th class="text-end">Credit (Bill +)</th>
                    <th class="text-end">Debit (Paid -)</th>
                    <th class="text-end">Balance (Rs)</th>
                  </tr>
                </thead>
                <tbody>
                  @php $runningTotal = 0; @endphp
                  @forelse($ledgers as $index => $leg)
                  @php $runningTotal = $leg->running_balance; @endphp
                  <tr>
                    <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($leg->date)->format('d-M-Y') }}</td>
                    <td><strong>{{ $leg->vendor->name ?? 'N/A' }}</strong></td>
                    <td><span class="badge bg-light text-dark border">{{ $leg->reference_no ?? '-' }}</span></td>
                    <td>{{ $leg->description }}</td>
                    <td class="text-end fw-bold text-danger">
                      {{ $leg->credit > 0 ? 'Rs '.number_format($leg->credit, 2) : '-' }}
                    </td>
                    <td class="text-end fw-bold text-success">
                      {{ $leg->debit > 0 ? 'Rs '.number_format($leg->debit, 2) : '-' }}
                    </td>
                    <td class="text-end fw-bold {{ $leg->running_balance > 0 ? 'text-danger' : 'text-success' }}">
                      Rs {{ number_format($leg->running_balance, 2) }}
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No ledger transactions found for the selected filter.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- 6. RAW MATERIAL STOCK REPORT TAB -->
    <div class="tab-pane fade {{ $activeTab === 'stock_report' ? 'show active' : '' }}" id="tab-stock-report" role="tabpanel">
      <div class="rm-card">
        <div class="rm-card-hdr bg-white border-bottom p-3">
          <form action="{{ route('raw_materials.index') }}" method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="tab" value="stock_report">
            <div class="col-md-3 col-6">
              <label class="small text-muted mb-1 fw-bold">From Date</label>
              <input type="date" name="stock_date_from" value="{{ $stockDateFrom }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 col-6">
              <label class="small text-muted mb-1 fw-bold">To Date</label>
              <input type="date" name="stock_date_to" value="{{ $stockDateTo }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 col-8">
              <label class="small text-muted mb-1 fw-bold">Filter Material</label>
              <select name="stock_material_id" class="form-select form-select-sm">
                <option value="">-- All Raw Materials --</option>
                @foreach($materials as $m)
                  <option value="{{ $m->id }}" {{ $stockMaterialId == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 col-4 d-flex gap-1">
              <button type="submit" class="btn btn-sm btn-primary fw-bold flex-fill"><i class="bi bi-filter me-1"></i> Filter</button>
              <a href="{{ route('raw_materials.index', ['tab' => 'stock_report']) }}" class="btn btn-sm btn-outline-secondary fw-bold" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
          </form>
        </div>

        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <i class="bi bi-search text-muted"></i>
            <input type="text" id="stockSearchInput" onkeyup="filterStockTable()" class="form-control form-control-sm" placeholder="Search by material name or item code...">
          </div>
          <div class="d-flex gap-2 align-items-center">
            <button type="button" onclick="printSelectedStockReport()" class="btn btn-sm btn-outline-dark fw-bold px-3">
              <i class="bi bi-check2-square me-1"></i> Print Selected Items (80mm)
            </button>
            <button type="button" onclick="printStockReportDirect()" class="btn btn-sm btn-dark fw-bold px-3">
              <i class="bi bi-printer me-1"></i> Print Thermal Report (80mm)
            </button>
            <iframe id="printStockIframe" style="display:none; width:0; height:0; border:none;"></iframe>
          </div>
        </div>

        <div class="rm-card-body">
          <div class="table-responsive">
            <table class="table rm-table align-middle" id="stockReportTable">
              <thead>
                <tr>
                  <th style="width: 35px;" class="text-center">
                    <input type="checkbox" id="selectAllStockCheck" onclick="toggleAllStockChecks(this)">
                  </th>
                  <th>#</th>
                  <th>Item Code</th>
                  <th>Material Name</th>
                  <th class="text-center">Unit</th>
                  <th class="text-end">Initial Stock</th>
                  <th class="text-end text-success">Purchased (In)</th>
                  <th class="text-end text-danger">Issued (Out)</th>
                  <th class="text-end fw-bold text-primary">Available Stock</th>
                  <th class="text-end">Unit Cost (Rs)</th>
                  <th class="text-end fw-bold">Stock Value (Rs)</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $totInit = 0; $totPurch = 0; $totOut = 0; $totAvail = 0; $totVal = 0;
                @endphp
                @foreach($stockReportData as $idx => $r)
                @php
                  $totInit += $r->initial_stock;
                  $totPurch += $r->purchased_qty;
                  $totOut += $r->out_qty;
                  $totAvail += $r->stock_qty;
                  $totVal += $r->stock_value;
                @endphp
                <tr class="stock-row">
                  <td class="text-center">
                    <input type="checkbox" class="stock-item-check" value="{{ $r->id }}">
                  </td>
                  <td class="text-muted small">{{ $idx + 1 }}</td>
                  <td><code class="bg-light px-2 py-1 rounded fw-bold text-dark">{{ $r->item_code }}</code></td>
                  <td class="fw-bold">{{ $r->name }}</td>
                  <td class="text-center"><span class="badge bg-secondary">{{ $r->unit }}</span></td>
                  <td class="text-end font-monospace">{{ number_format($r->initial_stock, 2) }}</td>
                  <td class="text-end font-monospace text-success">+{{ number_format($r->purchased_qty, 2) }}</td>
                  <td class="text-end font-monospace text-danger">-{{ number_format($r->out_qty, 2) }}</td>
                  <td class="text-end font-monospace fw-bold {{ $r->stock_qty <= $r->alert_qty ? 'text-danger' : 'text-primary' }}">
                    {{ number_format($r->stock_qty, 2) }}
                    @if($r->stock_qty <= $r->alert_qty)
                      <small class="badge bg-danger ms-1">Low</small>
                    @endif
                  </td>
                  <td class="text-end font-monospace">Rs {{ number_format($r->unit_price, 2) }}</td>
                  <td class="text-end font-monospace fw-bold">Rs {{ number_format($r->stock_value, 2) }}</td>
                </tr>
                @endforeach
              </tbody>
              <tfoot class="table-dark">
                <tr>
                  <td colspan="5" class="text-end fw-bold">TOTALS:</td>
                  <td class="text-end font-monospace fw-bold">{{ number_format($totInit, 2) }}</td>
                  <td class="text-end font-monospace fw-bold text-success">+{{ number_format($totPurch, 2) }}</td>
                  <td class="text-end font-monospace fw-bold text-danger">-{{ number_format($totOut, 2) }}</td>
                  <td class="text-end font-monospace fw-bold text-info">{{ number_format($totAvail, 2) }}</td>
                  <td></td>
                  <td class="text-end font-monospace fw-bold text-warning">Rs {{ number_format($totVal, 2) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. ADD / EDIT RAW MATERIAL MODAL (LARGE / MULTI-ROW) -->
<div class="modal fade" id="materialModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white py-2 px-3">
        <div class="d-flex align-items-center gap-2">
          <div style="background: rgba(255,255,255,0.15); width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-cubes text-info"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="materialModalTitle" style="font-size: 1.05rem;"><i class="fa fa-cubes me-2"></i>Add Raw Material(s)</h5>
            <small class="text-white-50" id="materialModalSubtitle" style="font-size: 0.75rem;">Add one or multiple raw materials. Press <strong>Enter</strong> to quickly add next row.</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('raw_materials.material.store') }}" method="POST" id="materialForm">
        @csrf
        <input type="hidden" name="id" id="mat_id">
        <div class="modal-body p-3">

          <!-- A. MULTI-ROW BULK ADD SECTION -->
          <div id="multiMaterialSection">
            <div class="table-responsive" style="border: 1px solid #e2e8f0; border-radius: 8px; max-height: 480px; overflow-y: auto;">
              <table class="table table-bordered table-hover align-middle mb-0" id="rawMaterialTable" style="font-size: 0.85rem;">
                <thead class="table-light text-muted sticky-top" style="z-index: 2; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                  <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th style="min-width: 250px;">Material Name <span class="text-danger">*</span></th>
                    <th style="min-width: 125px;">Unit <span class="text-danger">*</span></th>
                    <th style="min-width: 130px;" class="text-end">Opening Stock</th>
                    <th style="min-width: 130px;" class="text-end">Cost Rate (Rs)</th>
                    <th style="min-width: 120px;" class="text-end">Alert Qty</th>
                    <th style="min-width: 180px;">Notes (Optional)</th>
                    <th class="text-center" style="width: 85px;">Action</th>
                  </tr>
                </thead>
                <tbody id="rawMaterialRows">
                  <!-- Dynamic rows rendered via JavaScript -->
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMaterialRow()" style="font-weight: 600;">
                <i class="fa fa-plus me-1"></i> Add Another Row (or Press Enter)
              </button>
              <div class="text-muted small d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border"><i class="bi bi-keyboard me-1"></i> <strong>Enter</strong> = New Row</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-plus-lg text-success me-1"></i> = Add Row Below</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-x-lg text-danger me-1"></i> = Remove</span>
              </div>
            </div>
          </div>

          <!-- B. SINGLE EDIT SECTION -->
          <div id="singleMaterialSection" style="display: none;">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="rm-lbl">Material Name <span class="text-danger">*</span></label>
                <input type="text" name="single_name" id="mat_name" class="rm-fld" placeholder="e.g. Sugar, Flour, Ghee, Packaging Box">
              </div>
              <div class="col-md-3">
                <label class="rm-lbl">Unit <span class="text-danger">*</span></label>
                <select name="single_unit" id="mat_unit" class="rm-fld">
                  <option value="KG">KG</option>
                  <option value="Gram">Gram</option>
                  <option value="Ltr">Ltr</option>
                  <option value="Pc">Pc</option>
                  <option value="Bag">Bag</option>
                  <option value="Pack">Pack</option>
                  <option value="Dozen">Dozen</option>
                  <option value="Box">Box</option>
                  <option value="Pound">Pound</option>
                  <option value="Tin">Tin</option>
                  <option value="Carton">Carton</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="rm-lbl">Current Stock</label>
                <input type="number" step="any" name="single_stock_qty" id="mat_stock" class="rm-fld" value="0">
              </div>
              <div class="col-md-4">
                <label class="rm-lbl">Unit Cost Rate (Rs)</label>
                <input type="number" step="any" name="single_unit_price" id="mat_price" class="rm-fld" value="0">
              </div>
              <div class="col-md-4">
                <label class="rm-lbl">Low Stock Alert Qty</label>
                <input type="number" step="any" name="single_alert_qty" id="mat_alert" class="rm-fld" value="10">
              </div>
              <div class="col-md-4">
                <label class="rm-lbl">Notes</label>
                <input type="text" name="single_note" id="mat_note" class="rm-fld" placeholder="Optional notes">
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="materialSubmitBtn">
            <i class="fa fa-save me-1"></i> Save Material(s)
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 2. NEW / EDIT RAW MATERIAL PURCHASE MODAL -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title fw-bold" id="purchaseModalTitle"><i class="fa fa-shopping-cart me-2"></i>New Raw Material Purchase</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('raw_materials.purchase.store') }}" method="POST" id="purchaseForm">
        @csrf
        <input type="hidden" name="id" id="pur_id">
        <div class="modal-body">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="rm-lbl">Vendor <span class="text-danger">*</span></label>
              <select name="vendor_id" id="pur_vendor_id" class="rm-fld" required>
                <option value="">-- Select Material Vendor --</option>
                @foreach($vendors as $v)
                  <option value="{{ $v->id }}">{{ $v->name }} (Balance: Rs {{ number_format($v->closing_balance, 0) }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="rm-lbl">Purchase Date <span class="text-danger">*</span></label>
              <input type="date" name="purchase_date" id="pur_date" value="{{ date('Y-m-d') }}" class="rm-fld" required>
            </div>
          </div>

          <!-- ITEMS TABLE -->
          <div class="border rounded p-3 mb-3 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <strong class="text-dark" style="font-size: .88rem;"><i class="fa fa-cubes me-1"></i> Purchase Items List</strong>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPurchaseRow()">
                <i class="fa fa-plus me-1"></i> Add Material Row
              </button>
            </div>

            <table class="table table-sm table-bordered bg-white" id="purchaseItemsTable">
              <thead class="table-light text-muted small uppercase">
                <tr>
                  <th style="width: 45%;">Raw Material Item</th>
                  <th style="width: 20%;">Qty</th>
                  <th style="width: 25%;">Unit Price (Rs)</th>
                  <th style="width: 25%;">Total (Rs)</th>
                  <th style="width: 10%;"></th>
                </tr>
              </thead>
              <tbody id="purchaseItemRows">
                <!-- Initial Row -->
                <tr class="pur-row">
                  <td>
                    <select name="raw_material_id[]" class="rm-fld mat-select" onchange="updateRowUnit(this)" required>
                      <option value="">-- Select Material --</option>
                      @foreach($materials as $m)
                        <option value="{{ $m->id }}" data-unit="{{ $m->unit }}" data-price="{{ $m->unit_price }}">{{ $m->name }} ({{ $m->unit }})</option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <input type="number" step="any" name="qty[]" class="rm-fld mat-qty" placeholder="Qty" oninput="calcPurchaseTotals()" required>
                  </td>
                  <td>
                    <input type="number" step="any" name="unit_price[]" class="rm-fld mat-price" placeholder="Rate" oninput="calcPurchaseTotals()" required>
                  </td>
                  <td>
                    <input type="number" step="any" class="rm-fld mat-total" readonly value="0.00">
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removePurchaseRow(this)"><i class="fa fa-times"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- TOTALS SUMMARY -->
          <div class="row g-3">
            <div class="col-md-3">
              <label class="rm-lbl">Subtotal (Rs)</label>
              <input type="number" step="any" id="pur_subtotal" class="rm-fld bg-light fw-bold" readonly value="0">
            </div>
            <div class="col-md-3">
              <label class="rm-lbl">Discount (Rs)</label>
              <input type="number" step="any" name="discount" id="pur_discount" class="rm-fld text-danger" value="0" oninput="calcPurchaseTotals()">
            </div>
            <div class="col-md-3">
              <label class="rm-lbl">Freight / Extra Cost</label>
              <input type="number" step="any" name="extra_cost" id="pur_extra" class="rm-fld text-primary" value="0" oninput="calcPurchaseTotals()">
            </div>
            <div class="col-md-3">
              <label class="rm-lbl">Net Total (Rs)</label>
              <input type="number" step="any" id="pur_net" class="rm-fld fw-bold text-dark bg-warning bg-opacity-10" readonly value="0">
            </div>

            <div class="col-md-4">
              <label class="rm-lbl">Paid Amount</label>
              <input type="number" step="any" name="paid_amount" id="pur_paid" class="rm-fld text-success fw-bold" value="0" oninput="calcPurchaseTotals()">
            </div>
            <div class="col-md-4">
              <label class="rm-lbl">Remaining Due</label>
              <input type="number" step="any" id="pur_due" class="rm-fld text-danger fw-bold bg-light" readonly value="0">
            </div>
            <div class="col-md-4">
              <label class="rm-lbl">Note / Remarks</label>
              <input type="text" name="note" id="pur_note" class="rm-fld" placeholder="Optional reference or vehicle #">
            </div>
          </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="purchaseSubmitBtn"><i class="fa fa-check-circle me-1"></i> Save Purchase Order</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 2.5 NEW / EDIT RAW MATERIAL OUT (DC) MODAL -->
<div class="modal fade" id="outModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white py-2 px-3">
        <div class="d-flex align-items-center gap-2">
          <div style="background: rgba(255,255,255,0.15); width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-truck text-warning"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="outModalTitle" style="font-size: 1.05rem;"><i class="fa fa-truck me-2"></i>New Raw Material Out (Delivery Challan)</h5>
            <small class="text-white-50" id="outModalSub" style="font-size: .75rem;">Issue raw materials to production/kitchen with cost tracking. Press <strong>Enter</strong> for next row.</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('raw_materials.out.store') }}" method="POST" id="outForm">
        @csrf
        <input type="hidden" name="id" id="out_id">
        <div class="modal-body p-3">
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="rm-lbl">Location / Department <span class="text-danger">*</span></label>
              <input type="text" name="location" id="out_location" list="locationSuggestions" class="rm-fld" placeholder="e.g. Kitchen, Bakery" required autocomplete="off">
              <datalist id="locationSuggestions">
                <option value="Kitchen (Mithai Section)">
                <option value="Bakery & Cakes Section">
                <option value="Sweet Factory (Main)">
                <option value="Nimko / Snacks Unit">
                <option value="Packaging & Box Unit">
                <option value="Main Counter / Retail Display">
              </datalist>
            </div>
            <div class="col-md-4">
              <label class="rm-lbl">Taken By (Person Name) <span class="text-danger">*</span></label>
              <input type="text" name="taken_by" id="out_taken_by" class="rm-fld" placeholder="e.g. Chef Aslam, Master Zubair, Ali Staff" required autocomplete="off">
            </div>
            <div class="col-md-4">
              <label class="rm-lbl">Issue Date <span class="text-danger">*</span></label>
              <input type="date" name="out_date" id="out_date" class="rm-fld" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-12">
              <label class="rm-lbl">Notes / Purpose (Optional)</label>
              <input type="text" name="notes" id="out_notes" class="rm-fld" placeholder="e.g. Issued for Gulab Jamun & Barfi Batch 4">
            </div>
          </div>

          <!-- DYNAMIC OUT ITEMS TABLE -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-list me-1 text-primary"></i>Raw Material Items to Issue Out</h6>
            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="addOutRow()">
              <i class="fa fa-plus me-1"></i> Add Material Row (or press Enter)
            </button>
          </div>

          <div class="table-responsive border rounded mb-3" style="max-height: 380px; overflow-y: auto;">
            <table class="table table-bordered table-hover align-middle mb-0" id="outItemsTable" style="font-size: 0.84rem;">
              <thead class="table-light sticky-top text-muted" style="z-index: 2; font-size: 0.75rem; text-transform: uppercase;">
                <tr>
                  <th style="width: 35px;" class="text-center">#</th>
                  <th style="min-width: 250px;">Raw Material Item <span class="text-danger">*</span></th>
                  <th style="min-width: 95px;" class="text-center">Stock</th>
                  <th style="min-width: 140px;">Qty Issued <span class="text-danger">*</span></th>
                  <th style="min-width: 120px;" class="text-end">Cost Rate (Rs)</th>
                  <th style="min-width: 130px;" class="text-end">Total Cost (Rs)</th>
                  <th style="min-width: 150px;">Purpose / Note</th>
                  <th class="text-center" style="width: 80px;">Action</th>
                </tr>
              </thead>
              <tbody id="outItemRows">
                <!-- Out rows -->
              </tbody>
            </table>
          </div>

          <!-- SUMMARY STRIP -->
          <div class="row g-2 bg-light p-2 rounded border align-middle">
            <div class="col-md-4 d-flex align-items-center">
              <span class="text-muted small me-2">Total Items:</span>
              <strong id="out_total_items_badge" class="badge bg-secondary">0</strong>
            </div>
            <div class="col-md-4 text-center">
              <span class="text-muted small me-2">Total Qty:</span>
              <strong id="out_total_qty_badge" class="text-dark">0.00</strong>
            </div>
            <div class="col-md-4 text-end">
              <span class="text-muted small me-2">Total Out Value:</span>
              <strong id="out_total_val_badge" class="text-primary fs-6 fw-bold">Rs 0.00</strong>
            </div>
          </div>

        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="outSubmitBtn"><i class="fa fa-check me-1"></i> Issue Material Out & Save DC</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- QUICK VIEW RAW MATERIAL OUT (DC) MODAL -->
<div class="modal fade" id="outQuickViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white py-2 px-3">
        <div class="d-flex align-items-center gap-2">
          <i class="fa fa-file-text-o text-info fs-5"></i>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="qv_issue_no" style="font-size: 1.05rem;">Delivery Challan Details</h5>
            <small class="text-white-50" id="qv_out_date">Date</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-3">
        <div class="row g-2 mb-3 p-2 bg-light border rounded">
          <div class="col-md-4">
            <span class="text-muted small d-block">Issued To / Location:</span>
            <strong id="qv_location" class="text-dark">-</strong>
          </div>
          <div class="col-md-4">
            <span class="text-muted small d-block">Taken By (Person):</span>
            <strong id="qv_taken_by" class="text-dark">-</strong>
          </div>
          <div class="col-md-4">
            <span class="text-muted small d-block">Created By:</span>
            <strong id="qv_created_by" class="text-dark">-</strong>
          </div>
          <div class="col-md-12 mt-1" id="qv_notes_wrap">
            <span class="text-muted small d-block">Notes:</span>
            <span id="qv_notes" class="text-secondary">-</span>
          </div>
        </div>

        <h6 class="fw-bold text-dark mb-2"><i class="fa fa-cubes text-primary me-1"></i> Issued Items List</h6>
        <div class="table-responsive border rounded mb-3">
          <table class="table table-bordered table-sm mb-0 align-middle">
            <thead class="table-light text-muted small uppercase">
              <tr>
                <th style="width: 35px;" class="text-center">#</th>
                <th>Material Name</th>
                <th class="text-center" style="width: 80px;">Unit</th>
                <th class="text-end" style="width: 110px;">Qty Issued</th>
                <th class="text-end" style="width: 120px;">Cost Rate (Rs)</th>
                <th class="text-end" style="width: 130px;">Total Value (Rs)</th>
                <th style="width: 140px;">Purpose / Note</th>
              </tr>
            </thead>
            <tbody id="qv_items_body">
              <!-- Rendered via JS -->
            </tbody>
            <tfoot class="table-light fw-bold">
              <tr>
                <td colspan="3" class="text-end">Grand Total:</td>
                <td class="text-end" id="qv_foot_qty">0.00</td>
                <td></td>
                <td class="text-end text-primary" id="qv_foot_amount">Rs 0.00</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <a href="#" id="qv_print_link" class="btn btn-dark btn-sm fw-bold px-3" target="_blank"><i class="bi bi-printer me-1"></i> Print Delivery Challan</a>
      </div>
    </div>
  </div>
</div>

<!-- 3. ADD / EDIT VENDOR MODAL -->
<div class="modal fade" id="vendorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title fw-bold" id="vendorModalTitle"><i class="fa fa-user-plus me-2"></i>Add Material Vendor</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('raw_materials.vendor.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id" id="v_id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="rm-lbl">Vendor Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="v_name" class="rm-fld" placeholder="e.g. Al-Madina Flour Mills" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="rm-lbl">Phone Number</label>
              <input type="text" name="phone" id="v_phone" class="rm-fld" placeholder="0300-1234567">
            </div>
            <div class="col-md-6">
              <label class="rm-lbl">Email</label>
              <input type="email" name="email" id="v_email" class="rm-fld" placeholder="vendor@email.com">
            </div>
          </div>
          <div class="mb-3">
            <label class="rm-lbl">Opening Balance (Rs)</label>
            <input type="number" step="any" name="opening_balance" id="v_opening" class="rm-fld" placeholder="0.00">
            <span class="text-muted small">Positive means vendor is payable.</span>
          </div>
          <div class="mb-3">
            <label class="rm-lbl">Address</label>
            <textarea name="address" id="v_address" class="rm-fld" rows="2" placeholder="Vendor address"></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm px-4">Save Vendor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 4. RECORD VENDOR PAYMENT MODAL -->
<div class="modal fade" id="vendorPaymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white py-2 px-3">
        <div class="d-flex align-items-center gap-2">
          <i class="fa fa-money fs-5"></i>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="vendorPaymentModalTitle" style="font-size: 1.05rem;">Record Vendor Payment</h5>
            <small class="text-white-50" id="vendorPaymentModalSub" style="font-size: .75rem;">Pay vendor and adjust closing balance</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('raw_materials.vendor_payment.store') }}" method="POST" id="vendorPaymentForm">
        @csrf
        <div class="modal-body p-3">
          <div class="mb-3">
            <label class="rm-lbl">Select Vendor <span class="text-danger">*</span></label>
            <select name="vendor_id" id="pay_vendor_id" class="rm-fld" required>
              <option value="">-- Select Vendor --</option>
              @foreach($vendors as $v)
                <option value="{{ $v->id }}">{{ $v->name }} (Payable: Rs {{ number_format($v->closing_balance, 2) }})</option>
              @endforeach
            </select>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="rm-lbl">Payment Date <span class="text-danger">*</span></label>
              <input type="date" name="date" id="pay_date" value="{{ date('Y-m-d') }}" class="rm-fld" required>
            </div>
            <div class="col-md-6">
              <label class="rm-lbl">Amount Paid (Rs) <span class="text-danger">*</span></label>
              <input type="number" step="any" min="0.01" name="amount" id="pay_amount" class="rm-fld fw-bold text-success" placeholder="0.00" required>
            </div>
          </div>
          <div class="mb-2">
            <label class="rm-lbl">Note / Remarks (Optional)</label>
            <input type="text" name="note" id="pay_note" class="rm-fld" placeholder="e.g. Cash payment / remarks">
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm px-4 fw-bold"><i class="fa fa-check me-1"></i> Submit Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
  var dtConfig = {
    responsive: true,
    pageLength: 25,
    order: [],
    autoWidth: false,
    columnDefs: [{ targets: '_all', className: 'dt-head-left' }]
  };

  $('#materialsTable').DataTable(dtConfig);
  $('#purchasesTable').DataTable(dtConfig);
  $('#outsTable').DataTable(dtConfig);
  $('#vendorsTable').DataTable(dtConfig);
  $('#ledgerTable').DataTable({
    responsive: true,
    pageLength: 50,
    order: [],
    autoWidth: false
  });

  // Automatically adjust DataTables columns when switching tabs
  $('button[data-bs-toggle="tab"], button[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    var targetTab = $(e.target).attr('id').replace('-tab', '');
    if (history.pushState) {
      var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + targetTab;
      window.history.replaceState({ path: newUrl }, '', newUrl);
    }
    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
  });

  // Material edit modal populate
  $(document).on('click', '.btn-edit-material', function() {
    $('#mat_id').val($(this).data('id'));
    $('#mat_name').val($(this).data('name'));
    $('#mat_unit').val($(this).data('unit'));
    $('#mat_price').val($(this).data('price'));
    $('#mat_stock').val($(this).data('stock'));
    $('#mat_alert').val($(this).data('alert'));
    $('#mat_note').val($(this).data('note'));

    $('#materialModalTitle').html('<i class="fa fa-pencil me-2"></i>Edit Raw Material');
    $('#materialModalSubtitle').hide();
    $('#multiMaterialSection').hide();
    $('#singleMaterialSection').show();
    $('#materialSubmitBtn').html('<i class="fa fa-save me-1"></i> Update Material');

    new bootstrap.Modal(document.getElementById('materialModal')).show();
    setTimeout(function() {
      $('#mat_name').focus();
    }, 300);
  });

  // Vendor edit modal populate
  $(document).on('click', '.btn-edit-vendor', function() {
    $('#v_id').val($(this).data('id'));
    $('#v_name').val($(this).data('name'));
    $('#v_phone').val($(this).data('phone'));
    $('#v_email').val($(this).data('email'));
    $('#v_address').val($(this).data('address'));
    $('#v_opening').val($(this).data('opening'));
    $('#vendorModalTitle').html('<i class="fa fa-pencil me-2"></i>Edit Material Vendor');
    new bootstrap.Modal(document.getElementById('vendorModal')).show();
  });

  // Vendor quick pay button
  $(document).on('click', '.btn-pay-vendor', function() {
    const vendorId = $(this).data('id');
    const vendorName = $(this).data('name');
    const balance = $(this).data('balance');

    $('#pay_vendor_id').val(vendorId);
    $('#vendorPaymentModalTitle').html('<i class="fa fa-money me-2"></i>Pay to ' + vendorName);
    $('#vendorPaymentModalSub').text('Payable Balance: Rs ' + Number(balance || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#pay_amount').val('');
    $('#pay_note').val('');

    new bootstrap.Modal(document.getElementById('vendorPaymentModal')).show();
    setTimeout(function() {
      $('#pay_amount').focus();
    }, 350);
  });

  // Purchase edit modal populate
  $(document).on('click', '.btn-edit-purchase', function() {
    const data = $(this).data('purchase');
    if (!data) return;

    $('#pur_id').val(data.id);
    $('#purchaseModalTitle').html('<i class="fa fa-pencil me-2"></i>Edit Purchase #' + (data.purchase_no || ''));
    $('#pur_vendor_id').val(data.vendor_id);
    $('#pur_date').val(data.purchase_date);
    $('#pur_discount').val(data.discount || 0);
    $('#pur_extra').val(data.extra_cost || 0);
    $('#pur_paid').val(data.paid_amount || 0);
    $('#pur_note').val(data.note || '');
    $('#purchaseSubmitBtn').html('<i class="fa fa-save me-1"></i> Update Purchase Order');

    let rowsHtml = '';
    if (data.items && data.items.length > 0) {
      data.items.forEach(function(item) {
        let optHtml = '<option value="">-- Select Material --</option>';
        @foreach($materials as $m)
          var isSel = (item.raw_material_id == {{ $m->id }}) ? 'selected' : '';
          optHtml += '<option value="{{ $m->id }}" data-unit="{{ $m->unit }}" data-price="{{ $m->unit_price }}" ' + isSel + '>{{ $m->name }} ({{ $m->unit }})</option>';
        @endforeach

        var lineTot = (item.line_total ? parseFloat(item.line_total) : (parseFloat(item.qty || 0) * parseFloat(item.unit_price || 0))).toFixed(2);

        rowsHtml += `
          <tr class="pur-row">
            <td>
              <select name="raw_material_id[]" class="rm-fld mat-select" onchange="updateRowUnit(this)" required style="width:100%;">
                ${optHtml}
              </select>
            </td>
            <td><input type="number" step="any" name="qty[]" class="rm-fld mat-qty" placeholder="Qty" value="${item.qty}" oninput="calcPurchaseTotals()" required></td>
            <td><input type="number" step="any" name="unit_price[]" class="rm-fld mat-price" placeholder="Rate" value="${item.unit_price}" oninput="calcPurchaseTotals()" required></td>
            <td><input type="number" step="any" class="rm-fld mat-total" readonly value="${lineTot}"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removePurchaseRow(this)"><i class="fa fa-times"></i></button></td>
          </tr>
        `;
      });
    }

    if (rowsHtml) {
      $('#purchaseItemRows').html(rowsHtml);
    }
    calcPurchaseTotals();
    new bootstrap.Modal(document.getElementById('purchaseModal')).show();
  });

  // Delegate input events for dynamically added purchase rows
  $(document).on('input', '.mat-qty, .mat-price', function() {
    calcPurchaseTotals();
  });

  // Material Out (DC) edit modal populate
  $(document).on('click', '.btn-edit-out', function() {
    const data = $(this).data('out');
    if (!data) return;

    $('#out_id').val(data.id);
    $('#outModalTitle').html('<i class="fa fa-pencil me-2"></i>Edit Delivery Challan #' + (data.issue_no || ''));
    $('#out_location').val(data.location || '');
    $('#out_taken_by').val(data.taken_by || '');
    $('#out_date').val(data.out_date || '');
    $('#out_notes').val(data.notes || '');
    $('#outSubmitBtn').html('<i class="fa fa-save me-1"></i> Update Delivery Challan');

    let rowsHtml = '';
    if (data.items && data.items.length > 0) {
      data.items.forEach(function(item, idx) {
        rowsHtml += getOutRowHtml(idx + 1, item.raw_material_id, item.qty, item.unit_price, item.item_note);
      });
    }

    if (rowsHtml) {
      $('#outItemRows').html(rowsHtml);
    } else {
      $('#outItemRows').html(getOutRowHtml(1));
    }
    calcOutTotals();
    new bootstrap.Modal(document.getElementById('outModal')).show();
  });

  // Material Out Quick View Modal
  $(document).on('click', '.btn-quick-view-out', function() {
    const data = $(this).data('out');
    if (!data) return;

    $('#qv_issue_no').html('<i class="fa fa-truck text-warning me-2"></i>DC: ' + (data.issue_no || '-'));
    $('#qv_out_date').text('Date: ' + (data.out_date || '-'));
    $('#qv_location').text(data.location || '-');
    $('#qv_taken_by').text(data.taken_by || '-');
    $('#qv_created_by').text(data.created_by || 'Admin');
    $('#qv_notes').text(data.notes || 'None');

    let itemsHtml = '';
    let totalQty = 0;
    let totalAmt = 0;

    if (data.items && data.items.length > 0) {
      data.items.forEach(function(it, idx) {
        totalQty += parseFloat(it.qty || 0);
        totalAmt += parseFloat(it.line_total || 0);
        itemsHtml += `
          <tr>
            <td class="text-center text-muted fw-bold">${idx + 1}</td>
            <td><strong class="text-dark">${it.name}</strong></td>
            <td class="text-center"><span class="badge bg-light text-dark border">${it.unit}</span></td>
            <td class="text-end fw-bold">${Number(it.qty).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end">Rs ${Number(it.unit_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end fw-bold text-dark">Rs ${Number(it.line_total).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-muted small">${it.item_note || '-'}</td>
          </tr>
        `;
      });
    } else {
      itemsHtml = '<tr><td colspan="7" class="text-center text-muted py-3">No items found</td></tr>';
    }

    $('#qv_items_body').html(itemsHtml);
    $('#qv_foot_qty').text(totalQty.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#qv_foot_amount').text('Rs ' + totalAmt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    
    let printUrl = "{{ url('raw-materials/out/dc') }}/" + data.id;
    $('#qv_print_link').attr('href', printUrl);

    new bootstrap.Modal(document.getElementById('outQuickViewModal')).show();
  });

  // Enter Key Navigation for Out Multi-Row Table
  $(document).on('keydown', '#outItemRows input, #outItemRows select', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const currentTr = $(this).closest('tr');
      const nextTr = currentTr.next('tr');
      if (nextTr.length > 0) {
        nextTr.find('.out-mat-select').focus();
      } else {
        addOutRow();
      }
    }
  });

  // Enter Key Navigation for Raw Material Multi-Row Table
  $(document).on('keydown', '#rawMaterialRows input, #rawMaterialRows select', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const currentTr = $(this).closest('tr');
      const nextTr = currentTr.next('tr');
      if (nextTr.length > 0) {
        nextTr.find('.mat-row-name').focus().select();
      } else {
        addMaterialRow();
      }
    }
  });
});

/* ══════════ MULTI-ROW RAW MATERIAL LOGIC ══════════ */
function getMaterialRowHtml(num) {
  num = num || 1;
  return `
    <tr class="mat-row">
      <td class="text-center mat-row-num text-muted fw-bold">${num}</td>
      <td>
        <input type="text" name="name[]" class="form-control form-control-sm mat-row-name" placeholder="e.g. Sugar, Desi Ghee, Maida" required autocomplete="off">
      </td>
      <td>
        <select name="unit[]" class="form-select form-select-sm mat-row-unit" required>
          <option value="KG">KG</option>
          <option value="Gram">Gram</option>
          <option value="Ltr">Ltr</option>
          <option value="Pc">Pc</option>
          <option value="Bag">Bag</option>
          <option value="Pack">Pack</option>
          <option value="Dozen">Dozen</option>
          <option value="Box">Box</option>
          <option value="Pound">Pound</option>
          <option value="Tin">Tin</option>
          <option value="Carton">Carton</option>
        </select>
      </td>
      <td>
        <input type="number" step="any" min="0" name="stock_qty[]" class="form-control form-control-sm text-end mat-row-stock" value="0">
      </td>
      <td>
        <input type="number" step="any" min="0" name="unit_price[]" class="form-control form-control-sm text-end mat-row-price" value="0">
      </td>
      <td>
        <input type="number" step="any" min="0" name="alert_qty[]" class="form-control form-control-sm text-end mat-row-alert" value="10">
      </td>
      <td>
        <input type="text" name="note[]" class="form-control form-control-sm mat-row-note" placeholder="Optional note">
      </td>
      <td class="text-center">
        <div class="btn-group btn-group-sm" role="group">
          <button type="button" class="btn btn-outline-success py-1 px-2 btn-add-row" title="Add Row Below" onclick="addMaterialRowAfter(this)">
            <i class="bi bi-plus-lg"></i>
          </button>
          <button type="button" class="btn btn-outline-danger py-1 px-2 btn-remove-row" title="Remove Row" onclick="removeMaterialRow(this)">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </td>
    </tr>`;
}

function reindexMaterialRows() {
  $('#rawMaterialRows tr').each(function(index) {
    $(this).find('.mat-row-num').text(index + 1);
  });
}

function addMaterialRow() {
  const count = $('#rawMaterialRows tr').length;
  const newRow = $(getMaterialRowHtml(count + 1));
  $('#rawMaterialRows').append(newRow);
  reindexMaterialRows();
  newRow.find('.mat-row-name').focus();
}

function addMaterialRowAfter(btn) {
  const currentTr = $(btn).closest('tr');
  const newRow = $(getMaterialRowHtml(1));
  currentTr.after(newRow);
  reindexMaterialRows();
  newRow.find('.mat-row-name').focus();
}

function removeMaterialRow(btn) {
  const rows = $('#rawMaterialRows tr');
  if (rows.length > 1) {
    $(btn).closest('tr').remove();
    reindexMaterialRows();
  } else {
    // If 1 row remains, reset it
    const tr = $(btn).closest('tr');
    tr.find('.mat-row-name').val('');
    tr.find('.mat-row-unit').val('KG');
    tr.find('.mat-row-stock').val('0');
    tr.find('.mat-row-price').val('0');
    tr.find('.mat-row-alert').val('10');
    tr.find('.mat-row-note').val('');
    tr.find('.mat-row-name').focus();
  }
}

function clearMaterialForm() {
  $('#mat_id').val('');
  $('#mat_name').val('');
  $('#mat_unit').val('KG');
  $('#mat_price').val('0');
  $('#mat_stock').val('0');
  $('#mat_alert').val('10');
  $('#mat_note').val('');

  $('#materialModalTitle').html('<i class="fa fa-cubes me-2"></i>Add Raw Material(s)');
  $('#materialModalSubtitle').show();
  $('#multiMaterialSection').show();
  $('#singleMaterialSection').hide();
  $('#materialSubmitBtn').html('<i class="fa fa-save me-1"></i> Save Material(s)');

  // Initial 3 clean rows
  $('#rawMaterialRows').html(getMaterialRowHtml(1) + getMaterialRowHtml(2) + getMaterialRowHtml(3));
  setTimeout(function() {
    $('#rawMaterialRows tr:first .mat-row-name').focus();
  }, 350);
}

function clearVendorForm() {
  $('#v_id').val('');
  $('#v_name').val('');
  $('#v_phone').val('');
  $('#v_email').val('');
  $('#v_address').val('');
  $('#v_opening').val('0');
  $('#vendorModalTitle').html('<i class="fa fa-user-plus me-2"></i>Add Material Vendor');
}

function clearPurchaseForm() {
  $('#pur_id').val('');
  $('#purchaseForm')[0].reset();
  $('#purchaseModalTitle').html('<i class="fa fa-shopping-cart me-2"></i>New Raw Material Purchase');
  $('#purchaseSubmitBtn').html('<i class="fa fa-check-circle me-1"></i> Save Purchase Order');
  $('#purchaseItemRows').html(`
    <tr class="pur-row">
      <td>
        <select name="raw_material_id[]" class="rm-fld mat-select" onchange="updateRowUnit(this)" required style="width:100%;">
          <option value="">-- Select Material --</option>
          @foreach($materials as $m)
            <option value="{{ $m->id }}" data-unit="{{ $m->unit }}" data-price="{{ $m->unit_price }}">{{ $m->name }} ({{ $m->unit }})</option>
          @endforeach
        </select>
      </td>
      <td><input type="number" step="any" name="qty[]" class="rm-fld mat-qty" placeholder="Qty" oninput="calcPurchaseTotals()" required></td>
      <td><input type="number" step="any" name="unit_price[]" class="rm-fld mat-price" placeholder="Rate" oninput="calcPurchaseTotals()" required></td>
      <td><input type="number" step="any" class="rm-fld mat-total" readonly value="0.00"></td>
      <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removePurchaseRow(this)"><i class="fa fa-times"></i></button></td>
    </tr>
  `);
  calcPurchaseTotals();
}

function addPurchaseRow() {
  const rowHtml = `
    <tr class="pur-row">
      <td>
        <select name="raw_material_id[]" class="rm-fld mat-select" onchange="updateRowUnit(this)" required style="width:100%;">
          <option value="">-- Select Material --</option>
          @foreach($materials as $m)
            <option value="{{ $m->id }}" data-unit="{{ $m->unit }}" data-price="{{ $m->unit_price }}">{{ $m->name }} ({{ $m->unit }})</option>
          @endforeach
        </select>
      </td>
      <td><input type="number" step="any" name="qty[]" class="rm-fld mat-qty" placeholder="Qty" oninput="calcPurchaseTotals()" required></td>
      <td><input type="number" step="any" name="unit_price[]" class="rm-fld mat-price" placeholder="Rate" oninput="calcPurchaseTotals()" required></td>
      <td><input type="number" step="any" class="rm-fld mat-total" readonly value="0.00"></td>
      <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removePurchaseRow(this)"><i class="fa fa-times"></i></button></td>
    </tr>
  `;
  const $newRow = $(rowHtml);
  $('#purchaseItemRows').append($newRow);
  $newRow.find('.mat-select').select2({
    dropdownParent: $('#purchaseModal'),
    width: '100%'
  });
}

function removePurchaseRow(btn) {
  if ($('#purchaseItemRows tr').length > 1) {
    $(btn).closest('tr').remove();
    calcPurchaseTotals();
  }
}

function updateRowUnit(selectEl) {
  const selectedOpt = $(selectEl).find(':selected');
  const defaultPrice = selectedOpt.data('price') || 0;
  const row = $(selectEl).closest('tr');
  if (defaultPrice && !row.find('.mat-price').val()) {
    row.find('.mat-price').val(defaultPrice);
  }
  calcPurchaseTotals();
}

function calcPurchaseTotals() {
  let subtotal = 0;
  $('.pur-row').each(function() {
    const qty = parseFloat($(this).find('.mat-qty').val()) || 0;
    const price = parseFloat($(this).find('.mat-price').val()) || 0;
    const lineTotal = qty * price;
    $(this).find('.mat-total').val(lineTotal.toFixed(2));
    subtotal += lineTotal;
  });

  $('#pur_subtotal').val(subtotal.toFixed(2));

  const discount = parseFloat($('#pur_discount').val()) || 0;
  const extraCost = parseFloat($('#pur_extra').val()) || 0;
  const net = Math.max(0, (subtotal - discount) + extraCost);

  $('#pur_net').val(net.toFixed(2));

  const paid = parseFloat($('#pur_paid').val()) || 0;
  const due = Math.max(0, net - paid);

  $('#pur_due').val(due.toFixed(2));
}

/* ══════════ MULTI-ROW RAW MATERIAL OUT (DC) LOGIC ══════════ */
function getOutRowHtml(num, selectedId, qty, price, note) {
  num = num || 1;
  selectedId = selectedId || '';
  qty = (qty !== undefined && qty !== null && qty !== '') ? qty : '';
  note = note || '';

  let optHtml = '<option value="">-- Select Material --</option>';
  let curUnit = 'KG';
  let curStock = 0;
  let curPrice = price !== undefined ? price : 0;

  @foreach($materials as $m)
    var isSel = (selectedId == {{ $m->id }}) ? 'selected' : '';
    if (selectedId == {{ $m->id }}) {
      curUnit = "{{ $m->unit }}";
      curStock = {{ (float)$m->stock_qty }};
      if (!curPrice) curPrice = {{ (float)$m->unit_price }};
    }
    optHtml += `<option value="{{ $m->id }}" data-unit="{{ $m->unit }}" data-stock="{{ (float)$m->stock_qty }}" data-price="{{ (float)$m->unit_price }}" ${isSel}>{{ $m->name }} (Available: {{ number_format($m->stock_qty, 2) }} {{ $m->unit }})</option>`;
  @endforeach

  const lineTotal = (parseFloat(qty || 0) * parseFloat(curPrice || 0)).toFixed(2);

  return `
    <tr class="out-row">
      <td class="text-center text-muted fw-bold out-row-num">${num}</td>
      <td>
        <select name="raw_material_id[]" class="form-select form-select-sm out-mat-select" onchange="updateOutRowUnit(this)" required>
          ${optHtml}
        </select>
      </td>
      <td class="text-center">
        <span class="badge bg-secondary out-stock-badge">${Number(curStock).toFixed(2)} ${curUnit}</span>
      </td>
      <td>
        <div class="input-group input-group-sm">
          <input type="number" step="any" min="0.001" name="qty[]" class="form-control form-control-sm out-qty fw-bold" placeholder="Qty" value="${qty}" oninput="calcOutTotals()" required autocomplete="off">
          <span class="input-group-text out-unit-badge py-0 px-2" style="font-size: 0.75rem;">${curUnit}</span>
        </div>
      </td>
      <td>
        <input type="number" step="any" min="0" name="unit_price[]" class="form-control form-control-sm text-end out-price" placeholder="Cost" value="${curPrice}" oninput="calcOutTotals()" autocomplete="off">
      </td>
      <td>
        <input type="number" step="any" class="form-control form-control-sm text-end out-total bg-light fw-bold" readonly value="${lineTotal}">
      </td>
      <td>
        <input type="text" name="item_note[]" class="form-control form-control-sm out-item-note" placeholder="e.g. Batch 1" value="${note}" autocomplete="off">
      </td>
      <td class="text-center">
        <div class="btn-group btn-group-sm" role="group">
          <button type="button" class="btn btn-outline-success py-1 px-2 btn-add-out-row" title="Add Row Below" onclick="addOutRowAfter(this)">
            <i class="bi bi-plus-lg"></i>
          </button>
          <button type="button" class="btn btn-outline-danger py-1 px-2 btn-remove-out-row" title="Remove Row" onclick="removeOutRow(this)">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </td>
    </tr>
  `;
}

function reindexOutRows() {
  $('#outItemRows tr').each(function(index) {
    $(this).find('.out-row-num').text(index + 1);
  });
}

function addOutRow() {
  const count = $('#outItemRows tr').length;
  const newRow = $(getOutRowHtml(count + 1));
  $('#outItemRows').append(newRow);
  reindexOutRows();
  newRow.find('.out-mat-select').focus();
}

function addOutRowAfter(btn) {
  const currentTr = $(btn).closest('tr');
  const newRow = $(getOutRowHtml(1));
  currentTr.after(newRow);
  reindexOutRows();
  newRow.find('.out-mat-select').focus();
}

function removeOutRow(btn) {
  const rows = $('#outItemRows tr');
  if (rows.length > 1) {
    $(btn).closest('tr').remove();
    reindexOutRows();
    calcOutTotals();
  } else {
    // If only 1 row remains, reset it
    const tr = $(btn).closest('tr');
    tr.find('.out-mat-select').val('');
    tr.find('.out-stock-badge').text('0.00 KG');
    tr.find('.out-unit-badge').text('KG');
    tr.find('.out-qty').val('');
    tr.find('.out-price').val('0');
    tr.find('.out-total').val('0.00');
    tr.find('.out-item-note').val('');
    calcOutTotals();
    tr.find('.out-mat-select').focus();
  }
}

function updateOutRowUnit(selectEl) {
  const selectedOpt = $(selectEl).find(':selected');
  const unit = selectedOpt.data('unit') || 'KG';
  const stock = parseFloat(selectedOpt.data('stock')) || 0;
  const defaultPrice = parseFloat(selectedOpt.data('price')) || 0;
  const row = $(selectEl).closest('tr');

  row.find('.out-unit-badge').text(unit);
  row.find('.out-stock-badge').text(stock.toFixed(2) + ' ' + unit);

  if (defaultPrice > 0 && (!row.find('.out-price').val() || row.find('.out-price').val() == '0')) {
    row.find('.out-price').val(defaultPrice);
  }

  calcOutTotals();
}

function calcOutTotals() {
  let totalQty = 0;
  let totalVal = 0;
  let count = 0;

  $('.out-row').each(function() {
    const qty = parseFloat($(this).find('.out-qty').val()) || 0;
    const price = parseFloat($(this).find('.out-price').val()) || 0;
    const lineTotal = qty * price;
    $(this).find('.out-total').val(lineTotal.toFixed(2));

    if (qty > 0 || $(this).find('.out-mat-select').val()) {
      count++;
    }
    totalQty += qty;
    totalVal += lineTotal;
  });

  $('#out_total_items_badge').text(count);
  $('#out_total_qty_badge').text(totalQty.toFixed(2));
  $('#out_total_val_badge').text('Rs ' + totalVal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
}

function clearOutForm() {
  $('#out_id').val('');
  $('#outForm')[0].reset();
  $('#outModalTitle').html('<i class="fa fa-truck me-2"></i>New Raw Material Out (Delivery Challan)');
  $('#outSubmitBtn').html('<i class="fa fa-check me-1"></i> Issue Material Out & Save DC');
  $('#outItemRows').html(getOutRowHtml(1) + getOutRowHtml(2));
  calcOutTotals();
  setTimeout(function() {
    $('#out_location').focus();
  }, 350);
}

function filterStockTable() {
  const query = $('#stockSearchInput').val().toLowerCase();
  $('#stockReportTable tbody tr.stock-row').each(function() {
    const text = $(this).text().toLowerCase();
    if (text.indexOf(query) > -1) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
}

function toggleAllStockChecks(master) {
  $('.stock-item-check').prop('checked', master.checked);
}

function printSelectedStockReport() {
  const selectedIds = [];
  $('.stock-item-check:checked').each(function() {
    selectedIds.push($(this).val());
  });

  if (selectedIds.length === 0) {
    alert('Please select at least one material using checkboxes to print.');
    return;
  }

  let printUrl = "{{ route('raw_materials.stock_report.print') }}?ids=" + selectedIds.join(',');
  const dateFrom = "{{ $stockDateFrom }}";
  const dateTo = "{{ $stockDateTo }}";
  if (dateFrom) printUrl += "&date_from=" + dateFrom;
  if (dateTo) printUrl += "&date_to=" + dateTo;

  const iframe = document.getElementById('printStockIframe');
  iframe.src = printUrl;
}

function printStockReportDirect() {
  let printUrl = "{{ route('raw_materials.stock_report.print') }}";
  const dateFrom = "{{ $stockDateFrom }}";
  const dateTo = "{{ $stockDateTo }}";
  const materialId = "{{ $stockMaterialId }}";
  
  const params = [];
  if (dateFrom) params.push("date_from=" + dateFrom);
  if (dateTo) params.push("date_to=" + dateTo);
  if (materialId) params.push("ids=" + materialId);
  
  if (params.length > 0) {
    printUrl += "?" + params.join("&");
  }

  const iframe = document.getElementById('printStockIframe');
  iframe.src = printUrl;
}

function printVendorLedgerDirect() {
  let printUrl = "{{ route('raw_materials.vendor_ledger.print') }}";
  const vendorId = "{{ $selectedVendorId }}";
  const dateFrom = "{{ $ledgerDateFrom }}";
  const dateTo = "{{ $ledgerDateTo }}";

  const params = [];
  if (vendorId) params.push("ledger_vendor_id=" + vendorId);
  if (dateFrom) params.push("ledger_date_from=" + dateFrom);
  if (dateTo) params.push("ledger_date_to=" + dateTo);

  if (params.length > 0) {
    printUrl += "?" + params.join("&");
  }

  const iframe = document.getElementById('printLedgerIframe');
  iframe.src = printUrl;
}

$(document).ready(function() {
  $('#purchaseModal').on('shown.bs.modal', function () {
    $(this).find('.mat-select').select2({
      dropdownParent: $('#purchaseModal'),
      width: '100%'
    });
  });
});
</script>
@endsection
