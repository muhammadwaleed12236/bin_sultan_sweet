@extends('admin_panel.layout.app')
@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --pc-bg: #f1f4f9;
  --pc-surface: #ffffff;
  --pc-border: #e9edf2;
  --pc-border-lt: #f1f4f9;
  --pc-text: #0b1a33;
  --pc-text-sec: #54657e;
  --pc-text-muted: #8896ab;
  --pc-accent: #2b7fff;
  --pc-accent-drk: #1a6ae8;
  --pc-success: #0fae6b;
  --pc-danger: #e54545;
  --pc-warning: #f5a623;
  --pc-radius: 14px;
  --pc-radius-sm: 9px;
  --pc-shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
  --pc-shadow-lg: 0 8px 30px rgba(0,0,0,.07), 0 3px 12px rgba(0,0,0,.04);
  --pc-shadow-xl: 0 20px 60px rgba(0,0,0,.10), 0 8px 24px rgba(0,0,0,.06);
  --pc-font: 'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif;
}

.pc-page * { font-family: var(--pc-font); }
.pc-page { background: var(--pc-bg); min-height: 100vh; padding-bottom: 2.5rem; }

/* ═══════ HEADER ═══════ */
.pc-hdr {
  position: relative;
  background: linear-gradient(135deg, #0b1a33 0%, #162d50 50%, #1a4d8c 100%);
  border-radius: var(--pc-radius);
  padding: 1.3rem 2rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--pc-shadow-xl);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  overflow: hidden;
}

.pc-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 60% 50% at 10% 90%, rgba(43,127,255,.15) 0%, transparent 100%),
    radial-gradient(ellipse 40% 40% at 90% 10%, rgba(43,127,255,.08) 0%, transparent 100%);
  pointer-events: none;
}

.pc-hdr::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  opacity: .5;
  pointer-events: none;
}

.pc-hdr > * { position: relative; z-index: 1; }

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

.pc-hdr h2 i { font-size: 1.4rem; color: #60a5fa; }

.pc-btn {
  display: inline-flex;
  align-items: center;
  gap: .45rem;
  border-radius: var(--pc-radius-sm);
  font-weight: 600;
  font-size: .82rem;
  transition: all .25s ease;
  cursor: pointer;
  text-decoration: none;
  border: none;
  padding: .5rem 1.35rem;
}

.pc-btn-primary {
  background: linear-gradient(135deg, var(--pc-accent) 0%, var(--pc-accent-drk) 100%);
  color: #fff;
}

.pc-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(43,127,255,.25);
  color: #fff;
}

.pc-btn-dark {
  background: #0b1a33;
  color: #fff;
}

.pc-btn-dark:hover { background: #162d50; color: #fff; }

.pc-btn-outline {
  background: transparent;
  border: 1.5px solid var(--pc-border);
  color: var(--pc-text-sec);
}

.pc-btn-outline:hover { border-color: var(--pc-accent); color: var(--pc-accent); }

/* ═══════ CARD ═══════ */
.pc-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius);
  box-shadow: var(--pc-shadow);
  transition: box-shadow .3s ease;
}

.pc-card:hover { box-shadow: var(--pc-shadow-lg); }
.pc-card-body { padding: 1.5rem; }

/* ═══════ SUMMARY CARDS ═══════ */
.pc-sum-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
  gap: 12px;
  margin-bottom: 16px;
}

.pc-sum-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: 14px 16px;
  box-shadow: var(--pc-shadow);
  border-left: 4px solid var(--pc-accent);
}

.pc-sum-card.green { border-color: var(--pc-success); }
.pc-sum-card.orange { border-color: var(--pc-warning); }
.pc-sum-card.red { border-color: var(--pc-danger); }
.pc-sum-card.purple { border-color: #764ba2; }

.pc-sum-card .lbl {
  font-size: .68rem;
  color: var(--pc-text-muted);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
}

.pc-sum-card .val {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--pc-text);
  margin-top: 4px;
}

/* ═══════ FILTER BAR ═══════ */
.pc-filter {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: 16px 18px;
  box-shadow: var(--pc-shadow);
  margin-bottom: 16px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: flex-end;
}

.pc-filter .fg { display: flex; flex-direction: column; gap: 4px; }
.pc-filter label {
  font-size: .68rem;
  font-weight: 700;
  color: var(--pc-text-sec);
  text-transform: uppercase;
  letter-spacing: .4px;
}

