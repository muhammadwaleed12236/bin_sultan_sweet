@extends('admin_panel.layout.app')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --pc-bg: #f8fafc;
  --pc-surface: #ffffff;
  --pc-border: #e2e8f0;
  --pc-border-lt: #f1f5f9;
  --pc-text: #0f172a;
  --pc-text-sec: #475569;
  --pc-text-muted: #94a3b8;
  --pc-accent: #4f46e5;
  --pc-accent-drk: #3730a3;
  --pc-success: #059669;
  --pc-danger: #dc2626;
  --pc-warning: #d97706;
  --pc-radius: 14px;
  --pc-radius-sm: 10px;
  --pc-shadow: 0 1px 3px rgba(0,0,0,.05), 0 1px 2px rgba(0,0,0,.03);
  --pc-shadow-lg: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
  --pc-shadow-xl: 0 20px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
  --pc-font: 'Inter', system-ui, -apple-system, sans-serif;
}

.pc-page * { font-family: var(--pc-font); }
.pc-page { background: var(--pc-bg); min-height: 100vh; padding-bottom: 2.5rem; }

/* ═══════ HEADER ═══════ */
.pc-hdr {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
  border-radius: var(--pc-radius);
  padding: 1.25rem 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--pc-shadow-lg);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.pc-hdr h2 {
  font-size: 1.35rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -.4px;
  margin: 0;
  display: flex;
  align-items: center;
  gap: .65rem;
}

.pc-hdr h2 .hdr-icon-box {
  width: 38px;
  height: 38px;
  background: rgba(99, 102, 241, 0.2);
  border: 1px solid rgba(99, 102, 241, 0.4);
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #a5b4fc;
  font-size: 18px;
}

.pc-hdr .hdr-badge {
  background: rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.15);
}

.pc-btn {
  display: inline-flex;
  align-items: center;
  gap: .45rem;
  border-radius: var(--pc-radius-sm);
  font-weight: 600;
  font-size: .82rem;
  transition: all .2s ease;
  cursor: pointer;
  text-decoration: none;
  border: none;
  padding: .5rem 1.25rem;
  white-space: nowrap;
}

.pc-btn-primary {
  background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
  color: #fff !important;
  box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
}

.pc-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
  filter: brightness(1.08);
}

.pc-btn-dark {
  background: rgba(255, 255, 255, 0.12);
  color: #fff !important;
  border: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(6px);
}

.pc-btn-dark:hover { 
  background: rgba(255, 255, 255, 0.22); 
  transform: translateY(-1px);
}

.pc-btn-outline {
  background: #ffffff;
  border: 1.5px solid var(--pc-border);
  color: var(--pc-text-sec);
}

.pc-btn-outline:hover { 
  border-color: var(--pc-accent); 
  color: var(--pc-accent); 
  background: #f8fafc;
}

/* ═══════ SUMMARY CARDS ═══════ */
.pc-sum-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 12px;
  margin-bottom: 18px;
}

.pc-sum-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: 14px 16px;
  box-shadow: var(--pc-shadow);
  border-left: 4px solid var(--pc-accent);
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.pc-sum-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--pc-shadow-lg);
}

