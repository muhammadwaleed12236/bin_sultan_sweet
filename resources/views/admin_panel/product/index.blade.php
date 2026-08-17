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

.pc-page {
  background: var(--pc-bg);
  min-height: 100vh;
  padding-bottom: 2.5rem;
}

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
  gap: .4rem;
  border-radius: var(--pc-radius-sm);
  font-weight: 600;
  font-size: .78rem;
  transition: all .25s ease;
  cursor: pointer;
  text-decoration: none;
  border: none;
  padding: .45rem 1.1rem;
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

.pc-btn-outline {
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  color: #fff;
}

.pc-btn-outline:hover { background: rgba(255,255,255,.14); color: #fff; }

.pc-btn-sm { font-size: .72rem; padding: .35rem .85rem; }

.pc-btn-danger {
  background: #e54545;
  color: #fff;
}

.pc-btn-danger:hover { background: #cc3333; color: #fff; }

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

/* ═══════ SEARCH BAR ═══════ */
.pc-search {
  border: 1.5px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: .5rem .85rem;
  font-size: .85rem;
  font-weight: 500;
  color: var(--pc-text);
  background: var(--pc-surface);
  outline: none;
  transition: all .25s ease;
}

.pc-search:focus {
  border-color: var(--pc-accent);
  box-shadow: 0 0 0 3px rgba(43,127,255,.1);
}

.pc-search::placeholder { color: var(--pc-text-muted); font-weight: 400; }

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
  font-size: .67rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: var(--pc-text-muted);
  padding: .55rem .7rem;
  border-bottom: 2px solid var(--pc-border);
  text-align: left;
  white-space: nowrap;
}

.pc-tbl tbody td {
  padding: .5rem .7rem;
  border-bottom: 1px solid var(--pc-border-lt);
  vertical-align: middle;
}

.pc-tbl tbody tr { transition: background .12s ease; }
.pc-tbl tbody tr:hover { background: #fafbfc; }
.pc-tbl tbody tr:last-child td { border-bottom: none; }

.pc-tbl .pc-code { font-weight: 700; color: var(--pc-text); white-space: nowrap; }

.pc-tbl .pc-bc-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: #f0f4fe;
  border: 1px solid #dde4f7;
  border-radius: 5px;
  padding: .15rem .55rem;
  font-size: .72rem;
  font-weight: 700;
  color: #3b5bb3;
  letter-spacing: .3px;
  font-family: 'Consolas', 'Courier New', monospace;
}

.pc-tbl .pc-cat { line-height: 1.3; }
.pc-tbl .pc-cat strong { font-size: .8rem; color: var(--pc-text); }
.pc-tbl .pc-cat small { font-size: .72rem; color: var(--pc-text-muted); }

.pc-tbl .pc-name-wrap { line-height: 1.3; }
.pc-tbl .pc-name-wrap strong { font-size: .8rem; color: var(--pc-text); }

.pc-badge {
  display: inline-flex;
  border-radius: 5px;
  padding: .15rem .55rem;
  font-size: .7rem;
  font-weight: 700;
  text-transform: uppercase;
}

.pc-badge-kg { background: #eef2ff; color: #3b5bb3; }
.pc-badge-pc { background: #f0fdf4; color: #0f7a47; }
.pc-badge-lb { background: #fffbeb; color: #a16207; }

.pc-tbl .pc-variant-list { font-size: .72rem; }
.pc-tbl .pc-variant-list > div {
  padding: 1px 0;
  border-bottom: 1px dashed var(--pc-border-lt);
  display: flex;
  justify-content: space-between;
  gap: 6px;
}
.pc-tbl .pc-variant-list > div:last-child { border-bottom: none; }
.pc-tbl .pc-variant-list .v-default { font-size: .6rem; background: #e0f2fe; color: #0369a1; border-radius: 3px; padding: 0 4px; }

.pc-tbl .pc-price { font-weight: 700; white-space: nowrap; }
.pc-tbl .pc-price del { font-weight: 400; font-size: .75rem; }

.pc-tbl .pc-disc-badge {
  display: inline-block;
  background: #fef2f2;
  color: #991b1b;
  font-size: .65rem;
  font-weight: 700;
  border-radius: 4px;
  padding: .05rem .45rem;
  margin-bottom: 2px;
}

.pc-tbl .pc-stock { font-weight: 600; white-space: nowrap; }

.pc-tbl .pc-actions {
  display: flex;
  gap: 4px;
  flex-wrap: nowrap;
}

.pc-act {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 3px;
  border-radius: 5px;
  padding: .3rem .65rem;
  font-size: .7rem;
  font-weight: 600;
  transition: all .2s ease;
  text-decoration: none;
  cursor: pointer;
  border: 1.5px solid transparent;
  white-space: nowrap;
}

.pc-act-edit { background: #eef2ff; border-color: #dde4f7; color: #3b5bb3; }
.pc-act-edit:hover { background: #dde4f7; color: #2a4a9e; }

.pc-act-bc {
  background: #f0fdf4;
  border-color: #b8e6c8;
  color: #0f7a47;
}

.pc-act-bc:hover { background: #d4f0dc; color: #0a5f35; }

/* ═══════ PAGINATION ═══════ */
.pc-pagi {
  margin-top: 1rem;
  display: flex;
  justify-content: center;
}

.pc-pagi nav span, .pc-pagi nav a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 34px;
  height: 34px;
  padding: 0 .55rem;
  margin: 0 2px;
  border: 1.5px solid var(--pc-border);
  border-radius: 6px;
  font-size: .8rem;
  font-weight: 600;
  color: var(--pc-text-sec);
  text-decoration: none;
  transition: all .2s ease;
}

.pc-pagi nav a:hover { border-color: var(--pc-accent); color: var(--pc-accent); }

/* ═══════ MODAL ═══════ */
#addProductModal .modal-content {
  border: none;
  border-radius: var(--pc-radius);
  box-shadow: var(--pc-shadow-xl);
}

#addProductModal .modal-header {
  background: linear-gradient(135deg, #0b1a33 0%, #162d50 100%);
  color: #fff;
  border-radius: var(--pc-radius) var(--pc-radius) 0 0;
  border: none;
  padding: 1rem 1.5rem;
}

#addProductModal .modal-header .btn-close { filter: brightness(0) invert(1); opacity: .6; }
#addProductModal .modal-header .btn-close:hover { opacity: 1; }
#addProductModal .modal-header h5 { font-weight: 700; font-size: 1rem; }

#addProductModal .modal-body { padding: 1.5rem; }

#addProductModal .modal-footer {
  border-top: 1px solid var(--pc-border-lt);
  padding: 1rem 1.5rem;
}

#addProductModal .cat-lbl {
  font-size: .78rem;
  font-weight: 600;
  color: var(--pc-text-sec);
  margin-bottom: .3rem;
}

#addProductModal .cat-fld {
  border: 1.5px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: .45rem .75rem;
  font-size: .84rem;
  font-weight: 500;
  color: var(--pc-text);
  background: var(--pc-surface);
  transition: all .25s ease;
  width: 100%;
  outline: none;
}

#addProductModal .cat-fld:focus {
  border-color: var(--pc-accent);
  box-shadow: 0 0 0 3px rgba(43,127,255,.1);
}

#addProductModal .cat-btn-p {
  background: linear-gradient(135deg, var(--pc-accent) 0%, var(--pc-accent-drk) 100%);
  border: none;
  border-radius: var(--pc-radius-sm);
  padding: .45rem 1.5rem;
  font-weight: 600;
  font-size: .83rem;
  color: #fff;
  transition: all .3s ease;
  cursor: pointer;
}

#addProductModal .cat-btn-p:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(43,127,255,.25);
  color: #fff;
}

