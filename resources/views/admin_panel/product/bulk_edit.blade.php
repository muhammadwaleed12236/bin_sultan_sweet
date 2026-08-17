@extends('admin_panel.layout.app')
@section('content')

<style>
:root {
  --be-bg: #f1f4f9;
  --be-surface: #ffffff;
  --be-border: #e9edf2;
  --be-text: #0b1a33;
  --be-text-sec: #54657e;
  --be-text-muted: #8896ab;
  --be-accent: #2b7fff;
  --be-radius: 14px;
  --be-radius-sm: 9px;
  --be-shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
  --be-shadow-lg: 0 8px 30px rgba(0,0,0,.07), 0 3px 12px rgba(0,0,0,.04);
}
.be-page * { font-family: 'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif; }
.be-page { background: var(--be-bg); min-height: 100vh; padding-bottom: 4rem; }
.be-hdr {
  background: linear-gradient(135deg, #0b1a33 0%, #162d50 50%, #1a4d8c 100%);
  border-radius: var(--be-radius); padding: 1.1rem 1.5rem; margin-bottom: 1rem;
  box-shadow: 0 20px 60px rgba(0,0,0,.10), 0 8px 24px rgba(0,0,0,.04);
  display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
}
.be-hdr h2 { font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0; display: flex; align-items: center; gap: .6rem; }
.be-hdr h2 i { color: #60a5fa; font-size: 1.25rem; }
.be-btn {
  display: inline-flex; align-items: center; gap: .4rem; border-radius: var(--be-radius-sm);
  font-weight: 600; font-size: .78rem; transition: all .25s ease; cursor: pointer;
  text-decoration: none; border: none; padding: .45rem 1.1rem;
}
.be-btn-primary { background: linear-gradient(135deg, var(--be-accent) 0%, #1a6ae8 100%); color: #fff; }
.be-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(43,127,255,.25); color: #fff; }
.be-btn-outline { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); color: #fff; }
.be-btn-outline:hover { background: rgba(255,255,255,.14); color: #fff; }

/* ─── Excel-like Table ─── */
.be-excel-wrap { overflow-x: auto; border: 1px solid var(--be-border); border-radius: var(--be-radius-sm); background: #fff; }
.be-excel { width: 100%; border-collapse: collapse; font-size: .8rem; min-width: 1000px; }
.be-excel thead th {
  background: #1e2a45; color: #c8d6e5; font-size: .67rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .4px; padding: .5rem .6rem;
  border-right: 1px solid #2a3a5a; text-align: left; white-space: nowrap; position: sticky; top: 0; z-index: 2;
}
.be-excel thead th:last-child { border-right: none; }
.be-excel tbody td { padding: .35rem .5rem; border-right: 1px solid var(--be-border); border-bottom: 1px solid var(--be-border); vertical-align: middle; }
.be-excel tbody td:last-child { border-right: none; }
.be-excel tbody tr { transition: background .1s; }
.be-excel tbody tr:hover { background: #f0f4ff; }
.be-excel tbody tr.be-variant-row {
  background: #f8f9fc;
}
.be-excel tbody tr.be-variant-row td { padding: .25rem .5rem; border-bottom: 1px dashed #e0e5f0; }
.be-excel tbody tr.be-variant-row:hover { background: #eef2fa; }
.be-excel .v-indent { padding-left: 1.5rem !important; font-size: .75rem; color: var(--be-text-sec); }

.be-fld {
  border: 1.5px solid var(--be-border); border-radius: 5px; padding: .3rem .5rem;
  font-size: .78rem; font-weight: 500; color: var(--be-text); background: #fff;
  outline: none; width: 100%; min-width: 60px; transition: border-color .2s;
  box-sizing: border-box;
}
.be-fld:focus { border-color: var(--be-accent); box-shadow: 0 0 0 2px rgba(43,127,255,.12); }
.be-fld-sm { padding: .2rem .4rem; font-size: .74rem; min-width: 50px; }
.be-sel { padding: .2rem .4rem; font-size: .74rem; min-width: 55px; }

/* ─── Floating Update Button ─── */
.be-floating-btn {
  position: fixed; bottom: 30px; right: 30px; z-index: 999;
  padding: .8rem 1.8rem; font-size: .9rem; font-weight: 700;
  border-radius: 50px; border: none; cursor: pointer;
  background: linear-gradient(135deg, #0fae6b 0%, #059669 100%);
  color: #fff; box-shadow: 0 8px 30px rgba(15,174,107,.35);
  transition: opacity .4s ease, transform .4s ease, box-shadow .3s;
  opacity: 0; transform: translateY(20px); pointer-events: none;
}
.be-floating-btn.visible { opacity: 1; transform: translateY(0); pointer-events: auto; }
.be-floating-btn:hover { transform: translateY(-2px) scale(1.03); box-shadow: 0 12px 40px rgba(15,174,107,.4); color: #fff; }
.be-floating-btn i { margin-right: .4rem; }

/* ─── Search ─── */
.be-search {
  border: 1.5px solid var(--be-border); border-radius: var(--be-radius-sm);
  padding: .5rem .85rem; font-size: .85rem; font-weight: 500;
  color: var(--be-text); background: var(--be-surface); outline: none;
  width: 100%; max-width: 380px; transition: all .25s ease;
}
.be-search:focus { border-color: var(--be-accent); box-shadow: 0 0 0 3px rgba(43,127,255,.1); }

@media (max-width:768px) {
  .be-hdr { padding: 1rem 1.25rem; flex-direction: column; gap: .5rem; }
  .be-floating-btn { bottom: 15px; right: 15px; padding: .6rem 1.2rem; font-size: .8rem; }
}
</style>

<div class="be-page">
  <div class="container-fluid px-3 px-md-4 py-3">
    <form action="{{ route('products.bulk-update') }}" method="POST" id="bulkEditForm">
      @csrf

      <div class="be-hdr">
        <h2><i class="bi bi-pencil-square"></i>Bulk Edit <span style="font-size:.85rem;font-weight:500;color:#94a3b8;">({{ $products->count() }})</span></h2>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <button type="submit" class="be-btn be-btn-primary"><i class="bi bi-check-lg"></i>Update All</button>
          <a href="{{ route('product') }}" class="be-btn be-btn-outline"><i class="bi bi-arrow-left"></i>Back</a>
        </div>
      </div>

      <div class="mb-3 d-flex align-items-center gap-3 flex-wrap">
        <input type="text" id="bulkSearch" class="be-search" placeholder="Search name or code...">
        <span id="visibleCount" class="text-muted" style="font-size:.82rem;"></span>
      </div>

      @if(session()->has('success'))
      <div class="alert alert-success alert-dismissible fade show mb-3" style="border:none;border-radius:var(--be-radius-sm);font-size:.86rem;padding:.75rem 1rem;">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif
      @if(session()->has('error'))
      <div class="alert alert-danger alert-dismissible fade show mb-3" style="border:none;border-radius:var(--be-radius-sm);font-size:.86rem;padding:.75rem 1rem;">
        <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      <div class="be-excel-wrap">
        <table class="be-excel">
          <thead>
            <tr>
              <th style="width:50px;">#</th>
              <th style="width:140px;">Name</th>
              <th style="width:100px;">Category</th>
              <th style="width:100px;">Sub-Category</th>
              <th style="width:65px;">Unit</th>
              <th style="width:70px;">Price</th>
              <th style="width:60px;">Alert</th>
              <th style="width:90px;">Brand</th>
              <th style="width:90px;">Variant</th>
              <th style="width:55px;">Size</th>
              <th style="width:50px;">SzU</th>
              <th style="width:70px;">Sale Price</th>
              <th style="width:70px;">Cost Price</th>
              <th style="width:60px;">Stock</th>
              <th style="width:50px;">Def</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $product)
            @php
                $pid = $product->id;
                $kgStockKg = 0;
                if ($product->unit_type == 'kg') {
                    $productLevelStock = $product->stocks->firstWhere('variant_id', null);
                    $kgStockKg = $productLevelStock ? $productLevelStock->qty / 1000 : 0;
                }
            @endphp
            <tr class="be-product-row" data-pid="{{ $pid }}">
              <td style="font-weight:600;color:var(--be-text-muted);">{{ $loop->iteration }}</td>
              <td><input type="text" class="be-fld" name="product_name[{{ $pid }}]" value="{{ $product->item_name }}"></td>
              <td>
                <select class="be-fld be-cat" name="category_id[{{ $pid }}]" data-pid="{{ $pid }}">
                  <option value="">-</option>
                  @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
              </td>
              <td>
                <select class="be-fld be-subcat" name="sub_category_id[{{ $pid }}]" data-pid="{{ $pid }}">
                  <option value="">-</option>
                  @foreach($subcategories as $sub)
                  <option value="{{ $sub->id }}" data-cat="{{ $sub->category_id }}" {{ $product->sub_category_id == $sub->id ? 'selected' : '' }} {{ $product->category_id != $sub->category_id ? 'hidden' : '' }}>{{ $sub->name }}</option>
                  @endforeach
                </select>
              </td>
              <td>
                <select class="be-fld" name="unit_type[{{ $pid }}]">
                  <option value="piece" {{ ($product->unit_type ?? 'piece') == 'piece' ? 'selected' : '' }}>Pc</option>
                  <option value="kg" {{ $product->unit_type == 'kg' ? 'selected' : '' }}>KG</option>
                  <option value="pound" {{ $product->unit_type == 'pound' ? 'selected' : '' }}>Lb</option>
                </select>
              </td>
              <td><input type="number" class="be-fld" name="price[{{ $pid }}]" step="0.01" value="{{ $product->price }}"></td>
              <td><input type="number" class="be-fld" name="alert_quantity[{{ $pid }}]" value="{{ $product->alert_quantity }}"></td>
              <td>
                <select class="be-fld" name="brand_id[{{ $pid }}]">
                  <option value="">-</option>
                  @foreach($brands as $brand)
                  <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                  @endforeach
                </select>
              </td>
              @php $firstVar = $product->variants->first(); @endphp
              @if($firstVar)
              <td><input type="text" class="be-fld" name="variant_name[{{ $pid }}][]" value="{{ $firstVar->variant_name }}" placeholder="-"><input type="hidden" name="variant_id[{{ $pid }}][]" value="{{ $firstVar->id }}"></td>
              <td><input type="number" class="be-fld" step="0.01" name="variant_size_value[{{ $pid }}][]" value="{{ $firstVar->grams }}"></td>
              <td>
                <select class="be-fld" name="variant_size_unit[{{ $pid }}][]">
                  <option value="piece" {{ $firstVar->size_unit == 'piece' ? 'selected' : '' }}>Pc</option>
                  <option value="kg" {{ $firstVar->size_unit == 'kg' ? 'selected' : '' }}>KG</option>
                  <option value="pound" {{ $firstVar->size_unit == 'pound' ? 'selected' : '' }}>Lb</option>
                </select>
              </td>
              <td><input type="number" class="be-fld" step="0.01" name="variant_price[{{ $pid }}][]" value="{{ $firstVar->price }}"></td>
              <td><input type="number" class="be-fld" step="0.01" name="variant_cost_price[{{ $pid }}][]" value="{{ $firstVar->cost_price }}"></td>
              @if($product->unit_type == 'kg')
              <td><input type="number" class="be-fld" step="0.01" name="kg_stock[{{ $pid }}]" value="{{ $kgStockKg }}"><input type="hidden" name="variant_stock[{{ $pid }}][]" value="0"></td>
              @else
              <td><input type="number" class="be-fld" step="0.01" name="variant_stock[{{ $pid }}][]" value="{{ $firstVar->stocks->where('branch_id', 1)->where('warehouse_id', 1)->first()?->qty ?? $firstVar->stock_qty }}"></td>
              @endif
              <td style="text-align:center;"><input type="radio" name="variant_default[{{ $pid }}]" value="0" {{ $firstVar->is_default ? 'checked' : '' }}></td>
              @else
              <td><input type="text" class="be-fld" name="variant_name[{{ $pid }}][]" placeholder="-"><input type="hidden" name="variant_id[{{ $pid }}][]" value=""></td>
              <td><input type="hidden" name="variant_size_value[{{ $pid }}][]" value="0"></td>
              <td><input type="hidden" name="variant_size_unit[{{ $pid }}][]" value="piece"></td>
              <td><input type="hidden" name="variant_price[{{ $pid }}][]" value="0"></td>
              <td><input type="hidden" name="variant_cost_price[{{ $pid }}][]" value="0"></td>
              <td><input type="hidden" name="variant_stock[{{ $pid }}][]" value="0"></td>
              <td></td>
              @endif
            </tr>
            @foreach($product->variants->skip(1) as $v)
            <tr class="be-variant-row" data-pid="{{ $pid }}">
              <td></td>
              <td colspan="7" class="v-indent"><i class="bi bi-arrow-return-right text-muted"></i> {{ $product->item_name }}</td>
              <td><input type="text" class="be-fld be-fld-sm" name="variant_name[{{ $pid }}][]" value="{{ $v->variant_name }}"><input type="hidden" name="variant_id[{{ $pid }}][]" value="{{ $v->id }}"></td>
              <td><input type="number" class="be-fld be-fld-sm" step="0.01" name="variant_size_value[{{ $pid }}][]" value="{{ $v->grams }}"></td>
              <td>
                <select class="be-fld be-sel" name="variant_size_unit[{{ $pid }}][]">
                  <option value="piece" {{ $v->size_unit == 'piece' ? 'selected' : '' }}>Pc</option>
                  <option value="kg" {{ $v->size_unit == 'kg' ? 'selected' : '' }}>KG</option>
                  <option value="pound" {{ $v->size_unit == 'pound' ? 'selected' : '' }}>Lb</option>
                </select>
              </td>
              <td><input type="number" class="be-fld be-fld-sm" step="0.01" name="variant_price[{{ $pid }}][]" value="{{ $v->price }}"></td>
              <td><input type="number" class="be-fld be-fld-sm" step="0.01" name="variant_cost_price[{{ $pid }}][]" value="{{ $v->cost_price }}"></td>
              @if($product->unit_type == 'kg')
              <td><input type="hidden" name="variant_stock[{{ $pid }}][]" value="0">—</td>
              @else
              <td><input type="number" class="be-fld be-fld-sm" step="0.01" name="variant_stock[{{ $pid }}][]" value="{{ $v->stocks->where('branch_id', 1)->where('warehouse_id', 1)->first()?->qty ?? $v->stock_qty }}"></td>
              @endif
              <td style="text-align:center;"><input type="radio" name="variant_default[{{ $pid }}]" value="{{ $loop->index + 1 }}" {{ $v->is_default ? 'checked' : '' }}></td>
            </tr>
            @endforeach
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Bottom submit --}}
      <div class="text-center mt-3">
        <button type="submit" class="be-btn be-btn-primary" style="padding:.6rem 2rem;font-size:.9rem;"><i class="bi bi-check-lg"></i> Update All Products</button>
      </div>
    </form>

    {{-- Floating update button (inside form via JS click) --}}
    <button type="button" class="be-floating-btn" id="floatingUpdateBtn">
      <i class="bi bi-check-lg"></i> Update All
    </button>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
  // Subcategory filter on category change
  $(document).on('change', '.be-cat', function() {
    var catId = $(this).val();
    var pid = $(this).data('pid');
    var $subcat = $('.be-subcat[data-pid="' + pid + '"]');
    $subcat.find('option').each(function() {
      var $opt = $(this);
      if ($opt.val() === '') return;
      $opt.prop('hidden', $opt.data('cat') != catId);
      if ($opt.prop('selected') && $opt.prop('hidden')) $opt.prop('selected', false);
    });
    if ($subcat.find('option:selected').prop('hidden')) $subcat.val('');
  });

  // Live search
  function updateVisibleCount() {
    var total = $('.be-product-row').length;
    var visible = $('.be-product-row:visible').length;
    $('#visibleCount').text(visible + ' of ' + total + ' shown');
  }

  $('#bulkSearch').on('keyup', function() {
    var q = $(this).val().toLowerCase().trim();
    $('.be-product-row').each(function() {
      var $row = $(this);
      var pid = $row.data('pid');
      var text = ($row.find('td:eq(1) input').val() || '') + ' ' + ($row.find('td:eq(0)').text() || '');
      var match = !q || text.toLowerCase().indexOf(q) > -1;
      $row.toggle(match);
      $('.be-variant-row[data-pid="' + pid + '"]').toggle(match);
    });
    updateVisibleCount();
  });
  updateVisibleCount();

  // ─── Floating button: show on scroll, hide after 2s idle ───
  var $floatBtn = $('#floatingUpdateBtn');
  var scrollTimer;

  $floatBtn.on('click', function() { $('#bulkEditForm').submit(); });

  $(window).on('scroll', function() {
    $floatBtn.addClass('visible');
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(function() {
      $floatBtn.removeClass('visible');
    }, 2000);
  });

  // Prevent Enter from submitting the form
  $('#bulkEditForm').on('keydown', function(e) {
    if (e.key === 'Enter') e.preventDefault();
  });
});
</script>
@endsection