.pc-sum-card.green { border-color: var(--pc-success); }
.pc-sum-card.orange { border-color: var(--pc-warning); }
.pc-sum-card.red { border-color: var(--pc-danger); }
.pc-sum-card.indigo { border-color: #6366f1; }
.pc-sum-card.purple { border-color: #8b5cf6; }
.pc-sum-card.emerald { border-color: #10b981; }

.pc-sum-card .lbl {
  font-size: .7rem;
  color: var(--pc-text-sec);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .4px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.pc-sum-card .val {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--pc-text);
  margin-top: 4px;
  line-height: 1.2;
}

.pc-sum-card .val-sub {
  font-size: .8rem;
  font-weight: 700;
  margin-top: 4px;
  display: block;
}

.val-sub.text-success { color: #059669 !important; }
.val-sub.text-indigo { color: #4f46e5 !important; }
.val-sub.text-purple { color: #7c3aed !important; }

/* ═══════ FILTER BAR ═══════ */
.pc-filter {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: 16px 18px;
  box-shadow: var(--pc-shadow);
  margin-bottom: 18px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: flex-end;
}

.pc-filter .fg { display: flex; flex-direction: column; gap: 5px; }
.pc-filter label {
  font-size: .7rem;
  font-weight: 700;
  color: var(--pc-text-sec);
  text-transform: uppercase;
  letter-spacing: .4px;
  display: flex;
  align-items: center;
  gap: 4px;
}

.pc-filter .pc-fld {
  border: 1.5px solid var(--pc-border);
  border-radius: 8px;
  padding: .5rem .8rem;
  font-size: .84rem;
  font-weight: 500;
  color: var(--pc-text);
  outline: none;
  transition: all .2s ease;
  height: 40px;
  background: #ffffff;
}

.pc-filter .pc-fld:focus { border-color: var(--pc-accent); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12); }
.pc-filter .fg-wide { flex: 1; min-width: 220px; }

.preset-btn-group {
  display: inline-flex;
  gap: 4px;
  background: #f1f5f9;
  padding: 3px;
  border-radius: 8px;
  border: 1px solid #cbd5e1;
}

.preset-btn {
  padding: 4px 10px;
  font-size: 11px;
  font-weight: 700;
  border-radius: 6px;
  border: none;
  background: transparent;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s ease;
}

.preset-btn:hover { background: #e2e8f0; color: #0f172a; }
.preset-btn.active { background: #4f46e5; color: #ffffff; }

/* ═══════ CARD & TABLE ═══════ */
.pc-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius);
  box-shadow: var(--pc-shadow);
}

.pc-card-body { padding: 1.25rem 1.5rem; }

.pc-tbl-wrap {
  overflow-x: auto;
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  -webkit-overflow-scrolling: touch;
}

.pc-tbl {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: .82rem;
}

.pc-tbl thead th {
  background: #0f172a;
  font-size: .68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: #ffffff;
  padding: .65rem .75rem;
  border-bottom: 2px solid var(--pc-border);
  text-align: left;
  white-space: nowrap;
  vertical-align: middle;
}

.pc-tbl thead th.num { text-align: right; }
.pc-tbl tbody td { padding: .55rem .75rem; border-bottom: 1px solid var(--pc-border-lt); vertical-align: middle; }
.pc-tbl tbody tr { transition: background .12s ease; }
.pc-tbl tbody tr:hover { background: #f8fafc; }
.pc-tbl tbody tr:last-child td { border-bottom: none; }
.pc-tbl tbody td.num { text-align: right; font-variant-numeric: tabular-nums; }
.pc-tbl tbody td.bold { font-weight: 700; }

.pc-tbl .pc-code {
  font-size: .72rem;
  background: #f1f5f9;
  color: #0f172a;
  padding: 3px 6px;
  border-radius: 5px;
  font-family: 'Consolas', monospace;
  font-weight: 600;
  border: 1px solid #e2e8f0;
}

.pc-tbl .pc-name { font-weight: 600; color: #1e293b; }

/* Micro badges inside table */
.chip-prod {
  display: inline-block;
  background: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
  border-radius: 6px;
  padding: 2px 6px;
  font-weight: 700;
  font-size: 11.5px;
  white-space: nowrap;
}
.chip-sold {
  display: inline-block;
  background: #f5f3ff;
  color: #7c3aed;
  border: 1px solid #ddd6fe;
  border-radius: 6px;
  padding: 2px 6px;
  font-weight: 700;
  font-size: 11.5px;
  white-space: nowrap;
}
.chip-bal {
  display: inline-block;
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  border-radius: 6px;
  padding: 2px 6px;
  font-weight: 800;
  font-size: 12px;
  white-space: nowrap;
}

.bal-good { color: var(--pc-success); font-weight: 800; }
.bal-low { color: var(--pc-warning); font-weight: 800; }
.bal-out { color: var(--pc-danger); font-weight: 800; }

.pc-tbl tr.row-out { background: #fff1f2 !important; }
.pc-tbl tr.row-low { background: #fffbeb !important; }

.pc-tfoot {
  background: #0f172a;
  color: #ffffff;
  font-weight: 800;
}

.pc-tfoot td {
  padding: .7rem .75rem;
  border-top: 2px solid #334155;
  font-size: .82rem;
  color: #ffffff;
}
.pc-tfoot td.num { text-align: right; }

/* ═══════ EMPTY / SPINNER ═══════ */
.pc-empty {
  text-align: center;
  padding: 2.5rem .85rem;
  color: var(--pc-text-muted);
}

.pc-empty i { font-size: 2rem; color: #ced8e6; display: block; margin-bottom: .5rem; }
.pc-empty span { font-size: .9rem; font-weight: 500; }

.pc-spinner {
  display: flex; align-items: center; justify-content: center; gap: 10px;
  padding: 48px; color: var(--pc-accent); font-size: 14px; font-weight: 600;
}

.pc-spinner i { font-size: 1.5rem; animation: pc-spin 1s linear infinite; }
@keyframes pc-spin { to { transform: rotate(360deg); } }

/* ═══════ LIVE SEARCH ═══════ */
.pc-ls-wrap { position: relative; }
.pc-ls-wrap input {
  border: 1.5px solid var(--pc-border); border-radius: 8px;
  padding: .45rem .75rem .45rem 32px; font-size: .82rem; outline: none;
  min-width: 220px;
  transition: all 0.2s ease;
}
.pc-ls-wrap input:focus { border-color: var(--pc-accent); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12); }
.pc-ls-wrap .ls-ico {
  position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
  color: var(--pc-text-muted); font-size: 14px; pointer-events: none;
}

/* ═══════ TABS ═══════ */
.pc-tabs {
  display: flex; gap: 0; margin-bottom: 16px;
  background: var(--pc-surface); border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm); padding: 4px; width: fit-content;
  box-shadow: var(--pc-shadow);
}

.pc-tab {
  padding: 8px 18px; border-radius: 7px; font-weight: 700;
  font-size: .78rem; cursor: pointer; color: var(--pc-text-muted);
  transition: all .15s ease; border: none; background: transparent;
}

.pc-tab.active {
  background: linear-gradient(135deg, var(--pc-accent) 0%, var(--pc-accent-drk) 100%);
  color: #fff; box-shadow: 0 3px 10px rgba(79, 70, 229, 0.25);
}

/* ═══════ VARIANT CARDS ═══════ */
.pc-vp { background: #f8fafc; padding: 10px 16px; font-weight: 700; font-size: .8rem; color: var(--pc-text); border-top: 2px solid #e2e8f0; display: flex; align-items: center; gap: 10px; }
.pc-vp .badge { font-size: .65rem; background: var(--pc-accent); color: #fff; border-radius: 5px; padding: 2px 7px; }
.pc-vp .tot { margin-left: auto; font-size: .72rem; color: var(--pc-text-muted); }

.pc-sz-grid { display: flex; flex-wrap: wrap; gap: 8px; padding: 12px 16px 14px; border-bottom: 1px solid var(--pc-border-lt); }
.pc-sz-card { border-radius: 8px; padding: 10px 14px; min-width: 120px; text-align: center; border: 2px solid var(--pc-border); transition: .15s; }
.pc-sz-card:hover { border-color: var(--pc-accent); box-shadow: 0 2px 8px rgba(79, 70, 229, 0.12); }
.pc-sz-card.ok { border-color: var(--pc-success); background: #f0fdf4; }
.pc-sz-card.low { border-color: var(--pc-warning); background: #fffbf0; }
.pc-sz-card.out { border-color: var(--pc-danger); background: #fff5f5; }
.pc-sz-card .sz-lbl { font-size: .72rem; font-weight: 700; color: var(--pc-text); margin-bottom: 4px; }
.pc-sz-card .sz-stk { font-size: 1.2rem; font-weight: 800; line-height: 1; }
.pc-sz-card.ok .sz-stk { color: var(--pc-success); }
.pc-sz-card.low .sz-stk { color: var(--pc-warning); }
.pc-sz-card.out .sz-stk { color: var(--pc-danger); }
.pc-sz-card .sz-price { font-size: .68rem; color: var(--pc-text-muted); margin-top: 3px; }
.pc-sz-card .sz-status { font-size: .6rem; font-weight: 700; text-transform: uppercase; margin-top: 3px; letter-spacing: .4px; }
.pc-sz-card.ok .sz-status { color: var(--pc-success); }
.pc-sz-card.low .sz-status { color: var(--pc-warning); }
.pc-sz-card.out .sz-status { color: var(--pc-danger); }

/* ═══════ RESPONSIVE ═══════ */
@media (max-width: 768px) {
  .pc-hdr { padding: 1rem 1.25rem; flex-direction: column; align-items: stretch; gap: .5rem; }
  .pc-hdr h2 { font-size: 1.1rem; }
  .pc-hdr .hdr-actions { flex-wrap: wrap; }
  .pc-card-body { padding: 1rem; }
  .pc-tbl tbody td { padding: .45rem .55rem; }
  .pc-sum-grid { grid-template-columns: repeat(2, 1fr); }
  .pc-filter { flex-direction: column; align-items: stretch; }
  .pc-filter .fg-wide, .pc-filter .fg { width: 100%; min-width: auto; }
}
</style>

<div class="pc-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══ HEADER ═══ --}}
    <div class="pc-hdr">
      <div class="d-flex align-items-center gap-3">
        <h2>
          <span class="hdr-icon-box"><i class="bi bi-boxes"></i></span>
          Item Stock & Production Report
        </h2>
        <span class="hdr-badge d-none d-sm-inline" id="totalBadge">0 Products</span>
      </div>
      <div class="d-flex align-items-center gap-2 flex-wrap hdr-actions">
        <button class="pc-btn pc-btn-dark" onclick="printClosing()">
          <i class="bi bi-receipt"></i> Print Closing
        </button>
      </div>
    </div>

    {{-- ═══ TABS ═══ --}}
    <div class="pc-tabs">
      <button class="pc-tab active" id="tabItem" onclick="switchTab('item')">📋 Item Stock & Production</button>
      <button class="pc-tab" id="tabSize" onclick="switchTab('size')">📐 Size / Variant Stock</button>
    </div>

    {{-- ═══ ITEM STOCK PANEL ═══ --}}
    <div id="panelItem">

      {{-- Summary Cards --}}
      <div class="pc-sum-grid">
        <div class="pc-sum-card" style="border-left-color: #64748b;" title="Initial / Opening stock at start of selected date & time">
          <div class="lbl"><i class="bi bi-clock-history text-secondary"></i> Previous Stock (Opening)</div>
          <div class="val" id="cInitialQty">0 PC</div>
          <span class="val-sub text-muted" id="cInitialAmt">Rs 0 (Opening)</span>
        </div>

        <div class="pc-sum-card emerald" title="Own Production Inward Stock and Value">
          <div class="lbl"><i class="bi bi-gear-wide-connected text-success"></i> Own Production (Inward)</div>
          <div class="val" id="cProducedQty">0 PC</div>
          <span class="val-sub text-success" id="cProducedAmt">+ Rs 0 (Production)</span>
        </div>

        <div class="pc-sum-card purple" title="Total Sold Quantity and Sale Value">
          <div class="lbl"><i class="bi bi-cart-check text-purple"></i> Total Sold (Sales)</div>
          <div class="val" id="cSoldQty">0 PC</div>
          <span class="val-sub text-purple" id="cSoldAmt">- Rs 0 (Sales)</span>
        </div>

        <div class="pc-sum-card indigo" title="Current Available Stock Balance & Valuation">
          <div class="lbl"><i class="bi bi-box-seam text-indigo"></i> Closing Stock (Balance)</div>
          <div class="val" id="cStockQty">0 PC</div>
          <span class="val-sub text-indigo" id="cStockAmt">= Rs 0 (Valuation)</span>
        </div>

        <div class="pc-sum-card" title="Total Purchased Stock">
          <div class="lbl"><i class="bi bi-truck"></i> Purchased Stock</div>
          <div class="val" id="cPurchQty">0 PC</div>
          <span class="val-sub text-muted" id="cPurchAmt">Rs 0</span>
        </div>

        <div class="pc-sum-card green" onclick="filterReportCard('ok')" style="cursor:pointer;" title="Click to show In Stock products">
          <div class="lbl"><i class="bi bi-check-circle"></i> In Stock (>5)</div>
          <div class="val" id="cInStock">–</div>
          <span class="val-sub text-success">Active Products</span>
        </div>

        <div class="pc-sum-card orange" onclick="filterReportCard('low')" style="cursor:pointer;" title="Click to show Low Stock products">
          <div class="lbl"><i class="bi bi-exclamation-triangle"></i> Low Stock (1-5)</div>
          <div class="val" id="cLow">–</div>
          <span class="val-sub text-warning">Needs Restock</span>
        </div>

        <div class="pc-sum-card red" onclick="filterReportCard('out')" style="cursor:pointer;" title="Click to show Out of Stock products">
          <div class="lbl"><i class="bi bi-x-circle"></i> Out of Stock</div>
          <div class="val" id="cOut">–</div>
          <span class="val-sub text-danger">0 Balance</span>
        </div>
      </div>

      {{-- Filter --}}
      <div class="pc-filter">
        <div class="fg fg-wide">
          <label><i class="bi bi-search"></i> Filter by Product</label>
          <select id="product_id" class="select2-item pc-fld" multiple style="width:100%">
            <option value="all">— All Products —</option>
            @foreach($products as $prod)
            <option value="{{ $prod->id }}">{{ $prod->item_code }} – {{ $prod->item_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="fg">
          <div class="d-flex justify-content-between align-items-center">
            <label><i class="bi bi-calendar-event"></i> From Date & Time (12h)</label>
          </div>
          <input type="text" class="pc-fld" id="start_datetime" placeholder="Select Date & Time (AM/PM)">
        </div>

        <div class="fg">
          <div class="d-flex justify-content-between align-items-center">
            <label><i class="bi bi-calendar-check"></i> To Date & Time (12h)</label>
          </div>
          <input type="text" class="pc-fld" id="end_datetime" placeholder="Select Date & Time (AM/PM)">
        </div>

        <div class="fg">
          <label>Quick Presets</label>
          <div class="preset-btn-group">
            <button type="button" class="preset-btn active" onclick="setPreset('today')">Today</button>
            <button type="button" class="preset-btn" onclick="setPreset('yesterday')">Yesterday</button>
            <button type="button" class="preset-btn" onclick="setPreset('month')">This Month</button>
          </div>
        </div>

        <button class="pc-btn pc-btn-primary" onclick="fetchReport()"><i class="bi bi-search"></i> Search Report</button>
        <button type="button" id="btnReportHasStock" class="pc-btn pc-btn-outline" onclick="toggleReportHasStock()">
          <i class="bi bi-box-seam"></i> <span>Has Stock</span>
        </button>
      </div>

      {{-- Table Card --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 style="font-weight:800;color:var(--pc-text);margin:0;font-size:.92rem;">
              <i class="bi bi-table me-1 text-primary"></i> Stock Movement & Valuation Detail
            </h6>
            <div class="d-flex align-items-center gap-2">
              <div class="pc-ls-wrap">
                <i class="bi bi-search ls-ico"></i>
                <input type="text" id="liveSearch" placeholder="Search by name / code..." oninput="applyFilter()">
              </div>
              <span style="font-size:.78rem;color:var(--pc-text-muted);font-weight:700;" id="rowCount">–</span>
            </div>
          </div>

          <div class="pc-tbl-wrap" style="max-height:calc(100vh - 350px);overflow-y:auto;">
            <table class="pc-tbl">
              <thead style="position:sticky;top:0;z-index:2;">
                <tr>
                  <th style="width:35px;">#</th>
                  <th>Item Code</th>
                  <th>Item Name</th>
                  <th class="num">Sale Price</th>
                  <th class="num">Initial Stock</th>
                  <th class="num" style="background:#064e3b;color:#a7f3d0;">🏭 Produced</th>
                  <th class="num">Purchased</th>
                  <th class="num" style="color:#fca5a5;">P. Return</th>
                  <th class="num" style="color:#86efac;">Adj ➕</th>
                  <th class="num" style="color:#fca5a5;">Adj ➖</th>
                  <th class="num" style="background:#3b0764;color:#ddd6fe;">🛒 Sold</th>
                  <th class="num" style="color:#93c5fd;">S. Return</th>
                  <th class="num" style="background:#1e1b4b;color:#c7d2fe;">📦 Balance Stock</th>
                </tr>
              </thead>
              <tbody id="reportBody">
                <tr><td colspan="13" class="pc-empty"><i class="bi bi-search"></i><span>Click Search to load data</span></td></tr>
              </tbody>
              <tfoot class="pc-tfoot">
                <tr>
                  <td colspan="3" style="text-align:right;color:#cbd5e1;font-size:.75rem;text-transform:uppercase">Totals:</td>
                  <td class="num" id="ftPrice">–</td>
                  <td class="num" id="ftInitial">–</td>
                  <td class="num" id="ftProduced" style="color:#34d399;">–</td>
                  <td class="num" id="ftPurch">–</td>
                  <td class="num" id="ftPReturn" style="color:#f87171;">–</td>
                  <td class="num" id="ftAdjInc" style="color:#4ade80;">–</td>
                  <td class="num" id="ftAdjDec" style="color:#f87171;">–</td>
                  <td class="num" id="ftSold" style="color:#c084fc;">–</td>
                  <td class="num" id="ftSReturn" style="color:#60a5fa;">–</td>
                  <td class="num bold" id="ftBal" style="color:#818cf8;font-size:.9rem;">–</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>{{-- /panelItem --}}

    {{-- ═══ SIZE PANEL ═══ --}}
    <div id="panelSize" style="display:none">
      <div class="pc-sum-grid">
        <div class="pc-sum-card" onclick="filterVarCard('all')" style="cursor:pointer;"><div class="lbl">Products w/ Sizes</div><div class="val" id="vTotal">–</div></div>
        <div class="pc-sum-card green" onclick="filterVarCard('ok')" style="cursor:pointer;"><div class="lbl">Sizes In Stock</div><div class="val" id="vOk">–</div></div>
        <div class="pc-sum-card orange" onclick="filterVarCard('low')" style="cursor:pointer;"><div class="lbl">Sizes Low</div><div class="val" id="vLow">–</div></div>
        <div class="pc-sum-card red" onclick="filterVarCard('out')" style="cursor:pointer;"><div class="lbl">Sizes Out</div><div class="val" id="vOut">–</div></div>
        <div class="pc-sum-card"><div class="lbl">Total Size Units</div><div class="val" id="vUnits">–</div></div>
      </div>

      <div class="pc-filter">
        <div class="fg fg-wide">
          <label>Filter by Product</label>
          <select id="v_product_id" class="select2-var pc-fld" style="width:100%">
            <option value="all">— All Products (with sizes) —</option>
            @foreach($products as $prod)
            <option value="{{ $prod->id }}">{{ $prod->item_code }} – {{ $prod->item_name }}</option>
            @endforeach
          </select>
        </div>
        <button class="pc-btn pc-btn-primary" onclick="fetchVariants()"><i class="bi bi-search"></i>Search</button>
        <button type="button" id="btnVarHasStock" class="pc-btn pc-btn-outline" onclick="toggleVarHasStock()">
          <i class="bi bi-box-seam"></i> <span>Has Stock</span>
        </button>
      </div>

      <div class="pc-card">
        <div class="pc-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 style="font-weight:700;color:var(--pc-text);margin:0;font-size:.9rem;"><i class="bi bi-rulers me-1"></i>Size-wise Stock</h6>
            <div class="d-flex align-items-center gap-2">
              <div class="pc-ls-wrap">
                <i class="bi bi-search ls-ico"></i>
                <input type="text" id="vLiveSearch" placeholder="Search product / size..." oninput="applyVarFilter()">
              </div>
              <span style="font-size:.75rem;color:var(--pc-text-muted);font-weight:600;" id="vRowCount">–</span>
            </div>
          </div>
          <div id="variantBody" style="max-height:calc(100vh - 380px);overflow-y:auto;">
            <div class="pc-empty"><i class="bi bi-rulers"></i><span>Click Search to load size data</span></div>
          </div>
        </div>
      </div>
    </div>{{-- /panelSize --}}

  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
let fpStart, fpEnd;

$(document).ready(function() {
    $('.select2-item').select2({ placeholder:'Search product…', allowClear:true, width:'100%' });
    $('.select2-var').select2({ placeholder:'Search product…', allowClear:true, width:'100%' });

    // Initialize 12-hour Flatpickr Pickers with local browser date
    var now = new Date();
    var pad = function(n) { return n < 10 ? '0' + n : n; };
    var todayStr = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate());
    
    fpStart = flatpickr("#start_datetime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:S",
        altInput: true,
        altFormat: "d-M-Y h:i K", // 12-hour AM/PM (e.g. 18-Aug-2026 12:00 AM)
        time_24hr: false,
        defaultDate: todayStr + " 00:00:00"
    });

    fpEnd = flatpickr("#end_datetime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:S",
        altInput: true,
        altFormat: "d-M-Y h:i K", // 12-hour AM/PM (e.g. 18-Aug-2026 11:59 PM)
        time_24hr: false,
        defaultDate: todayStr + " 23:59:59"
    });

    fetchReport();
});

function setPreset(type) {
    $('.preset-btn').removeClass('active');
    $(event.target).addClass('active');

    var now = new Date();
    var pad = function(n) { return n < 10 ? '0' + n : n; };
    var y = now.getFullYear();
    var m = pad(now.getMonth() + 1);
    var d = pad(now.getDate());

    if (type === 'today') {
        var start = y + '-' + m + '-' + d + ' 00:00:00';
        var end   = y + '-' + m + '-' + d + ' 23:59:59';
        fpStart.setDate(start, true);
        fpEnd.setDate(end, true);
    } else if (type === 'yesterday') {
        var yest = new Date(now.getTime() - 24*60*60*1000);
        var yY = yest.getFullYear();
        var yM = pad(yest.getMonth() + 1);
        var yD = pad(yest.getDate());
        var start = yY + '-' + yM + '-' + yD + ' 00:00:00';
        var end   = yY + '-' + yM + '-' + yD + ' 23:59:59';
        fpStart.setDate(start, true);
        fpEnd.setDate(end, true);
    } else if (type === 'month') {
        var start = y + '-' + m + '-01 00:00:00';
        var end   = y + '-' + m + '-' + d + ' 23:59:59';
        fpStart.setDate(start, true);
        fpEnd.setDate(end, true);
    }
    fetchReport();
}

function getProductIds() {
    var vals = $('#product_id').val();
    if (!vals || vals.length === 0 || vals.includes('all')) return 'all';
    return vals;
}

/* ═══════ TAB SWITCH ═══════ */
function switchTab(tab) {
    document.getElementById('panelItem').style.display = tab === 'item' ? '' : 'none';
    document.getElementById('panelSize').style.display = tab === 'size' ? '' : 'none';
    document.getElementById('tabItem').classList.toggle('active', tab === 'item');
    document.getElementById('tabSize').classList.toggle('active', tab === 'size');
    if (tab === 'size' && allVarRows.length === 0) fetchVariants();
}

/* ═══════ PRINT CLOSING ═══════ */
function printClosing() {
    var sVal = $('#start_datetime').val();
    var eVal = $('#end_datetime').val();
    var startDate = sVal ? sVal.slice(0, 10) : '';
    var endDate   = eVal ? eVal.slice(0, 10) : '';
    var startTime = sVal && sVal.length > 10 ? sVal.slice(11) : '00:00:00';
    var endTime   = eVal && eVal.length > 10 ? eVal.slice(11) : '23:59:59';
    var productIds = getProductIds();

    var params = 'start_date=' + startDate + '&end_date=' + endDate
               + '&start_time=' + startTime + '&end_time=' + endTime;
    if (productIds !== 'all') {
        productIds.forEach(function(id) { params += '&product_id[]=' + id; });
    }
    var url = "{{ route('report.item_stock.closing_print') }}?" + params;
    window.open(url, '_blank');
}

/* ═══════ ITEM STOCK ═══════ */
let allRows = [];
let reportHasStockOnly = false;
let reportStatusFilter = 'all';

window.toggleReportHasStock = function() {
    reportHasStockOnly = !reportHasStockOnly;
    reportStatusFilter = reportHasStockOnly ? 'has_stock' : 'all';
    updateReportHasStockBtn();
    applyFilter();
};

function updateReportHasStockBtn() {
    const btn = $('#btnReportHasStock');
    if (reportHasStockOnly || reportStatusFilter === 'ok' || reportStatusFilter === 'has_stock') {
        btn.removeClass('pc-btn-outline')
           .addClass('pc-btn-primary')
           .css({'background':'#4f46e5','color':'#fff','border-color':'#4f46e5'})
           .html('<i class="bi bi-check-circle-fill"></i> <span>Has Stock (Active)</span>');
    } else {
        btn.removeClass('pc-btn-primary')
           .addClass('pc-btn-outline')
           .css({'background':'','color':'','border-color':''})
           .html('<i class="bi bi-box-seam"></i> <span>Has Stock</span>');
    }
}

window.filterReportCard = function(status) {
    reportStatusFilter = status;
    reportHasStockOnly = (status === 'ok');
    updateReportHasStockBtn();
    applyFilter();
};

function fetchReport() {
    const productIds = getProductIds();
    const startVal = $('#start_datetime').val();
    const endVal   = $('#end_datetime').val();

    const startDate = startVal ? startVal.slice(0, 10) : '';
    const endDate   = endVal ? endVal.slice(0, 10) : '';
    const startTime = startVal && startVal.length > 10 ? startVal.slice(11) : '00:00:00';
    const endTime   = endVal && endVal.length > 10 ? endVal.slice(11) : '23:59:59';

    showSpinner();
    var data = { 
        _token: "{{ csrf_token() }}", 
        start_date: startDate, 
        end_date: endDate, 
        start_time: startTime, 
        end_time: endTime 
    };
    if (productIds === 'all') { data.product_id = 'all'; }
    else { data.product_id = productIds; }

    $.ajax({
        url: "{{ route('report.item_stock.fetch') }}", 
        type: 'POST', 
        dataType: 'json',
        data: data,
        success: function(res) {
            allRows = res.data || [];
            document.getElementById('liveSearch').value = '';
            applyFilter();
            updateCards(res);
            document.getElementById('totalBadge').textContent = allRows.length + ' Products';
        },
        error: function() {
            document.getElementById('reportBody').innerHTML = '<tr><td colspan="13" class="pc-empty" style="color:var(--pc-danger)"><i class="bi bi-exclamation-triangle"></i><span>Error loading data. Check console.</span></td></tr>';
        }
    });
}

function applyFilter() {
    const q = (document.getElementById('liveSearch').value || '').toLowerCase().trim();
    let vis = allRows;

    if (reportHasStockOnly || reportStatusFilter === 'has_stock') {
        vis = vis.filter(r => (parseFloat(r.balance) || 0) > 0);
    } else if (reportStatusFilter === 'ok') {
        vis = vis.filter(r => (parseFloat(r.balance) || 0) > 5);
    } else if (reportStatusFilter === 'low') {
        vis = vis.filter(r => {
            const b = parseFloat(r.balance) || 0;
            return b > 0 && b <= 5;
        });
    } else if (reportStatusFilter === 'out') {
        vis = vis.filter(r => (parseFloat(r.balance) || 0) <= 0);
    }

    if (q) {
        vis = vis.filter(r => (r.item_name||'').toLowerCase().includes(q) || (r.item_code||'').toLowerCase().includes(q));
    }
    renderRows(vis);
}

function formatVal(val, isKg, unit) {
    val = parseFloat(val) || 0;
    if (!isKg) {
        let fmtVal = Number.isInteger(val) ? val : val.toFixed(2);
        return fmtVal + ' ' + (unit || 'PC');
    }
    let isNegative = val < 0;
    let grams = Math.abs(val);
    if (grams >= 1000) {
        const kg = Math.floor(grams / 1000);
        const gm = Math.round(grams % 1000);
        return (isNegative ? '-' : '') + kg + 'kg' + (gm > 0 ? ' ' + gm + 'g' : '');
    } else if (grams > 0) {
        return (isNegative ? '-' : '') + Math.round(grams) + 'g';
    }
    return '0';
}

function formatMoney(amount) {
    amount = parseFloat(amount) || 0;
    return 'Rs ' + Math.round(amount).toLocaleString();
}

function renderRows(rows) {
    const tbody = document.getElementById('reportBody');
    if (!rows || !rows.length) {
        tbody.innerHTML = '<tr><td colspan="13" class="pc-empty"><i class="bi bi-inbox"></i><span>No records found</span></td></tr>';
        setText('rowCount', '0');
        clearFooter();
        return;
    }
    let h = '';
    rows.forEach(function(r, i) {
        const bal = parseFloat(r.balance) || 0;
        const rc = bal <= 0 ? 'row-out' : (bal <= 5 && !r.is_kg ? 'row-low' : '');
        const bc = bal <= 0 ? 'bal-out' : (bal <= 5 && !r.is_kg ? 'bal-low' : 'bal-good');
        const adjInc = parseFloat(r.adj_increase) || 0;
        const adjDec = parseFloat(r.adj_decrease) || 0;
        const prod = parseFloat(r.produced) || 0;
        const prodAmt = parseFloat(r.produced_amount) || 0;
        const sold = parseFloat(r.sold) || 0;
        const soldAmt = parseFloat(r.sold_amount) || 0;
        const balAmt = parseFloat(r.balance_amount) || 0;
        const init = parseFloat(r.initial_stock) || 0;
        const initAmt = parseFloat(r.initial_amount) || 0;
        const price = parseFloat(r.price) || 0;

        h += '<tr class="' + rc + '"><td>' + (i + 1) + '</td>';
        h += '<td><code class="pc-code">' + esc(r.item_code) + '</code></td>';
        h += '<td class="pc-name">' + esc(r.item_name) + '</td>';
        h += '<td class="num bold" style="color:#475569;">Rs ' + fmt(price) + '</td>';
        
        // Initial
        h += '<td class="num">' + formatVal(init, r.is_kg, r.unit) + (initAmt > 0 ? '<br><small class="text-muted">' + formatMoney(initAmt) + '</small>' : '') + '</td>';
        
        // Produced
        h += '<td class="num" style="background:#f0fdf4;">' + 
             (prod > 0 ? '<span class="chip-prod">' + formatVal(prod, r.is_kg, r.unit) + '</span><br><small class="text-success fw-bold">' + formatMoney(prodAmt) + '</small>' : '–') + 
             '</td>';
        
        // Purchased
        h += '<td class="num">' + (r.purchased > 0 ? formatVal(r.purchased, r.is_kg, r.unit) : '–') + '</td>';
        
        // Purchase Return
        h += '<td class="num" style="color:var(--pc-danger)">' + (r.purchase_return > 0 ? formatVal(r.purchase_return, r.is_kg, r.unit) : '–') + '</td>';
        
        // Adjustments
        h += '<td class="num" style="color:var(--pc-success);font-weight:700">' + (adjInc > 0 ? '+' + formatVal(adjInc, r.is_kg, r.unit) : '–') + '</td>';
        h += '<td class="num" style="color:var(--pc-danger);font-weight:700">' + (adjDec > 0 ? '-' + formatVal(adjDec, r.is_kg, r.unit) : '–') + '</td>';
        
        // Sold
        h += '<td class="num" style="background:#faf5ff;">' + 
             (sold > 0 ? '<span class="chip-sold">' + formatVal(sold, r.is_kg, r.unit) + '</span><br><small class="text-purple fw-bold" style="color:#7c3aed;">' + formatMoney(soldAmt) + '</small>' : '–') + 
             '</td>';
        
        // Sale Return
        h += '<td class="num" style="color:#2563eb">' + (r.sale_return > 0 ? formatVal(r.sale_return, r.is_kg, r.unit) : '–') + '</td>';
        
        // Balance
        h += '<td class="num ' + bc + '" style="background:#f8fafc;">' + 
             '<span class="chip-bal ' + bc + '">' + formatVal(bal, r.is_kg, r.unit) + (bal <= 0 ? ' ❌' : '') + '</span>' + 
             (balAmt > 0 ? '<br><span class="fw-bold" style="color:#1e40af;font-size:11.5px;">' + formatMoney(balAmt) + '</span>' : '') + 
             '</td></tr>';
    });
    tbody.innerHTML = h;
    setText('rowCount', rows.length + ' products');
    updateFooter(rows);
}

function updateCards(res) {
    const rows = res.data || [];
    setText('cInStock', rows.filter(r => parseFloat(r.balance) > 5).length);
    setText('cLow', rows.filter(r => parseFloat(r.balance) > 0 && parseFloat(r.balance) <= 5).length);
    setText('cOut', rows.filter(r => parseFloat(r.balance) <= 0).length);

    // Initial Summary (Pehly ka Stock)
    var initQty = res.total_initial_qty !== undefined ? res.total_initial_qty : rows.reduce((s, r) => s + (parseFloat(r.initial_stock) || 0), 0);
    var initAmt = res.total_initial_amount !== undefined ? res.total_initial_amount : rows.reduce((s, r) => s + (parseFloat(r.initial_amount) || 0), 0);
    setText('cInitialQty', Number.isInteger(initQty) ? initQty + ' PC' : initQty.toFixed(1) + ' Units');
    setText('cInitialAmt', formatMoney(initAmt) + ' (Opening)');

    // Produced Summary (Aya Stock)
    var prodQty = res.total_produced_qty !== undefined ? res.total_produced_qty : rows.reduce((s, r) => s + (parseFloat(r.produced) || 0), 0);
    var prodAmt = res.total_produced_amount !== undefined ? res.total_produced_amount : rows.reduce((s, r) => s + (parseFloat(r.produced_amount) || 0), 0);
    setText('cProducedQty', (prodQty > 0 ? '+' : '') + (Number.isInteger(prodQty) ? prodQty + ' PC' : prodQty.toFixed(1) + ' Units'));
    setText('cProducedAmt', (prodAmt > 0 ? '+ ' : '') + formatMoney(prodAmt));

    // Sold Summary (Bika Stock)
    var soldQty = res.total_sold_qty !== undefined ? res.total_sold_qty : rows.reduce((s, r) => s + (parseFloat(r.sold) || 0), 0);
    var soldAmt = res.total_sold_amount !== undefined ? res.total_sold_amount : rows.reduce((s, r) => s + (parseFloat(r.sold_amount) || 0), 0);
    setText('cSoldQty', (soldQty > 0 ? '-' : '') + (Number.isInteger(soldQty) ? soldQty + ' PC' : soldQty.toFixed(1) + ' Units'));
    setText('cSoldAmt', (soldAmt > 0 ? '- ' : '') + formatMoney(soldAmt));

    // Stock Balance Summary (Ab Bacha Stock)
    var stockQty = res.total_stock_qty !== undefined ? res.total_stock_qty : rows.reduce((s, r) => s + (parseFloat(r.balance) || 0), 0);
    var stockAmt = res.grand_total !== undefined ? res.grand_total : rows.reduce((s, r) => s + (parseFloat(r.balance_amount) || 0), 0);
    setText('cStockQty', '= ' + (Number.isInteger(stockQty) ? stockQty + ' PC' : stockQty.toFixed(1) + ' Units'));
    setText('cStockAmt', '= ' + formatMoney(stockAmt) + ' (Valuation)');

    // Purchased Summary
    var purchQty = res.total_purchased_qty !== undefined ? res.total_purchased_qty : rows.reduce((s, r) => s + (parseFloat(r.purchased) || 0), 0);
    var purchAmt = res.total_purchased_amount !== undefined ? res.total_purchased_amount : rows.reduce((s, r) => s + (parseFloat(r.purchased_amount) || 0), 0);
    setText('cPurchQty', Number.isInteger(purchQty) ? purchQty + ' PC' : purchQty.toFixed(1) + ' Units');
    setText('cPurchAmt', formatMoney(purchAmt));
}

function updateFooter(rows) {
    function sm(f) { return rows.reduce(function(s, r) { return s + (parseFloat(r[f]) || 0); }, 0); }
    
    var totInit = sm('initial_stock');
    var totInitAmt = sm('initial_amount');
    setText('ftInitial', fmt(totInit) + (totInitAmt > 0 ? ' (' + formatMoney(totInitAmt) + ')' : ''));

    var totProd = sm('produced');
    var totProdAmt = sm('produced_amount');
    setText('ftProduced', fmt(totProd) + (totProdAmt > 0 ? ' (' + formatMoney(totProdAmt) + ')' : ''));

    setText('ftPurch', fmt(sm('purchased')));
    setText('ftPReturn', fmt(sm('purchase_return')));
    setText('ftAdjInc', fmt(sm('adj_increase')));
    setText('ftAdjDec', fmt(sm('adj_decrease')));

    var totSold = sm('sold');
    var totSoldAmt = sm('sold_amount');
    setText('ftSold', fmt(totSold) + (totSoldAmt > 0 ? ' (' + formatMoney(totSoldAmt) + ')' : ''));

    setText('ftSReturn', fmt(sm('sale_return')));

    var totBal = sm('balance');
    var totBalAmt = sm('balance_amount');
    setText('ftBal', fmt(totBal) + ' (' + formatMoney(totBalAmt) + ')');
}

function clearFooter() {
    ['ftInitial','ftProduced','ftPurch','ftPReturn','ftAdjInc','ftAdjDec','ftSold','ftSReturn','ftBal'].forEach(function(id) {
        var el = document.getElementById(id); if (el) el.textContent = '–';
    });
}

/* ═══════ VARIANT STOCK ═══════ */
let allVarRows = [];
let varHasStockOnly = false;
let varStatusFilter = 'all';

window.toggleVarHasStock = function() {
    varHasStockOnly = !varHasStockOnly;
    varStatusFilter = varHasStockOnly ? 'has_stock' : 'all';
    updateVarHasStockBtn();
    applyVarFilter();
};

function updateVarHasStockBtn() {
    const btn = $('#btnVarHasStock');
    if (varHasStockOnly || varStatusFilter === 'ok' || varStatusFilter === 'has_stock') {
        btn.removeClass('pc-btn-outline')
           .addClass('pc-btn-primary')
           .css({'background':'#4f46e5','color':'#fff','border-color':'#4f46e5'})
           .html('<i class="bi bi-check-circle-fill"></i> <span>Has Stock (Active)</span>');
    } else {
        btn.removeClass('pc-btn-primary')
           .addClass('pc-btn-outline')
           .css({'background':'','color':'','border-color':''})
           .html('<i class="bi bi-box-seam"></i> <span>Has Stock</span>');
    }
}

window.filterVarCard = function(status) {
    varStatusFilter = status;
    varHasStockOnly = (status === 'ok');
    updateVarHasStockBtn();
    applyVarFilter();
};

function fetchVariants() {
    const productId = $('#v_product_id').val() || 'all';
    document.getElementById('variantBody').innerHTML = '<div class="pc-spinner"><i class="bi bi-arrow-repeat"></i> Loading…</div>';
    ['vTotal','vOk','vLow','vOut','vUnits'].forEach(function(id) {
        var el = document.getElementById(id); if (el) el.textContent = '…';
    });
    $.ajax({
        url: "{{ route('report.variant_stock.fetch') }}", 
        type: 'POST', 
        dataType: 'json',
        data: { _token: "{{ csrf_token() }}", product_id: productId },
        success: function(res) {
            allVarRows = res.data || [];
            document.getElementById('vLiveSearch').value = '';
            applyVarFilter();
            updateVarCards(allVarRows);
        },
        error: function() {
            document.getElementById('variantBody').innerHTML = '<div class="pc-empty" style="color:var(--pc-danger)"><i class="bi bi-exclamation-triangle"></i><span>Error loading data.</span></div>';
        }
    });
}

function applyVarFilter() {
    const q = (document.getElementById('vLiveSearch').value || '').toLowerCase().trim();
    let vis = allVarRows;

    if (varHasStockOnly || varStatusFilter === 'has_stock') {
        vis = vis.filter(r => (parseFloat(r.total_stock) || 0) > 0 || (r.sizes && r.sizes.some(s => (parseFloat(s.stock_qty) || 0) > 0)));
    } else if (varStatusFilter === 'ok') {
        vis = vis.filter(r => r.sizes && r.sizes.some(s => s.status === 'ok'));
    } else if (varStatusFilter === 'low') {
        vis = vis.filter(r => r.sizes && r.sizes.some(s => s.status === 'low'));
    } else if (varStatusFilter === 'out') {
        vis = vis.filter(r => r.sizes && r.sizes.every(s => s.status === 'out'));
    }

    if (q) {
        vis = vis.filter(r => (r.product_name||'').toLowerCase().includes(q) || (r.item_code||'').toLowerCase().includes(q) || (r.sizes && r.sizes.some(s => (s.label||'').toLowerCase().includes(q))));
    }
    renderVariants(vis);
}

function renderVariants(data) {
    const body = document.getElementById('variantBody');
    if (!data || !data.length) {
        body.innerHTML = '<div class="pc-empty"><i class="bi bi-rulers"></i><span>No size/variant data found.<br><small>Only products with sizes will appear.</small></span></div>';
        setText('vRowCount', '0 products');
        return;
    }
    let h = '';
    data.forEach(function(prod) {
        const totStk = prod.sizes.reduce(function(s, v) { return s + (v.stock_qty || 0); }, 0);
        h += '<div class="pc-vp"><span class="badge">' + esc(prod.item_code) + '</span><strong>' + esc(prod.product_name) + '</strong>';
        if (prod.category && prod.category !== '–') h += '<span style="font-size:.7rem;background:#f0f2f7;color:#666;border-radius:5px;padding:2px 7px;font-weight:600;">' + esc(prod.category) + '</span>';
        h += '<span class="tot">Total: ' + fmt(totStk) + '</span></div>';
        h += '<div class="pc-sz-grid">';
        prod.sizes.forEach(function(sz) {
            const stk = sz.stock_qty || 0;
            const cls = sz.status;
            const statusTxt = cls === 'out' ? 'Out of Stock' : (cls === 'low' ? 'Low Stock ⚠️' : 'In Stock ✅');
            h += '<div class="pc-sz-card ' + cls + '"><div class="sz-lbl">' + esc(sz.label) + '</div>';
            h += '<div class="sz-stk">' + parseFloat(stk).toFixed(0) + '</div>';
            h += '<div class="sz-price">Rs ' + fmt(sz.price) + '</div>';
            h += '<div class="sz-status">' + statusTxt + '</div></div>';
        });
        h += '</div>';
    });
    body.innerHTML = h;
    setText('vRowCount', data.length + ' products');
}

function updateVarCards(data) {
    let ok = 0, low = 0, out = 0, units = 0;
    data.forEach(function(p) {
        p.sizes.forEach(function(s) {
            if (s.status === 'ok') ok++;
            else if (s.status === 'low') low++;
            else out++;
            units += (s.stock_qty || 0);
        });
    });
    setText('vTotal', data.length);
    setText('vOk', ok);
    setText('vLow', low);
    setText('vOut', out);
    setText('vUnits', parseFloat(units).toFixed(0));
}

/* ═══════ HELPERS ═══════ */
function showSpinner() {
    document.getElementById('reportBody').innerHTML = '<tr><td colspan="13"><div class="pc-spinner"><i class="bi bi-arrow-repeat"></i> Loading…</div></td></tr>';
}

function fmt(v) { return parseFloat(v || 0).toFixed(2); }

function esc(s) { return (s || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function setText(id, v) { var el = document.getElementById(id); if (el) el.textContent = v; }
</script>
@endsection