/* ═══════ RESPONSIVE ═══════ */
@media (max-width: 768px) {
  .pc-hdr { padding: 1rem 1.25rem; flex-direction: column; align-items: stretch; gap: .5rem; }
  .pc-hdr h2 { font-size: 1.1rem; }
  .pc-hdr .hdr-actions { justify-content: flex-start; flex-wrap: wrap; }
  .pc-card-body { padding: 1rem; }
  .pc-tbl tbody td { padding: .4rem .5rem; }
}
</style>

<div class="pc-page">
  <div class="container-fluid px-3 px-md-4 py-3">
    {{-- ═══ HEADER ═══ --}}
    <div class="pc-hdr">
      <div class="d-flex align-items-center gap-3">
        <h2><i class="bi bi-box-seam"></i>Product List</h2>
        <span class="hdr-badge d-none d-sm-inline">{{ $products->total() }} Products</span>
      </div>
      <div class="d-flex align-items-center gap-2 flex-wrap hdr-actions">
        @if (auth()->user()->email === 'admin@admin.com')
        <button class="pc-btn pc-btn-danger pc-btn-sm" onclick="confirmResetStock()">
          <i class="bi bi-arrow-counterclockwise"></i>Reset Stock
        </button>
        @endif
        @if (auth()->user()->can(' Discount.index') || auth()->user()->email === 'admin@admin.com')
        <a href="{{ route('discount.index') }}" class="pc-btn pc-btn-outline pc-btn-sm">
          <i class="bi bi-percent"></i>Discounts
        </a>
        @endif
        <a href="create_prodcut" class="pc-btn pc-btn-primary pc-btn-sm">
          <i class="bi bi-plus-circle"></i>Add Product
        </a>
        <button id="bulkEditBtn" class="pc-btn pc-btn-primary pc-btn-sm" style="display:none;">
          <i class="bi bi-pencil-square"></i>Bulk Edit
        </button>
        <button id="createDiscountBtn" class="pc-btn pc-btn-primary pc-btn-sm">
          <i class="bi bi-tag"></i>Create Discount
        </button>
        <a id="exportAllBtn" class="pc-btn pc-btn-outline pc-btn-sm" href="javascript:void(0)">
          <i class="bi bi-download"></i>Export All
        </a>
        <button id="exportSelectedBtn" class="pc-btn pc-btn-outline pc-btn-sm">
          <i class="bi bi-download"></i>Export Selected
        </button>
        <a href="{{ url()->previous() }}" class="pc-btn pc-btn-outline pc-btn-sm">
          <i class="bi bi-arrow-left"></i>Back
        </a>
      </div>
    </div>

    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border:none;border-radius:var(--pc-radius-sm);font-size:.86rem;padding:.75rem 1rem;">
      <strong><i class="bi bi-check-circle me-1"></i></strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form id="resetStockForm" action="{{ route('products.reset_stock') }}" method="POST" style="display:none;">@csrf</form>

    {{-- ═══ SEARCH & FILTERS ═══ --}}
    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
      <input type="text" id="productSearch" class="pc-search" placeholder="Search by code, name, barcode, brand..." style="width:100%;max-width:420px;">
      <button type="button" id="hasStockToggleBtn" class="pc-btn pc-btn-outline pc-btn-sm" onclick="toggleProductHasStock()" style="padding:.5rem .9rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
        <i class="bi bi-box-seam"></i> <span>Has Stock</span>
      </button>
    </div>

    {{-- ═══ TABLE CARD ═══ --}}
    <div class="pc-card">
      <div class="pc-card-body">
        <div class="pc-tbl-wrap">
          <table class="pc-tbl" style="width:100%">
            <thead>
              <tr>
                <th style="width:32px;">
                  <input type="checkbox" id="selectAll" title="Select this page">
                  <br><input type="checkbox" id="selectAllPages" title="Select all products across all pages" style="margin-top:4px;">
                  <div style="font-size:9px;color:var(--pc-text-muted);font-weight:400;line-height:1.1;">
                    <span id="selectedCount">0</span>
                  </div>
                </th>
                <th style="width:40px;">#</th>
                <th>Item Code</th>
                <th>Barcode</th>
                <th>Category<br>Sub-Category</th>
                <th>Item Name</th>
                <th>Unit</th>
                <th>Variants / Sizes</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Brand</th>
                <th style="width:140px;">Actions</th>
              </tr>
            </thead>
            <tbody id="productTable">
              @foreach ($products as $key => $product)
              <tr>
                <td><input type="checkbox" class="selectProduct" value="{{ $product->id }}"></td>
                <td style="color:var(--pc-text-muted);font-weight:600;">{{ $products->firstItem() + $key }}</td>
                <td><span class="pc-code">{{ $product->item_code }}</span></td>
                <td>
                  @if($product->barcode_path)
                  <span class="pc-bc-badge"><i class="bi bi-upc-scan"></i>{{ $product->barcode_path }}</span>
                  @else
                  <span style="color:var(--pc-text-muted);font-size:.72rem;">—</span>
                  @endif
                </td>
                <td>
                  <div class="pc-cat">
                    <strong>{{ $product->category_relation->name ?? '-' }}</strong>
                    @if($product->sub_category_relation)
                    <br><small>{{ $product->sub_category_relation->name }}</small>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="pc-name-wrap">
                    <strong>{{ $product->item_name }}</strong>
                  </div>
                </td>
                <td>
                  @php
                    $unitType = $product->unit_type ?? 'piece';
                  @endphp
                  <span class="pc-badge {{ $unitType === 'kg' ? 'pc-badge-kg' : ($unitType === 'pound' ? 'pc-badge-lb' : 'pc-badge-pc') }}">
                    {{ $unitType === 'kg' ? 'KG' : ($unitType === 'pound' ? 'Pound' : 'Piece') }}
                  </span>
                </td>
                <td>
                  @if($product->variants && $product->variants->count() > 0)
                  <div class="pc-variant-list">
                    @foreach($product->variants as $variant)
                    <div>
                      <span><strong>{{ $variant->variant_name }}</strong> - Rs {{ number_format($variant->price) }}                       @if($product->unit_type != 'kg')<span style="color:var(--pc-text-muted)">({{ $variant->stock_qty }})</span>@endif</span>
                      @if($variant->is_default)<span class="v-default">Default</span>@endif
                    </div>
                    @endforeach
                  </div>
                  @else
                  <span style="color:var(--pc-text-muted);font-size:.72rem;">No variants</span>
                  @endif
                </td>
                <td>
                  <div class="pc-price">
                    @if($product->discountProduct)
                    @php
                      $discount = $product->discountProduct;
                      $discountedPrice = $discount->final_price;
                    @endphp
                    <div class="pc-disc-badge">{{ $discount->discount_percentage }}% OFF</div>
                    <del style="color:var(--pc-text-muted);font-size:.75rem;">PKR {{ number_format($product->price) }}</del><br>
                    <span style="color:var(--pc-success)">PKR {{ number_format($discountedPrice) }}</span>
                    @else
                    PKR {{ number_format($product->price) }}
                    @endif
                  </div>
                </td>
                <td>
                  <span class="pc-stock">
                    @php
                      $totalStockValue = $product->total_stock ?? 0;
                      if (($product->unit_type ?? '') === 'kg') {
                        echo number_format($totalStockValue / 1000, 2) . ' KG';
                      } else {
                        echo number_format($totalStockValue);
                      }
                    @endphp
                  </span>
                </td>
                <td>{{ $product->brand->name ?? '-' }}</td>
                <td>
                  <div class="pc-actions">
                    <a href="{{ route('products.edit', $product->id) }}" class="pc-act pc-act-edit">
                      <i class="bi bi-pencil"></i>Edit
                    </a>
                    <a href="{{ route('product.barcode', $product->id) }}" class="pc-act pc-act-bc" target="_blank">
                      <i class="bi bi-upc-scan"></i>Barcode
                    </a>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="pc-pagi" id="paginationLinks">
          {{ $products->appends(request()->input())->links() }}
        </div>
      </div>
    </div>

    {{-- ═══ ADD PRODUCT MODAL ═══ --}}
    <div class="modal fade bd-example-modal-lg" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-plus-circle me-1"></i>Add Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('store-product') }}" method="POST">
            @csrf
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="cat-lbl">Category</label>
                  <select class="cat-fld" name="category_id" id="categorySelect" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="cat-lbl">Sub-Category</label>
                  <select class="cat-fld" name="sub_category_id" id="subCategorySelect">
                    <option value="">Select Sub-Category</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="cat-lbl">Item Name</label>
                  <input type="text" class="cat-fld" name="item_name" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="cat-lbl">Alert Quantity</label>
                  <input type="number" class="cat-fld" name="alert_quantity" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="cat-lbl">Wholesale Price</label>
                  <input type="number" step="0.01" class="cat-fld" name="wholesale_price" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="cat-lbl">Sale Price</label>
                  <input type="number" step="0.01" class="cat-fld" name="retail_price" required>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="cat-btn-p">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
  let searchTimer = null;
  let hasStockOnly = false;

  window.toggleProductHasStock = function() {
    hasStockOnly = !hasStockOnly;
    const btn = $('#hasStockToggleBtn');
    if (hasStockOnly) {
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
    fetchProducts($('#productSearch').val());
  };

  $('#productSearch').on('keyup', function() {
    clearTimeout(searchTimer);
    let query = $(this).val();
    searchTimer = setTimeout(() => { fetchProducts(query); }, 400);
  });

  var allSelectedIds = new Set();
  var selectAllPagesActive = false;

  // Update selected count display
  function updateSelectedCount() {
    var count = allSelectedIds.size;
    $('#selectedCount').text(count);
    if (count > 0) {
      $('#bulkEditBtn').show();
    } else {
      $('#bulkEditBtn').hide();
    }
  }

  // When "select all this page" is clicked, sync visible checkboxes
  $('#selectAll').click(function() {
    $('.selectProduct').prop('checked', this.checked);
    // If select-all-pages is off, update the set from visible checkboxes
    if (!selectAllPagesActive) {
      allSelectedIds.clear();
      $('.selectProduct:checked').each(function() { allSelectedIds.add($(this).val()); });
      updateSelectedCount();
    }
  });

  // When individual checkbox changes
  $(document).on('change', '.selectProduct', function() {
    if (selectAllPagesActive) {
      // Allow unchecking individual items even when "all pages" is on
      if (!this.checked) {
        allSelectedIds.delete($(this).val());
      } else {
        allSelectedIds.add($(this).val());
      }
      updateSelectedCount();
    } else {
      if (this.checked) {
        allSelectedIds.add($(this).val());
      } else {
        allSelectedIds.delete($(this).val());
      }
      updateSelectedCount();
    }
  });

  // "Select All Across Pages" checkbox
  $('#selectAllPages').change(function() {
    if (this.checked) {
      selectAllPagesActive = true;
      var search = $('#productSearch').val() || '';
      // Fetch all product IDs matching current search & stock filter
      $.ajax({
        url: "{{ route('products.all-ids') }}",
        data: { search: search, has_stock: hasStockOnly ? 1 : 0 },
        success: function(ids) {
          allSelectedIds = new Set(ids.map(String));
          // Check all visible checkboxes too
          $('.selectProduct').each(function() { $(this).prop('checked', true); });
          $('#selectAll').prop('checked', true);
          updateSelectedCount();
          Swal.fire({ icon: 'success', title: 'Selected!', text: ids.length + ' products selected across all pages', timer: 1500, showConfirmButton: false });
        }
      });
    } else {
      selectAllPagesActive = false;
      allSelectedIds.clear();
      $('.selectProduct').prop('checked', false);
      $('#selectAll').prop('checked', false);
      updateSelectedCount();
    }
  });

  // After AJAX page load / search, re-sync checkboxes with allSelectedIds
  function syncCheckboxesAfterLoad() {
    $('.selectProduct').each(function() {
      if (selectAllPagesActive || allSelectedIds.has($(this).val())) {
        $(this).prop('checked', true);
      }
    });
    var allVisible = $('.selectProduct').length > 0 && $('.selectProduct:checked').length === $('.selectProduct').length;
    $('#selectAll').prop('checked', allVisible);
  }

  function fetchProducts(search = '', url = null) {
    if (!url) url = "{{ route('product') }}";
    $.ajax({
      url: url,
      data: { 
        search: search,
        has_stock: hasStockOnly ? 1 : 0
      },
      success: function(res) {
        $('#productTable').html($(res).find('#productTable').html());
        $('#paginationLinks').html($(res).find('#paginationLinks').html());
        syncCheckboxesAfterLoad();
      }
    });
  }

  // Handle AJAX pagination clicks
  $(document).on('click', '#paginationLinks a', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    if (url) {
      fetchProducts($('#productSearch').val(), url);
    }
  });

  // Bulk Edit button
  $('#bulkEditBtn').click(function() {
    var ids = Array.from(allSelectedIds);
    if (ids.length === 0) {
      Swal.fire({ icon: 'error', title: 'Oops...', text: 'Please select at least one product!' });
      return;
    }
    var form = $('<form>').attr({ method: 'POST', action: "{{ route('products.bulk-edit-store') }}" });
    form.append('@csrf');
    ids.forEach(function(id) { form.append($('<input>').attr({ type: 'hidden', name: 'ids[]', value: id })); });
    form.appendTo('body').submit();
  });

  $('#createDiscountBtn').click(function() {
    var selected = Array.from(allSelectedIds);
    if (selected.length === 0) {
      Swal.fire({ icon: "error", title: "Oops...", text: "Please select at least one product!" });
      return;
    }
    window.location.href = "{{ route('discount.create') }}" + "?products=" + selected.join(',');
  });

  $('#categorySelect').change(function() {
    var categoryId = $(this).val();
    $('#subCategorySelect').html('<option value="">Loading...</option>');
    if (categoryId) {
      $.ajax({
        url: "/get-subcategories/" + categoryId,
        type: "GET",
        success: function(data) {
          $('#subCategorySelect').html('<option value="">Select Sub-Category</option>');
          $.each(data, function(key, subCategory) {
            $('#subCategorySelect').append('<option value="' + subCategory.id + '">' + subCategory.name + '</option>');
          });
        },
        error: function() { alert('Error fetching subcategories.'); }
      });
    } else {
      $('#subCategorySelect').html('<option value="">Select Sub-Category</option>');
    }
  });
});