.pc-filter .pc-fld {
  border: 1.5px solid var(--pc-border);
  border-radius: 7px;
  padding: .45rem .75rem;
  font-size: .83rem;
  font-weight: 500;
  color: var(--pc-text);
  outline: none;
  transition: all .2s ease;
}

.pc-filter .pc-fld:focus { border-color: var(--pc-accent); box-shadow: 0 0 0 3px rgba(43,127,255,.1); }

.pc-filter .fg-wide { flex: 1; min-width: 200px; }

/* ═══════ TABLE ═══════ */
.pc-tbl-wrap {
  overflow-x: auto;
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
}

.pc-tbl {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: .82rem;
}

.pc-tbl thead th {
  background: #f8fafc;
  font-size: .65rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: var(--pc-text-muted);
  padding: .55rem .7rem;
  border-bottom: 2px solid var(--pc-border);
  text-align: left;
  white-space: nowrap;
}

.pc-tbl thead th.num { text-align: right; }
.pc-tbl tbody td { padding: .5rem .7rem; border-bottom: 1px solid var(--pc-border-lt); vertical-align: middle; }
.pc-tbl tbody tr { transition: background .12s ease; }
.pc-tbl tbody tr:hover { background: #fafbfc; }
.pc-tbl tbody tr:last-child td { border-bottom: none; }
.pc-tbl tbody td.num { text-align: right; font-variant-numeric: tabular-nums; }
.pc-tbl tbody td.bold { font-weight: 700; }

.pc-tbl .pc-code {
  font-size: .72rem;
  background: #f5f5f5;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: 'Consolas', monospace;
}

.pc-tbl .pc-name { font-weight: 600; }

.bal-good { color: var(--pc-success); font-weight: 700; }
.bal-low { color: var(--pc-warning); font-weight: 700; }
.bal-out { color: var(--pc-danger); font-weight: 700; }

.pc-tbl tr.row-out { background: #fff5f5 !important; }
.pc-tbl tr.row-low { background: #fffbf0 !important; }

.pc-tfoot {
  background: #f8fafc;
  font-weight: 700;
}

.pc-tfoot td {
  padding: .55rem .7rem;
  border-top: 2px solid var(--pc-border);
  font-size: .82rem;
}

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
  padding-left: 32px; border: 1.5px solid var(--pc-border); border-radius: 7px;
  padding: .45rem .7rem .45rem 32px; font-size: .82rem; outline: none;
  min-width: 220px;
}
.pc-ls-wrap input:focus { border-color: var(--pc-accent); }
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
  color: #fff; box-shadow: 0 3px 10px rgba(43,127,255,.3);
}

/* ═══════ VARIANT CARDS ═══════ */
.pc-vp { background: #f4f6ff; padding: 10px 16px; font-weight: 700; font-size: .8rem; color: var(--pc-text); border-top: 2px solid #e8ebf7; display: flex; align-items: center; gap: 10px; }
.pc-vp .badge { font-size: .65rem; background: var(--pc-accent); color: #fff; border-radius: 5px; padding: 2px 7px; }
.pc-vp .tot { margin-left: auto; font-size: .72rem; color: var(--pc-text-muted); }

.pc-sz-grid { display: flex; flex-wrap: wrap; gap: 8px; padding: 12px 16px 14px; border-bottom: 1px solid var(--pc-border-lt); }
.pc-sz-card { border-radius: 8px; padding: 10px 14px; min-width: 120px; text-align: center; border: 2px solid var(--pc-border); transition: .15s; }
.pc-sz-card:hover { border-color: var(--pc-accent); box-shadow: 0 2px 8px rgba(43,127,255,.12); }
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
  .pc-tbl tbody td { padding: .4rem .5rem; }
}
</style>

<div class="pc-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══ HEADER ═══ --}}
    <div class="pc-hdr">
      <div class="d-flex align-items-center gap-3">
        <h2><i class="bi bi-boxes"></i>Stock Reports</h2>
        <span class="hdr-badge d-none d-sm-inline" id="totalBadge">0 Products</span>
      </div>
      <div class="d-flex align-items-center gap-2 flex-wrap hdr-actions">
        <button class="pc-btn pc-btn-dark" onclick="printClosing()">
          <i class="bi bi-receipt"></i>Print Closing
        </button>
      </div>
    </div>

    {{-- ═══ TABS ═══ --}}
    <div class="pc-tabs">
      <button class="pc-tab active" id="tabItem" onclick="switchTab('item')">📋 Item Stock</button>
      <button class="pc-tab" id="tabSize" onclick="switchTab('size')">📐 Size / Variant Stock</button>
    </div>

    {{-- ═══ ITEM STOCK PANEL ═══ --}}
    <div id="panelItem">

      {{-- Summary Cards --}}
      <div class="pc-sum-grid">
        <div class="pc-sum-card" onclick="filterReportCard('all')" style="cursor:pointer;" title="Click to show all products"><div class="lbl">Total Products</div><div class="val" id="cTotal">–</div></div>
        <div class="pc-sum-card green" onclick="filterReportCard('ok')" style="cursor:pointer;" title="Click to show in stock products"><div class="lbl">In Stock</div><div class="val" id="cInStock">–</div></div>
        <div class="pc-sum-card orange" onclick="filterReportCard('low')" style="cursor:pointer;" title="Click to show low stock products"><div class="lbl">Low Stock (≤5)</div><div class="val" id="cLow">–</div></div>
        <div class="pc-sum-card red" onclick="filterReportCard('out')" style="cursor:pointer;" title="Click to show out of stock products"><div class="lbl">Out of Stock</div><div class="val" id="cOut">–</div></div>
        <div class="pc-sum-card"><div class="lbl">Total Sold</div><div class="val" id="cSold">–</div></div>
        <div class="pc-sum-card"><div class="lbl">Total Purchased</div><div class="val" id="cPurch">–</div></div>
        <div class="pc-sum-card purple"><div class="lbl">Inventory Value</div><div class="val" id="cValue">–</div></div>
      </div>

      {{-- Filter --}}
      <div class="pc-filter">
        <div class="fg fg-wide">
          <label>Filter by Product</label>
          <select id="product_id" class="select2-item pc-fld" multiple style="width:100%">
            <option value="all">— All Products —</option>
            @foreach($products as $prod)
            <option value="{{ $prod->id }}">{{ $prod->item_code }} – {{ $prod->item_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="fg">
          <label>Start Date</label>
          <input type="date" class="pc-fld" id="start_date" value="{{ date('Y-m-01') }}">
        </div>
        <div class="fg">
          <label>Start Time</label>
          <input type="time" class="pc-fld" id="start_time" value="07:00">
        </div>
        <div class="fg">
          <label>End Date</label>
          <input type="date" class="pc-fld" id="end_date" value="{{ date('Y-m-d') }}">
        </div>
        <div class="fg">
          <label>End Time</label>
          <input type="time" class="pc-fld" id="end_time" value="23:59">
        </div>
        <button class="pc-btn pc-btn-primary" onclick="fetchReport()"><i class="bi bi-search"></i>Search</button>
        <button type="button" id="btnReportHasStock" class="pc-btn pc-btn-outline" onclick="toggleReportHasStock()" style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
          <i class="bi bi-box-seam"></i> <span>Has Stock</span>
        </button>
      </div>

      {{-- Table Card --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 style="font-weight:700;color:var(--pc-text);margin:0;font-size:.9rem;"><i class="bi bi-table me-1"></i>Stock Detail</h6>
            <div class="d-flex align-items-center gap-2">
              <div class="pc-ls-wrap">
                <i class="bi bi-search ls-ico"></i>
                <input type="text" id="liveSearch" placeholder="Search by name / code..." oninput="applyFilter()">
              </div>
              <span style="font-size:.75rem;color:var(--pc-text-muted);font-weight:600;" id="rowCount">–</span>
            </div>
          </div>
          <div class="pc-tbl-wrap" style="max-height:calc(100vh - 380px);overflow-y:auto;">
            <table class="pc-tbl">
              <thead style="position:sticky;top:0;z-index:2;">
                <tr>
                  <th>#</th><th>Item Code</th><th>Item Name</th>
                  <th class="num">Initial</th><th class="num">Produced</th>
                  <th class="num">Purchased</th><th class="num">Purch. Return</th>
                  <th class="num" style="color:var(--pc-success)">Adj ➕</th>
                  <th class="num" style="color:var(--pc-danger)">Adj ➖</th>
                  <th class="num">Sold</th><th class="num">Sale Return</th>
                  <th class="num">Stock Qty</th>
                </tr>
              </thead>
              <tbody id="reportBody">
                <tr><td colspan="12" class="pc-empty"><i class="bi bi-search"></i><span>Click Search to load data</span></td></tr>
              </tbody>
              <tfoot class="pc-tfoot">
                <tr>
                  <td colspan="3" style="text-align:right;color:var(--pc-text-sec);font-size:.72rem;text-transform:uppercase">Totals:</td>
                  <td class="num" id="ftInitial">–</td><td class="num" id="ftProduced">–</td>
                  <td class="num" id="ftPurch">–</td><td class="num" id="ftPReturn">–</td>
                  <td class="num" id="ftAdjInc" style="color:var(--pc-success)">–</td>
                  <td class="num" id="ftAdjDec" style="color:var(--pc-danger)">–</td>
                  <td class="num" id="ftSold">–</td><td class="num" id="ftSReturn">–</td>
                  <td class="num bold" id="ftBal" style="color:var(--pc-accent)">–</td>
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
        <button type="button" id="btnVarHasStock" class="pc-btn pc-btn-outline" onclick="toggleVarHasStock()" style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
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
<script>
$(document).ready(function() {
    $('.select2-item').select2({ placeholder:'Search product…', allowClear:true, width:'100%' });
    $('.select2-var').select2({ placeholder:'Search product…', allowClear:true, width:'100%' });
    fetchReport();
});

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
    var startDate = $('#start_date').val();
    var endDate   = $('#end_date').val();
    var startTime = $('#start_time').val() || '07:00';
    var endTime   = $('#end_time').val() || '03:00';
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
let reportStatusFilter = 'all'; // 'all', 'ok', 'low', 'out', 'has_stock'

window.toggleReportHasStock = function() {
    reportHasStockOnly = !reportHasStockOnly;
    if (reportHasStockOnly) {
        reportStatusFilter = 'has_stock';
    } else {
        reportStatusFilter = 'all';
    }
    updateReportHasStockBtn();
    applyFilter();
};

function updateReportHasStockBtn() {
    const btn = $('#btnReportHasStock');
    if (reportHasStockOnly || reportStatusFilter === 'ok' || reportStatusFilter === 'has_stock') {
        btn.removeClass('pc-btn-outline')
           .addClass('pc-btn-primary')
           .css({'background':'#2b7fff','color':'#fff','border-color':'#2b7fff'})
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
    if (status === 'ok') {
        reportHasStockOnly = true;
    } else if (status === 'all') {
        reportHasStockOnly = false;
    } else {
        reportHasStockOnly = false;
    }
    updateReportHasStockBtn();
    applyFilter();
};

function fetchReport() {
    const productIds = getProductIds();
    const startDate = $('#start_date').val();
    const endDate   = $('#end_date').val();
    const startTime = $('#start_time').val() || '00:00:00';
    const endTime   = $('#end_time').val() || '23:59:59';
    showSpinner();
    var data = { _token:"{{ csrf_token() }}", start_date:startDate, end_date:endDate, start_time:startTime, end_time:endTime };
    if (productIds === 'all') { data.product_id = 'all'; }
    else { data.product_id = productIds; }
    $.ajax({
        url: "{{ route('report.item_stock.fetch') }}", type:'POST', dataType:'json',
        data: data,
        success: function(res) {
            allRows = res.data || [];
            document.getElementById('liveSearch').value = '';
            applyFilter();
            updateCards(res);
            document.getElementById('totalBadge').textContent = allRows.length + ' Products';
        },
        error: function() {
            document.getElementById('reportBody').innerHTML = '<tr><td colspan="12" class="pc-empty" style="color:var(--pc-danger)"><i class="bi bi-exclamation-triangle"></i><span>Error loading data. Check console.</span></td></tr>';
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

function renderRows(rows) {
    const tbody = document.getElementById('reportBody');
    if (!rows || !rows.length) {
        tbody.innerHTML = '<tr><td colspan="12" class="pc-empty"><i class="bi bi-inbox"></i><span>No records found</span></td></tr>';
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

        h += '<tr class="' + rc + '"><td>' + (i + 1) + '</td>';
        h += '<td><code class="pc-code">' + esc(r.item_code) + '</code></td>';
        h += '<td class="pc-name">' + esc(r.item_name) + '</td>';
        h += '<td class="num">' + formatVal(r.initial_stock, r.is_kg, r.unit) + '</td>';
        h += '<td class="num">' + formatVal(r.produced, r.is_kg, r.unit) + '</td>';
        h += '<td class="num">' + formatVal(r.purchased, r.is_kg, r.unit) + '</td>';
        h += '<td class="num" style="color:var(--pc-danger)">' + formatVal(r.purchase_return, r.is_kg, r.unit) + '</td>';
        h += '<td class="num" style="color:var(--pc-success);font-weight:700">' + (adjInc > 0 ? '+' + formatVal(adjInc, r.is_kg, r.unit) : '–') + '</td>';
        h += '<td class="num" style="color:var(--pc-danger);font-weight:700">' + (adjDec > 0 ? '-' + formatVal(adjDec, r.is_kg, r.unit) : '–') + '</td>';
        h += '<td class="num" style="color:#8e44ad">' + formatVal(r.sold, r.is_kg, r.unit) + '</td>';
        h += '<td class="num" style="color:#2980b9">' + formatVal(r.sale_return, r.is_kg, r.unit) + '</td>';
        h += '<td class="num ' + bc + '">' + formatVal(bal, r.is_kg, r.unit) + (bal <= 0 ? ' ❌' : '') + '</td></tr>';
    });
    tbody.innerHTML = h;
    setText('rowCount', rows.length + ' products');
    updateFooter(rows);
}

function updateCards(res) {
    const rows = res.data || [];
    setText('cTotal', rows.length);
    setText('cInStock', rows.filter(r => parseFloat(r.balance) > 5).length);
    setText('cLow', rows.filter(r => parseFloat(r.balance) > 0 && parseFloat(r.balance) <= 5).length);
    setText('cOut', rows.filter(r => parseFloat(r.balance) <= 0).length);
    setText('cSold', rows.length > 0 ? rows.reduce((s, r) => s + (parseFloat(r.sold) || 0), 0).toFixed(0) : '0');
    setText('cPurch', rows.length > 0 ? rows.reduce((s, r) => s + (parseFloat(r.purchased) || 0), 0).toFixed(0) : '0');
    if (res.grand_total !== undefined) {
        setText('cValue', 'Rs ' + Math.round(res.grand_total).toLocaleString());
    }
}

function updateFooter(rows) {
    function sm(f) { return rows.reduce(function(s, r) { return s + (parseFloat(r[f]) || 0); }, 0); }
    setText('ftInitial', fmt(sm('initial_stock')));
    setText('ftProduced', fmt(sm('produced')));
    setText('ftPurch', fmt(sm('purchased')));
    setText('ftPReturn', fmt(sm('purchase_return')));
    setText('ftAdjInc', fmt(sm('adj_increase')));
    setText('ftAdjDec', fmt(sm('adj_decrease')));
    setText('ftSold', fmt(sm('sold')));
    setText('ftSReturn', fmt(sm('sale_return')));
    setText('ftBal', fmt(sm('balance')));
}

function clearFooter() {
    ['ftInitial','ftProduced','ftPurch','ftPReturn','ftAdjInc','ftAdjDec','ftSold','ftSReturn','ftBal'].forEach(function(id) {
        var el = document.getElementById(id); if (el) el.textContent = '–';
    });
}

/* ═══════ VARIANT STOCK ═══════ */
let allVarRows = [];
let varHasStockOnly = false;
let varStatusFilter = 'all'; // 'all', 'ok', 'low', 'out', 'has_stock'

window.toggleVarHasStock = function() {
    varHasStockOnly = !varHasStockOnly;
    if (varHasStockOnly) {
        varStatusFilter = 'has_stock';
    } else {
        varStatusFilter = 'all';
    }
    updateVarHasStockBtn();
    applyVarFilter();
};

function updateVarHasStockBtn() {
    const btn = $('#btnVarHasStock');
    if (varHasStockOnly || varStatusFilter === 'ok' || varStatusFilter === 'has_stock') {
        btn.removeClass('pc-btn-outline')
           .addClass('pc-btn-primary')
           .css({'background':'#2b7fff','color':'#fff','border-color':'#2b7fff'})
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
    if (status === 'ok') {
        varHasStockOnly = true;
    } else if (status === 'all') {
        varHasStockOnly = false;
    } else {
        varHasStockOnly = false;
    }
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
        url: "{{ route('report.variant_stock.fetch') }}", type:'POST', dataType:'json',
        data: { _token:"{{ csrf_token() }}", product_id:productId },
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
    document.getElementById('reportBody').innerHTML = '<tr><td colspan="12"><div class="pc-spinner"><i class="bi bi-arrow-repeat"></i> Loading…</div></td></tr>';
}

function fmt(v) { return parseFloat(v || 0).toFixed(2); }

function esc(s) { return (s || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function setText(id, v) { var el = document.getElementById(id); if (el) el.textContent = v; }
</script>
@endsection