// ═══════ EXPORT ═══════
(function() {
  function extractNumber(str) {
    if (str === null || str === undefined) return '';
    str = String(str).trim().replace(/PKR/ig, '').replace(/[^\d\.\-\,'']/g, '').replace(/,/g, '');
    var n = Number(str);
    return isNaN(n) ? '' : n;
  }

  function parseRow($tr) {
    var $tds = $tr.find('td');
    var itemCode = $tds.eq(2).text().trim();
    var barcode = $tds.eq(3).text().trim();
    var cat = $tds.eq(4).find('strong').text().trim() || '';
    var sub = $tds.eq(4).find('small').text().trim() || '';
    var itemName = $tds.eq(5).text().trim();
    var unit = $tds.eq(6).text().trim();
    var price = extractNumber($tds.eq(8).text().trim());
    var stock = extractNumber($tds.eq(9).text().trim());
    var brand = $tds.eq(10).text().trim();
    return [itemCode, barcode, cat, sub, itemName, unit, price, stock, brand];
  }

  function buildWorkbook(dataArray, sheetName) {
    var ws = XLSX.utils.aoa_to_sheet(dataArray);
    ws['!cols'] = [{wpx:90},{wpx:80},{wpx:110},{wpx:110},{wpx:160},{wpx:60},{wpx:70},{wpx:60},{wpx:110}];
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, sheetName || 'Products');
    return wb;
  }

  var HEADERS = ['Item Code','Barcode','Category','Sub-Category','Item Name','Unit','Price','Stock','Brand'];

  document.getElementById('exportAllBtn')?.addEventListener('click', function() {
    var rows = Array.from(document.querySelectorAll('#productTable tr'));
    if (!rows.length) { alert('No products found to export.'); return; }
    var out = [HEADERS];
    rows.forEach(function(tr) {
      if (tr.style.display === 'none') return;
      var $ = window.jQuery;
      if (!$) return;
      out.push(parseRow($(tr)));
    });
    var wb = buildWorkbook(out, 'Products_All');
    var ts = new Date().toISOString().replace(/[:\-T]/g, '').slice(0, 14);
    XLSX.writeFile(wb, 'products_all_' + ts + '.xlsx');
  });

  document.getElementById('exportSelectedBtn')?.addEventListener('click', function() {
    var selectedBoxes = Array.from(document.querySelectorAll('.selectProduct:checked'));
    if (selectedBoxes.length === 0) {
      Swal.fire ? Swal.fire({ icon:'info', title:'No selection', text:'Please select at least one product.' }) : alert('Please select at least one product.');
      return;
    }
    var out = [HEADERS];
    var $ = window.jQuery;
    selectedBoxes.forEach(function(cb) {
      var tr = cb.closest('tr');
      if (!tr) return;
      out.push(parseRow($(tr)));
    });
    var wb = buildWorkbook(out, 'Products_Selected');
    var ts = new Date().toISOString().replace(/[:\-T]/g, '').slice(0, 14);
    XLSX.writeFile(wb, 'products_selected_' + ts + '.xlsx');
  });
})();

function confirmResetStock() {
  Swal.fire({
    title: 'Are you sure?',
    text: "All stocks (including initial stocks and variants) will be set to zero! Sales, purchases, and ledger history will NOT be deleted.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, reset all stock!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('resetStockForm').submit();
    }
  });
}
</script>
<style>
  #paginationLinks nav span, #paginationLinks nav a {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; padding: 0 .55rem; margin: 0 2px;
    border: 1.5px solid var(--pc-border); border-radius: 6px;
    font-size: .8rem; font-weight: 600; color: var(--pc-text-sec);
    text-decoration: none; transition: all .2s ease;
  }
  #paginationLinks nav a:hover { border-color: var(--pc-accent); color: var(--pc-accent); }
</style>
@endsection
