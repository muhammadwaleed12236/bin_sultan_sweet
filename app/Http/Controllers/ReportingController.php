<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomerPayment;
use App\Models\ExpenseVoucher;
use App\Models\Product;
use App\Models\Sale;
use App\Models\VendorPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportingController extends Controller
{
    public function item_stock_report()
    {
        // Pass ALL products for the dropdown — no pagination needed
        $products = \App\Models\Product::restricted()
            ->orderBy('item_name', 'asc')
            ->select('id', 'item_code', 'item_name')
            ->get();
        return view('admin_panel.reporting.item_stock_report', compact('products'));
    }

    public function fetchItemStock(Request $request)
    {
        $productIdsArr = $request->input('product_id', 'all');
        $searchQ    = trim($request->get('q', ''));
        $startDate  = $request->start_date ?? date('Y-m-01');
        $endDate    = $request->end_date   ?? date('Y-m-t');
        $startTime  = $request->start_time ?? '00:00:00';
        $endTime    = $request->end_time   ?? '23:59:59';

        $startDT = $startDate . ' ' . $startTime;
        $endDT   = $endDate   . ' ' . $endTime;
        $nowDT   = now()->format('Y-m-d H:i:s');
        $isHistorical = ($endDT < $nowDT);

        // Check if there is a stock reset timestamp
        $resetTime = null;
        if (\Illuminate\Support\Facades\Storage::exists('stock_reset_timestamp.txt')) {
            $resetTime = trim(\Illuminate\Support\Facades\Storage::get('stock_reset_timestamp.txt'));
        }

        // 1. Get products and their variants
        $pQuery = Product::restricted()->with(['variants', 'unit'])->orderBy('item_name');

        $productIdsFilter = [];
        if (is_array($productIdsArr)) {
            if (!in_array('all', $productIdsArr)) {
                $productIdsFilter = $productIdsArr;
            }
        } elseif ($productIdsArr !== 'all') {
            $productIdsFilter = [$productIdsArr];
        }
        if (!empty($productIdsFilter)) {
            $pQuery->whereIn('id', $productIdsFilter);
        } elseif ($searchQ !== '') {
            $pQuery->where(function($q) use ($searchQ) {
                $q->where('item_name','like',"%{$searchQ}%")
                  ->orWhere('item_code','like',"%{$searchQ}%");
            });
        }

        $products = $pQuery->get();
        $productIds = $products->pluck('id')->toArray();

        // 2. Fetch live stock from stocks table (branch 1 / warehouse 1)
        $stocksData = DB::table('stocks')
            ->whereIn('product_id', $productIds)
            ->where('branch_id', 1)
            ->where('warehouse_id', 1)
            ->select('product_id', 'variant_id', 'qty')
            ->get();

        $liveStockMap = [];
        foreach ($stocksData as $st) {
            $k = $st->product_id . '_' . ($st->variant_id ?? 0);
            $liveStockMap[$k] = ($liveStockMap[$k] ?? 0) + (float)$st->qty;
        }

        $codeToIdMap = [];
        $nameToIdMap = [];
        foreach (\App\Models\Product::all() as $prod) {
            $codeToIdMap[trim($prod->item_code)] = $prod->id;
            $nameToIdMap[trim($prod->item_name)] = $prod->id;
        }

        // 3. IN-PERIOD TRANSACTIONS: transactions within [startDate, endDate]
        $purchasesQuery = DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->whereIn('purchase_items.product_id', $productIds)
            ->whereBetween('purchases.purchase_date', [$startDate, $endDate]);
        if ($resetTime) {
            $purchasesQuery->where('purchases.created_at', '>=', $resetTime);
        }
        $purchases = $purchasesQuery->select('purchase_items.product_id', 'purchase_items.variant_id', DB::raw('SUM(purchase_items.qty) as total_qty'))
            ->groupBy('purchase_items.product_id', 'purchase_items.variant_id')
            ->get();

        $productionsQuery = DB::table('production_entry_items')
            ->join('production_entries', 'production_entries.id', '=', 'production_entry_items.production_entry_id')
            ->whereIn('production_entry_items.product_id', $productIds)
            ->whereDate('production_entries.production_date', '>=', $startDate)
            ->whereDate('production_entries.production_date', '<=', $endDate);
        if ($resetTime) {
            $productionsQuery->where('production_entries.created_at', '>=', $resetTime);
        }
        $productions = $productionsQuery->select('production_entry_items.product_id', 'production_entry_items.variant_id', DB::raw('SUM(production_entry_items.qty_stock) as total_qty'))
            ->groupBy('production_entry_items.product_id', 'production_entry_items.variant_id')
            ->get();

        $hasVariantIdInPRItems = \Illuminate\Support\Facades\Schema::hasColumn('purchase_return_items', 'variant_id');

        $purchaseReturnsQuery = DB::table('purchase_return_items')
            ->join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
            ->whereIn('purchase_return_items.product_id', $productIds)
            ->whereBetween('purchase_returns.return_date', [$startDate, $endDate]);
        if ($resetTime) {
            $purchaseReturnsQuery->where('purchase_returns.created_at', '>=', $resetTime);
        }
        if ($hasVariantIdInPRItems) {
            $purchaseReturnsQuery->select('purchase_return_items.product_id', 'purchase_return_items.variant_id', DB::raw('SUM(purchase_return_items.qty) as total_qty'));
        } else {
            $purchaseReturnsQuery->select('purchase_return_items.product_id', DB::raw('0 as variant_id'), DB::raw('SUM(purchase_return_items.qty) as total_qty'));
        }
        $purchaseReturns = $purchaseReturnsQuery->groupBy('purchase_return_items.product_id', $hasVariantIdInPRItems ? 'purchase_return_items.variant_id' : DB::raw('0'))
            ->get();

        $mapP    = []; foreach($purchases    as $p)  { $k = $p->product_id  . '_' . ($p->variant_id  ?? 0); $mapP[$k]    = ($mapP[$k]    ?? 0) + $p->total_qty; }
        $mapProd = []; foreach($productions  as $pd) { $k = $pd->product_id . '_' . ($pd->variant_id ?? 0); $mapProd[$k] = ($mapProd[$k] ?? 0) + $pd->total_qty; }
        $mapPR   = []; foreach($purchaseReturns as $pr) { $k = $pr->product_id . '_' . ($pr->variant_id ?? 0); $mapPR[$k] = ($mapPR[$k] ?? 0) + $pr->total_qty; }

        // Sales processing (by product and variant) with DATE filter
        $hasVariantIdInSales = \Illuminate\Support\Facades\Schema::hasColumn('sales', 'variant_id');
        $allSalesQuery = DB::table('sales')->whereBetween('created_at', [$startDT, $endDT])->whereNotNull('product')->select('product', 'qty');
        if ($hasVariantIdInSales) {
            $allSalesQuery->addSelect('variant_id');
        }
        if ($resetTime) {
            $allSalesQuery->where('created_at', '>=', $resetTime);
        }
        $allSales = $allSalesQuery->get();

        $soldMap = [];
        foreach ($allSales as $s) {
            $pids = explode(',', $s->product);
            $qtys = explode(',', $s->qty);
            $vids = $hasVariantIdInSales ? explode(',', $s->variant_id ?? '') : [];
            
            foreach ($pids as $idx => $pid) {
                $pid = trim($pid);
                if ($pid === '') continue;
                $vid = trim($vids[$idx] ?? '0');
                if ($vid === '') $vid = '0';
                $key = $pid . '_' . $vid;
                $soldMap[$key] = ($soldMap[$key] ?? 0) + floatval($qtys[$idx] ?? 0);
            }
        }

        $hasVariantIdInReturns = \Illuminate\Support\Facades\Schema::hasColumn('sales_returns', 'variant_id');
        $hasCodeInReturns = \Illuminate\Support\Facades\Schema::hasColumn('sales_returns', 'product_code');
        $allReturnsQuery = DB::table('sales_returns')->whereBetween('created_at', [$startDT, $endDT])->whereNotNull('product')->select('product', 'qty');
        if ($hasCodeInReturns) {
            $allReturnsQuery->addSelect('product_code');
        }
        if ($hasVariantIdInReturns) {
            $allReturnsQuery->addSelect('variant_id');
        }
        if ($resetTime) {
            $allReturnsQuery->where('created_at', '>=', $resetTime);
        }
        $allReturns = $allReturnsQuery->get();

        $retMap = [];
        foreach ($allReturns as $r) {
            $pids = explode(',', $r->product);
            $codes = isset($r->product_code) ? explode(',', $r->product_code ?? '') : [];
            $qtys = explode(',', $r->qty);
            $vids = $hasVariantIdInReturns ? explode(',', $r->variant_id ?? '') : [];
            
            foreach ($pids as $idx => $pid) {
                $pid = trim($pid);
                if ($pid === '') continue;
                $code = trim($codes[$idx] ?? '');
                $qty = floatval($qtys[$idx] ?? 0);
                if ($qty <= 0) continue;

                // Resolve real Product ID
                $resolvedPid = null;
                if (is_numeric($pid) && (isset($codeToIdMap[$pid]) || \App\Models\Product::where('id', intval($pid))->exists())) {
                    $resolvedPid = intval($pid);
                } elseif (!empty($code) && isset($codeToIdMap[$code])) {
                    $resolvedPid = $codeToIdMap[$code];
                } elseif (!empty($pid) && isset($nameToIdMap[$pid])) {
                    $resolvedPid = $nameToIdMap[$pid];
                } elseif (is_numeric($pid)) {
                    $resolvedPid = intval($pid);
                }

                if (!$resolvedPid) continue;

                $vid = trim($vids[$idx] ?? '0');
                if ($vid === '') $vid = '0';
                $key = $resolvedPid . '_' . $vid;
                $retMap[$key] = ($retMap[$key] ?? 0) + $qty;
            }
        }

        // ---- STOCK ADJUSTMENTS within date range ----
        $adjInRangeQuery = DB::table('stock_adjustment_items as sai')
            ->join('stock_adjustments as sa', 'sa.id', '=', 'sai.adjustment_id')
            ->whereIn('sai.product_id', $productIds)
            ->whereBetween('sa.adjustment_date', [$startDate, $endDate]);
        if ($resetTime) {
            $adjInRangeQuery->where('sa.created_at', '>=', $resetTime);
        }
        $adjInRange = $adjInRangeQuery->select('sai.product_id', 'sa.type', DB::raw('SUM(sai.qty_stock) as total_qty'))
            ->groupBy('sai.product_id', 'sa.type')
            ->get();

        $mapAdjInc = []; // increase within range
        $mapAdjDec = []; // decrease within range
        foreach ($adjInRange as $adj) {
            $k = $adj->product_id . '_0';
            if ($adj->type === 'increase') {
                $mapAdjInc[$k] = ($mapAdjInc[$k] ?? 0) + $adj->total_qty;
            } else {
                $mapAdjDec[$k] = ($mapAdjDec[$k] ?? 0) + $adj->total_qty;
            }
        }

        // 4. AFTER-PERIOD TRANSACTIONS (only needed if historical range viewed)
        $mapPAfter = [];
        $mapProdAfter = [];
        $mapPRAfter = [];
        $soldAfterMap = [];
        $retAfterMap = [];
        $mapAdjIncAfter = [];
        $mapAdjDecAfter = [];

        if ($isHistorical) {
            $afterPurchasesQuery = DB::table('purchase_items')
                ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
                ->whereIn('purchase_items.product_id', $productIds)
                ->where('purchases.purchase_date', '>', $endDate);
            if ($resetTime) { $afterPurchasesQuery->where('purchases.created_at', '>=', $resetTime); }
            $afterPurchases = $afterPurchasesQuery->select('purchase_items.product_id', 'purchase_items.variant_id', DB::raw('SUM(purchase_items.qty) as total_qty'))
                ->groupBy('purchase_items.product_id', 'purchase_items.variant_id')->get();
            foreach($afterPurchases as $p) { $k = $p->product_id . '_' . ($p->variant_id ?? 0); $mapPAfter[$k] = ($mapPAfter[$k] ?? 0) + $p->total_qty; }

            $afterProductionsQuery = DB::table('production_entry_items')
                ->join('production_entries', 'production_entries.id', '=', 'production_entry_items.production_entry_id')
                ->whereIn('production_entry_items.product_id', $productIds)
                ->whereDate('production_entries.production_date', '>', $endDate);
            if ($resetTime) { $afterProductionsQuery->where('production_entries.created_at', '>=', $resetTime); }
            $afterProductions = $afterProductionsQuery->select('production_entry_items.product_id', 'production_entry_items.variant_id', DB::raw('SUM(production_entry_items.qty_stock) as total_qty'))
                ->groupBy('production_entry_items.product_id', 'production_entry_items.variant_id')->get();
            foreach($afterProductions as $pd) { $k = $pd->product_id . '_' . ($pd->variant_id ?? 0); $mapProdAfter[$k] = ($mapProdAfter[$k] ?? 0) + $pd->total_qty; }

            $afterPRQuery = DB::table('purchase_return_items')
                ->join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                ->whereIn('purchase_return_items.product_id', $productIds)
                ->where('purchase_returns.return_date', '>', $endDate);
            if ($resetTime) { $afterPRQuery->where('purchase_returns.created_at', '>=', $resetTime); }
            $afterPR = $afterPRQuery->select('purchase_return_items.product_id', $hasVariantIdInPRItems ? 'purchase_return_items.variant_id' : DB::raw('0 as variant_id'), DB::raw('SUM(purchase_return_items.qty) as total_qty'))
                ->groupBy('purchase_return_items.product_id', $hasVariantIdInPRItems ? 'purchase_return_items.variant_id' : DB::raw('0'))->get();
            foreach($afterPR as $pr) { $k = $pr->product_id . '_' . ($pr->variant_id ?? 0); $mapPRAfter[$k] = ($mapPRAfter[$k] ?? 0) + $pr->total_qty; }

            $afterSalesQuery = DB::table('sales')->where('created_at', '>', $endDT)->whereNotNull('product')->select('product', 'qty');
            if ($hasVariantIdInSales) { $afterSalesQuery->addSelect('variant_id'); }
            if ($resetTime) { $afterSalesQuery->where('created_at', '>=', $resetTime); }
            $afterSales = $afterSalesQuery->get();
            foreach ($afterSales as $s) {
                $pids = explode(',', $s->product); $qtys = explode(',', $s->qty); $vids = $hasVariantIdInSales ? explode(',', $s->variant_id ?? '') : [];
                foreach ($pids as $idx => $pid) {
                    $pid = trim($pid); if ($pid === '') continue;
                    $vid = trim($vids[$idx] ?? '0'); if ($vid === '') $vid = '0';
                    $key = $pid . '_' . $vid;
                    $soldAfterMap[$key] = ($soldAfterMap[$key] ?? 0) + floatval($qtys[$idx] ?? 0);
                }
            }

            $afterRetQuery = DB::table('sales_returns')->where('created_at', '>', $endDT)->whereNotNull('product')->select('product', 'qty');
            if ($hasCodeInReturns) { $afterRetQuery->addSelect('product_code'); }
            if ($hasVariantIdInReturns) { $afterRetQuery->addSelect('variant_id'); }
            if ($resetTime) { $afterRetQuery->where('created_at', '>=', $resetTime); }
            $afterReturns = $afterRetQuery->get();
            foreach ($afterReturns as $r) {
                $pids = explode(',', $r->product); $codes = isset($r->product_code) ? explode(',', $r->product_code ?? '') : []; $qtys = explode(',', $r->qty); $vids = $hasVariantIdInReturns ? explode(',', $r->variant_id ?? '') : [];
                foreach ($pids as $idx => $pid) {
                    $pid = trim($pid); if ($pid === '') continue;
                    $code = trim($codes[$idx] ?? '');
                    $qty = floatval($qtys[$idx] ?? 0);
                    if ($qty <= 0) continue;
                    $resolvedPid = null;
                    if (is_numeric($pid) && (isset($codeToIdMap[$pid]) || \App\Models\Product::where('id', intval($pid))->exists())) {
                        $resolvedPid = intval($pid);
                    } elseif (!empty($code) && isset($codeToIdMap[$code])) {
                        $resolvedPid = $codeToIdMap[$code];
                    } elseif (!empty($pid) && isset($nameToIdMap[$pid])) {
                        $resolvedPid = $nameToIdMap[$pid];
                    } elseif (is_numeric($pid)) {
                        $resolvedPid = intval($pid);
                    }
                    if (!$resolvedPid) continue;
                    $vid = trim($vids[$idx] ?? '0'); if ($vid === '') $vid = '0';
                    $key = $resolvedPid . '_' . $vid;
                    $retAfterMap[$key] = ($retAfterMap[$key] ?? 0) + $qty;
                }
            }

            $afterAdjQuery = DB::table('stock_adjustment_items as sai')
                ->join('stock_adjustments as sa', 'sa.id', '=', 'sai.adjustment_id')
                ->whereIn('sai.product_id', $productIds)
                ->where('sa.adjustment_date', '>', $endDate);
            if ($resetTime) { $afterAdjQuery->where('sa.created_at', '>=', $resetTime); }
            $afterAdj = $afterAdjQuery->select('sai.product_id', 'sa.type', DB::raw('SUM(sai.qty_stock) as total_qty'))
                ->groupBy('sai.product_id', 'sa.type')->get();
            foreach ($afterAdj as $adj) {
                $k = $adj->product_id . '_0';
                if ($adj->type === 'increase') { $mapAdjIncAfter[$k] = ($mapAdjIncAfter[$k] ?? 0) + $adj->total_qty; }
                else { $mapAdjDecAfter[$k] = ($mapAdjDecAfter[$k] ?? 0) + $adj->total_qty; }
            }
        }

        $rows = [];
        $grandTotalValue = 0;

        foreach ($products as $p) {
            $is_kg = ($p->unit_type === 'kg');
            $displayName = $p->item_name ?: ('Item ' . $p->item_code);

            if (!$is_kg && $p->variants->count() > 0) {
                foreach ($p->variants as $v) {
                    $key = $p->id . '_' . $v->id;
                    $code = $p->item_code; 
                    
                    $purchased = (float)($mapP[$key] ?? 0);
                    $produced  = (float)($mapProd[$key] ?? 0);
                    if ($v->is_default || $p->variants->first()->id == $v->id) {
                        $produced  += (float)($mapProd[$p->id . '_0'] ?? 0);
                        $purchased += (float)($mapP[$p->id . '_0'] ?? 0);
                    }

                    $pReturn   = (float)($mapPR[$key] ?? 0); 
                    if ($v->is_default || $p->variants->first()->id == $v->id) {
                        $pReturn += (float)($mapPR[$p->id . '_0'] ?? 0);
                    }

                    $netSold   = (float)($soldMap[$key] ?? 0);
                    $sReturn   = (float)($retMap[$key] ?? 0);
                    if ($v->is_default || $p->variants->first()->id == $v->id) {
                        $netSold += (float)($soldMap[$p->id . '_0'] ?? 0);
                        $sReturn += (float)($retMap[$p->id . '_0'] ?? 0);
                    }
                    $sold = $netSold + $sReturn;

                    $adjInc    = (float)($mapAdjInc[$key] ?? $mapAdjInc[$p->id . '_0'] ?? 0);
                    $adjDec    = (float)($mapAdjDec[$key] ?? $mapAdjDec[$p->id . '_0'] ?? 0);

                    // Live stock from stocks table
                    $liveStock = (float)($liveStockMap[$key] ?? 0);
                    if ($liveStock == 0 && ($v->is_default || $p->variants->first()->id == $v->id)) {
                        $liveStock += (float)($liveStockMap[$p->id . '_0'] ?? 0);
                    }

                    // After period movements
                    $netMovementsAfter = 0;
                    if ($isHistorical) {
                        $purchAfter  = (float)($mapPAfter[$key] ?? 0);
                        $prodAfter   = (float)($mapProdAfter[$key] ?? 0);
                        $pRetAfter   = (float)($mapPRAfter[$key] ?? 0);
                        $netSoldAft  = (float)($soldAfterMap[$key] ?? 0);
                        $sRetAfter   = (float)($retAfterMap[$key] ?? 0);
                        $adjIncAfter = (float)($mapAdjIncAfter[$p->id . '_0'] ?? 0);
                        $adjDecAfter = (float)($mapAdjDecAfter[$p->id . '_0'] ?? 0);
                        if ($v->is_default || $p->variants->first()->id == $v->id) {
                            $prodAfter  += (float)($mapProdAfter[$p->id . '_0'] ?? 0);
                            $purchAfter += (float)($mapPAfter[$p->id . '_0'] ?? 0);
                            $pRetAfter  += (float)($mapPRAfter[$p->id . '_0'] ?? 0);
                            $netSoldAft += (float)($soldAfterMap[$p->id . '_0'] ?? 0);
                            $sRetAfter  += (float)($retAfterMap[$p->id . '_0'] ?? 0);
                        }
                        $soldAfter = $netSoldAft + $sRetAfter;
                        $netMovementsAfter = $purchAfter + $prodAfter + $sRetAfter - $soldAfter - $pRetAfter + $adjIncAfter - $adjDecAfter;
                    }

                    $closingStock = $liveStock - $netMovementsAfter;
                    $netMovementsInPeriod = $purchased + $produced + $sReturn - $sold - $pReturn + $adjInc - $adjDec;
                    $openingStock = $closingStock - $netMovementsInPeriod;

                    $vLabel = $v->size_label ?: $v->variant_name;
                    $rows[] = [
                        'item_code'       => $code,
                        'item_name'       => $displayName . ($vLabel ? ' (' . $vLabel . ')' : ''),
                        'is_kg'           => false,
                        'initial_stock'   => $openingStock,
                        'produced'        => $produced,
                        'purchased'       => $purchased,
                        'purchase_return' => $pReturn,
                        'adj_increase'    => $adjInc,
                        'adj_decrease'    => $adjDec,
                        'sold'            => $sold,
                        'sale_return'     => $sReturn,
                        'balance'         => $closingStock,
                        'unit'            => $p->unit?->name ?? 'PC',
                    ];
                    $priceVal = (float)($v->wholesale_price ?: $p->wholesale_price ?: $v->price ?: $p->price);
                    $grandTotalValue += $closingStock * $priceVal;
                }
            } else {
                // Base product only or combined KG product
                $key = $p->id . '_0';
                $code = $p->item_code;

                $purchased_kg = (float)($mapP[$key] ?? 0);
                $produced = 0;
                foreach ($mapProd as $k => $val) {
                    if (str_starts_with($k, $p->id . '_')) {
                        $produced += (float)$val;
                    }
                }
                
                $pReturn   = (float)($mapPR[$p->id . '_0'] ?? 0);
                $rawNetSold = (float)($soldMap[$key] ?? 0);
                $rawSReturn = (float)($retMap[$key] ?? 0);

                if ($is_kg) {
                    $purchased = $purchased_kg * 1000;
                    $sReturn   = $rawSReturn * 1000;
                    $sold      = ($rawNetSold * 1000) + $sReturn;
                    $pReturn   = $pReturn * 1000;
                } else {
                    $purchased = $purchased_kg;
                    $sReturn   = $rawSReturn;
                    $sold      = $rawNetSold + $sReturn;
                }

                if ($is_kg && $p->variants->count() > 0) {
                    foreach ($p->variants as $v) {
                        $vKey = $p->id . '_' . $v->id;
                        $mul = floatval($v->size_value); 
                        if ($v->size_unit === 'kg') {
                            $mul *= 1000;
                        }

                        $vPurchRaw = (float)($mapP[$vKey] ?? 0);
                        $purchased += $vPurchRaw * $mul;
                        $pReturn   += (float)($mapPR[$vKey] ?? 0) * $mul;

                        $vSReturn    = (float)($retMap[$vKey] ?? 0) * $mul;
                        $vNetSold    = (float)($soldMap[$vKey] ?? 0) * $mul;
                        $sold       += ($vNetSold + $vSReturn);
                        $sReturn    += $vSReturn;
                    }
                }

                $adjInc = (float)($mapAdjInc[$p->id . '_0'] ?? 0);
                $adjDec = (float)($mapAdjDec[$p->id . '_0'] ?? 0);

                // Live stock from stocks table
                $liveStock = (float)($liveStockMap[$p->id . '_0'] ?? 0);
                if ($is_kg) {
                    foreach ($p->variants as $v) {
                        $liveStock += (float)($liveStockMap[$p->id . '_' . $v->id] ?? 0);
                    }
                }

                // After period movements
                $netMovementsAfter = 0;
                if ($isHistorical) {
                    $purchAft_kg = (float)($mapPAfter[$key] ?? 0);
                    $prodAft = 0;
                    foreach ($mapProdAfter as $k => $val) {
                        if (str_starts_with($k, $p->id . '_')) { $prodAft += (float)$val; }
                    }
                    $pRetAft   = (float)($mapPRAfter[$p->id . '_0'] ?? 0);
                    $rawNetAft = (float)($soldAfterMap[$key] ?? 0);
                    $rawSRetAft = (float)($retAfterMap[$key] ?? 0);

                    if ($is_kg) {
                        $purchAft = $purchAft_kg * 1000;
                        $sRetAft  = $rawSRetAft * 1000;
                        $soldAft  = ($rawNetAft * 1000) + $sRetAft;
                        $pRetAft  = $pRetAft * 1000;
                    } else {
                        $purchAft = $purchAft_kg;
                        $sRetAft  = $rawSRetAft;
                        $soldAft  = $rawNetAft + $sRetAft;
                    }

                    if ($is_kg && $p->variants->count() > 0) {
                        foreach ($p->variants as $v) {
                            $vKey = $p->id . '_' . $v->id;
                            $mul = floatval($v->size_value); 
                            if ($v->size_unit === 'kg') { $mul *= 1000; }
                            $vPurchRaw = (float)($mapPAfter[$vKey] ?? 0);
                            $purchAft += $vPurchRaw * $mul;
                            $pRetAft  += (float)($mapPRAfter[$vKey] ?? 0) * $mul;
                            $vSReturn  = (float)($retAfterMap[$vKey] ?? 0) * $mul;
                            $vNetSold  = (float)($soldAfterMap[$vKey] ?? 0) * $mul;
                            $soldAft  += ($vNetSold + $vSReturn);
                            $sRetAft  += $vSReturn;
                        }
                    }

                    $adjIncAft = (float)($mapAdjIncAfter[$p->id . '_0'] ?? 0);
                    $adjDecAft = (float)($mapAdjDecAfter[$p->id . '_0'] ?? 0);
                    $netMovementsAfter = $purchAft + $prodAft + $sRetAft - $soldAft - $pRetAft + $adjIncAft - $adjDecAft;
                }

                $closingStock = $liveStock - $netMovementsAfter;
                $netMovementsInPeriod = $purchased + $produced + $sReturn - $sold - $pReturn + $adjInc - $adjDec;
                $openingStock = $closingStock - $netMovementsInPeriod;

                $rows[] = [
                    'item_code'       => $code,
                    'item_name'       => $displayName,
                    'is_kg'           => $is_kg,
                    'initial_stock'   => $openingStock,
                    'produced'        => $produced,
                    'purchased'       => $purchased,
                    'purchase_return' => $pReturn,
                    'adj_increase'    => $adjInc,
                    'adj_decrease'    => $adjDec,
                    'sold'            => $sold,
                    'sale_return'     => $sReturn,
                    'balance'         => $closingStock,
                    'unit'            => $p->unit?->name ?? ($is_kg ? 'KG' : 'PC'),
                ];
                
                $valuationQty = $is_kg ? ($closingStock / 1000) : $closingStock;
                $priceVal = (float)($p->wholesale_price ?: $p->price);
                $grandTotalValue += $valuationQty * $priceVal;
            }
        }

        if ($request->boolean('has_stock') || $request->has_stock == '1') {
            $rows = array_values(array_filter($rows, function($r) {
                return (float)($r['balance'] ?? 0) > 0;
            }));
        }

        return response()->json([
            'data'        => $rows,
            'grand_total' => $grandTotalValue,
            'total'       => count($rows),
        ]);
    }

    /**
     * Variant / Size-wise Stock Report
     * Returns all variants grouped under their parent product.
     */
    public function fetchVariantStock(Request $request)
    {
        $searchQ   = trim($request->get('q', ''));
        $productId = $request->get('product_id', 'all');

        $query = DB::table('product_variants')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->select(
                'products.id          as product_id',
                'products.item_code   as item_code',
                'products.item_name   as product_name',
                'products.unit_type   as unit_type',
                'categories.name      as category',
                'product_variants.id           as variant_id',
                'product_variants.size_label   as size_label',
                'product_variants.variant_name as variant_name',
                'product_variants.size_value   as size_value',
                'product_variants.size_unit    as size_unit',
                'product_variants.price        as price',
                'product_variants.stock_qty    as stock_qty',
                'product_variants.alert_quantity as alert_qty',
                'product_variants.is_default   as is_default'
            )
            ->where('product_variants.is_active', true)
            ->orderBy('products.item_name')
            ->orderBy('product_variants.size_value');

        $user = auth()->user();
        if ($user && $user->hasProductRestriction()) {
            $categoryIds = $user->getRestrictedCategoryIds();
            $productIds = $user->getRestrictedProductIds();
            $query->where(function($q) use ($categoryIds, $productIds) {
                if (empty($categoryIds) && empty($productIds)) {
                    $q->whereRaw('1 = 0');
                    return;
                }
                if (!empty($categoryIds)) {
                    $q->whereIn('products.category_id', $categoryIds);
                }
                if (!empty($productIds)) {
                    if (!empty($categoryIds)) {
                        $q->orWhereIn('products.id', $productIds);
                    } else {
                        $q->whereIn('products.id', $productIds);
                    }
                }
            });
        }

        if ($productId && $productId !== 'all') {
            $query->where('products.id', $productId);
        }

        if ($searchQ !== '') {
            $query->where(function($q) use ($searchQ) {
                $q->where('products.item_name', 'like', "%{$searchQ}%")
                  ->orWhere('products.item_code', 'like', "%{$searchQ}%")
                  ->orWhere('product_variants.size_label', 'like', "%{$searchQ}%");
            });
        }

        $variants = $query->get();
        $variantIds = $variants->pluck('variant_id')->toArray();
        $productIds = $variants->pluck('product_id')->unique()->toArray();

        $stocks = DB::table('stocks')
            ->whereIn('variant_id', $variantIds)
            ->where('branch_id', 1)
            ->where('warehouse_id', 1)
            ->pluck('qty', 'variant_id');

        $baseStocks = DB::table('stocks')
            ->whereIn('product_id', $productIds)
            ->whereNull('variant_id')
            ->where('branch_id', 1)
            ->where('warehouse_id', 1)
            ->pluck('qty', 'product_id');

        // Group by product
        $grouped = [];
        foreach ($variants as $v) {
            $pid = $v->product_id;
            if (!isset($grouped[$pid])) {
                $pName = $v->product_name ?: ('Item ' . $v->item_code);
                $grouped[$pid] = [
                    'product_id'   => $pid,
                    'item_code'    => $v->item_code,
                    'product_name' => $pName,
                    'category'     => $v->category ?? '–',
                    'total_stock'  => 0,
                    'sizes'        => [],
                ];
            }
            $label = $v->size_label ?: $v->variant_name ?: ('Size ' . $v->size_value . ' ' . $v->size_unit);
            
            // Stock resolution
            $stock = 0;
            if (isset($stocks[$v->variant_id]) && $stocks[$v->variant_id] > 0) {
                $stock = (float)$stocks[$v->variant_id];
            } elseif (isset($baseStocks[$v->product_id]) && $baseStocks[$v->product_id] > 0) {
                $baseQty = (float)$baseStocks[$v->product_id];
                if ($v->unit_type === 'kg' || $v->size_unit === 'kg') {
                    $stock = round($baseQty / 1000, 2); // Show KG for sizing
                } else {
                    $stock = $baseQty;
                }
            } else {
                $stock = (float)$v->stock_qty;
            }
            
            $grouped[$pid]['sizes'][] = [
                'variant_id'  => $v->variant_id,
                'label'       => $label,
                'price'       => (float)$v->price,
                'stock_qty'   => $stock,
                'alert_qty'   => (int)$v->alert_qty,
                'is_default'  => (bool)$v->is_default,
                'status'      => $stock <= 0 ? 'out' : ($stock <= ($v->alert_qty ?: 5) ? 'low' : 'ok'),
            ];
            $grouped[$pid]['total_stock'] += $stock;
        }

        if ($request->boolean('has_stock') || $request->has_stock == '1') {
            $grouped = array_filter($grouped, function($g) {
                return (float)($g['total_stock'] ?? 0) > 0 || !empty(array_filter($g['sizes'] ?? [], fn($s) => (float)($s['stock_qty'] ?? 0) > 0));
            });
        }

        return response()->json([
            'data'  => array_values($grouped),
            'total' => count($grouped),
        ]);
    }


    public function purchase_report()
    {
        return view('admin_panel.reporting.purchase_report');
    }

    public function fetchPurchaseReport(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        /* ================= NORMAL PURCHASE ================= */
        $purchaseQuery = DB::table('purchases')
            ->leftJoin('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->leftJoin('products', 'purchase_items.product_id', '=', 'products.id')
            ->leftJoin('vendors', 'purchases.vendor_id', '=', 'vendors.id')
            ->select(
                DB::raw("'purchase' as source_type"),
                'purchases.purchase_date as purchase_date',
                'purchases.invoice_no',
                'vendors.name as vendor_name',
                'products.item_code',
                'products.item_name',
                'purchase_items.qty',
                'purchase_items.unit',
                'purchase_items.price',
                'purchase_items.item_discount',
                'purchase_items.line_total',
                'purchases.subtotal',
                'purchases.discount',
                'purchases.extra_cost',
                'purchases.net_amount',
                'purchases.paid_amount',
                'purchases.due_amount'
            );

        if ($startDate && $endDate) {
            $purchaseQuery->whereBetween('purchases.purchase_date', [$startDate, $endDate]);
        }

        /* ================= INWARD AS PURCHASE ================= */
        $inwardQuery = DB::table('inward_gatepasses')
            ->leftJoin('inward_gatepass_items', 'inward_gatepasses.id', '=', 'inward_gatepass_items.inward_gatepass_id')
            ->leftJoin('products', 'inward_gatepass_items.product_id', '=', 'products.id')
            ->leftJoin('vendors', 'inward_gatepasses.vendor_id', '=', 'vendors.id')
            ->where('inward_gatepasses.status', 'linked')
            ->where('inward_gatepasses.bill_status', 'billed')
            ->select(
                DB::raw("'inward' as source_type"),
                'inward_gatepasses.gatepass_date as purchase_date',
                'inward_gatepasses.invoice_no',
                'vendors.name as vendor_name',
                'products.item_code',
                'products.item_name',
                'inward_gatepass_items.qty',
                DB::raw('products.unit_id as unit'),
                // Use the specific transaction price from the item table, not master product price
                DB::raw('COALESCE(inward_gatepass_items.price, products.wholesale_price) as price'),
                DB::raw('0 as item_discount'),
                // Calculate line total using the transaction price (no discount supported on inward items currently)
                DB::raw('(COALESCE(inward_gatepass_items.price, products.wholesale_price) * inward_gatepass_items.qty) as line_total'),
                'inward_gatepasses.subtotal',
                'inward_gatepasses.discount',
                'inward_gatepasses.extra_cost',
                'inward_gatepasses.net_amount',
                'inward_gatepasses.paid_amount',
                'inward_gatepasses.due_amount'
            );


        if ($startDate && $endDate) {
            $inwardQuery->whereBetween('gatepass_date', [$startDate, $endDate]);
        }

        /* ================= UNION ================= */
        $data = $purchaseQuery
            ->unionAll($inwardQuery)
            ->get()
            ->sort(function($a, $b) {
                if ($a->purchase_date === $b->purchase_date) {
                    // Secondary: invoice_no desc
                    return strcmp($b->invoice_no, $a->invoice_no);
                }
                // Primary: purchase_date desc
                return strcmp($b->purchase_date, $a->purchase_date);
            })
            ->values();

        // 🔹 Post-processing to remove duplicate invoice totals
        $seenInvoices = [];
        foreach ($data as $row) {
            $uniqueKey = $row->source_type . '_' . $row->invoice_no;

            if (in_array($uniqueKey, $seenInvoices)) {
                // Secondary item: Zero out invoice-level totals to prevent double counting
                $row->subtotal = 0;
                $row->discount = 0;
                $row->extra_cost = 0;
                $row->net_amount = 0;
                $row->paid_amount = 0;
                $row->due_amount = 0;
                $row->is_duplicate = true; 
            } else {
                // First item: Keep totals
                $seenInvoices[] = $uniqueKey;
                $row->is_duplicate = false;
            }
        }

        return response()->json([
            'data' => $data
        ]);
    }



    public function sale_report()
    {
        return view('admin_panel.reporting.sale_report');
    }

    public function fetchsaleReport(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->start_date;
            $end = $request->end_date;

            $query = DB::table('sales')
                ->leftJoin('customers', 'sales.customer', '=', 'customers.id')
                ->select(
                    'sales.id',
                    'sales.invoice_no', // ✅ Select invoice_no specifically
                    'sales.reference',
                    'sales.product',
                    'sales.variant_id',
                    'sales.product_code',
                    'sales.brand',
                    'sales.unit',
                    'sales.per_price',
                    'sales.per_discount',
                    'sales.qty',
                    'sales.per_total',
                    'sales.total_net',
                    'sales.created_at',
                    'customers.customer_name',
                    'sales.unit' // Add unit
                );

            if ($start && $end) {
                // Handle HTML5 datetime-local format (YYYY-MM-DDTHH:mm)
                $start = str_replace('T', ' ', $start);
                $end = str_replace('T', ' ', $end);
                
                // Append seconds if not present
                if (strlen($start) == 10) $start .= ' 00:00:00';
                if (strlen($end) == 10) $end .= ' 23:59:59';
                
                if (strlen($start) == 16) $start .= ':00';
                if (strlen($end) == 16) $end .= ':59';

                $query->whereBetween('sales.created_at', [$start, $end]);
            }

            // Filter by Customer Type/Category
            if ($request->has('customer_type')) {
                $types = $request->customer_type;

                // Ensure types is an array and not empty
                if (is_array($types) && count($types) > 0) {
                     $query->where(function($q) use ($types) {
                        
                        // 1. Handle Literal "Walk-in Customer" (Not a registered user ID)
                        if (in_array('Walking Customer', $types)) {
                            $q->orWhere('sales.customer', 'Walk-in Customer');
                        }

                        // 2. Filter by Category (Strictly Category, Ignore Type)
                        // This excludes customers like ID 12 who are Wholesalers but have Type='Walking Customer'
                        $q->orWhereIn('customers.customer_category', $types);
                     });
                }
            } else {
                // Default: Show 'Walk-in Customer' literal and 'Walking Customer' category
                 $query->where(function($q) {
                     $q->where('sales.customer', 'Walk-in Customer')
                       ->orWhere('customers.customer_category', 'Walking Customer');
                 });
            }

            $sales = $query->orderBy('sales.created_at', 'asc')->get();

            // 1. Collect all product and variant IDs for bulk fetching
            $allProdIds = [];
            $allVarIds  = [];
            foreach ($sales as $sale) {
                if (!empty($sale->product)) {
                    $ids = explode(',', $sale->product);
                    foreach($ids as $id) if(trim($id)) $allProdIds[] = trim($id);
                }
                if (!empty($sale->variant_id)) {
                    $vids = explode(',', $sale->variant_id);
                    foreach($vids as $vid) if(trim($vid)) $allVarIds[] = trim($vid);
                }
            }
            
            $productsDict = \App\Models\Product::whereIn('id', array_unique($allProdIds))->get()->keyBy('id');
            $variantsDict = \App\Models\ProductVariant::whereIn('id', array_unique($allVarIds))->get()->keyBy('id');

            foreach ($sales as $sale) {
                // --- Process Products, Variants and Units ---
                if (!empty($sale->product)) {
                    $pIds = explode(',', $sale->product);
                    $vIds = !empty($sale->variant_id) ? explode(',', $sale->variant_id) : [];
                    $qArr = explode(',', $sale->qty);
                    $prArr = explode(',', $sale->per_price);
                    
                    $orderedNames = [];
                    $orderedUnits = [];
                    $modifiedQtys = [];
                    $modifiedPrices = [];
                    
                    foreach ($pIds as $idx => $pid) {
                        $pid = trim($pid);
                        $p = $productsDict->get($pid);
                        $name = $p ? $p->item_name : '-';
                        $unit = $p ? strtoupper($p->unit_type ?? 'Piece') : '-';
                        $qty = isset($qArr[$idx]) ? floatval($qArr[$idx]) : 0;
                        $price = isset($prArr[$idx]) ? floatval($prArr[$idx]) : 0;
                        
                        $vid = isset($vIds[$idx]) ? trim($vIds[$idx]) : null;
                        if ($vid && $variantsDict->has($vid)) {
                            $v = $variantsDict->get($vid);
                            $name .= ' (' . ($v->size_label ?: $v->variant_name) . ')';
                            
                            // Check if we should convert to weight-based display
                            if ($p && strtolower($p->unit_type) === 'kg' && $v->size_value > 0) {
                                $multiplier = 1;
                                $sUnit = strtolower($v->size_unit ?? 'kg');
                                if ($sUnit === 'kg') {
                                    $multiplier = floatval($v->size_value);
                                } elseif ($sUnit === 'gm' || $sUnit === 'grams' || $sUnit === 'gram') {
                                    $multiplier = floatval($v->size_value) / 1000;
                                }
                                
                                // Convert display values: 2 boxes of 0.250kg -> 0.500kg
                                $qty = $qty * $multiplier;
                                // Adjust price to price-per-kg to keep total consistent
                                if ($multiplier > 0) {
                                    $price = $price / $multiplier;
                                }
                                $unit = 'KG';
                            } else {
                                // If not weight-based, use PIECE for variant count
                                $unit = 'PIECE';
                            }
                        }
                        
                        $orderedNames[]  = $name;
                        $orderedUnits[]  = $unit;
                        $modifiedQtys[]  = $qty;
                        $modifiedPrices[] = $price;
                    }

                    $sale->product_names = implode('|', $orderedNames);
                    $sale->unit          = implode('|', $orderedUnits);
                    $sale->qty           = implode(',', $modifiedQtys);
                    $sale->per_price     = implode(',', $modifiedPrices);
                } else {
                    $sale->product_names = '-';
                    $sale->unit          = '-';
                }

                // --- Merge Sale Returns ---
                $returnsRaw = DB::table('sales_returns')
                    ->where('sale_id', $sale->id)
                    ->get();
                
                $parsedReturns = [];
                foreach ($returnsRaw as $ret) {
                    $rProducts = explode(',', $ret->product ?? '');
                    $rQtys     = explode(',', $ret->qty ?? '');
                    $rTotals   = explode(',', $ret->per_total ?? ''); // or use per_price * qty if needed

                    foreach ($rProducts as $idx => $rProd) {
                        $q = isset($rQtys[$idx]) ? floatval($rQtys[$idx]) : 0;
                        $t = isset($rTotals[$idx]) ? floatval($rTotals[$idx]) : 0;
                        if ($q > 0) {
                            $parsedReturns[] = [
                                'product' => trim($rProd),
                                'qty'     => $q,
                                'amount'  => $t
                            ];
                        }
                    }
                }
                $sale->returns = $parsedReturns;
            }


            return response()->json($sales);
        }

        return view('admin_panel.reporting.sale_report');
    }



    public function sale_report_category()
    {
        $categories = Category::select('id', 'name')->get();
        return view('admin_panel.reporting.sale_report_category', compact('categories'));
    }

    public function fetchsalecategoryReport(Request $request)
    {
        if ($request->ajax()) {

            $start      = $request->start_date;
            $end        = $request->end_date;
            $categoryId = $request->category_id;
            $subCategoryId = $request->subcategory_id; // Get subcategory ID

            // ================== BASE SALES QUERY ==================
            $query = DB::table('sales')
                ->leftJoin('customers', 'sales.customer', '=', 'customers.id')
                ->select(
                    'sales.id',
                    'sales.invoice_no',
                    'sales.reference',
                    'sales.product',
                    'sales.variant_id',
                    'sales.product_code',
                    'sales.brand',
                    'sales.unit',
                    'sales.per_price',
                    'sales.per_discount',
                    'sales.qty',
                    'sales.per_total',
                    'sales.total_net',
                    'sales.created_at',
                    'customers.customer_name'
                )
                ->when($start && $end, function ($q) use ($start, $end) {
                    $start = str_replace('T', ' ', $start);
                    $end = str_replace('T', ' ', $end);
                    
                    if (strlen($start) == 10) $start .= ' 00:00:00';
                    if (strlen($end) == 10) $end .= ' 23:59:59';
                    
                    if (strlen($start) == 16) $start .= ':00';
                    if (strlen($end) == 16) $end .= ':59';

                    $q->whereBetween('sales.created_at', [$start, $end]);
                });

            // ================== CUSTOMER FILTERING ==================
            if ($request->has('customer_type')) {
                $types = $request->customer_type;
                if (is_array($types) && count($types) > 0) {
                     $query->where(function($q) use ($types) {
                        
                        // 1. Literal "Walk-in Customer"
                        if (in_array('Walking Customer', $types)) {
                            $q->orWhere('sales.customer', 'Walk-in Customer');
                        }
                        // 2. Category Match
                        $q->orWhereIn('customers.customer_category', $types);
                     });
                }
            } else {
                // Default: Walking
                 $query->where(function($q) {
                     $q->where('sales.customer', 'Walk-in Customer')
                       ->orWhere('customers.customer_category', 'Walking Customer');
                 });
            }

            $sales = $query->orderBy('sales.created_at', 'asc')->get();

            $finalSales = [];

            // 1. Collect all variant IDs for bulk fetching (products are already fetched per-sale or could be bulked too)
            $allVarIds = [];
            foreach ($sales as $sale) {
                if (!empty($sale->variant_id)) {
                    foreach(explode(',', $sale->variant_id) as $vid) if(trim($vid)) $allVarIds[] = trim($vid);
                }
            }
            $variantsDict = \App\Models\ProductVariant::whereIn('id', array_unique($allVarIds))->get()->keyBy('id');

            foreach ($sales as $sale) {
                if (empty($sale->product)) continue;

                $pIds     = explode(',', $sale->product);
                $vIds     = !empty($sale->variant_id) ? explode(',', $sale->variant_id) : [];
                $qtyArr   = explode(',', $sale->qty);
                $priceArr = explode(',', $sale->per_price);
                $totalArr = explode(',', $sale->per_total);

                // ================== PRODUCTS QUERY ==================
                // Filter products by category/subcategory if requested
                $productsQuery = DB::table('products')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('subcategories', 'products.sub_category_id', '=', 'subcategories.id')
                    ->whereIn('products.id', $pIds)
                    ->select('products.id','products.item_name','products.unit_type','categories.name as category_name','subcategories.name as subcategory_name');
                
                if ($categoryId)    $productsQuery->where('products.category_id', $categoryId);
                if ($subCategoryId) $productsQuery->where('products.sub_category_id', $subCategoryId);
                
                $matchedProducts = $productsQuery->get()->keyBy('id');

                if ($matchedProducts->isEmpty()) continue;

                $finalNames   = [];
                $finalCats    = [];
                $finalSubCats = [];
                $finalQtys    = [];
                $finalPrices  = [];
                $finalTotals  = [];
                $finalUnits   = [];

                // Iterate through the comma-separated arrays to maintain order and handle multiple instances/variants
                foreach ($pIds as $index => $pid) {
                    $pid = trim($pid);
                    if (!$matchedProducts->has($pid)) continue;

                    $product = $matchedProducts->get($pid);
                    $name    = $product->item_name;
                    $unit    = strtoupper($product->unit_type ?? 'Piece');
                    $qty     = (float)($qtyArr[$index] ?? 0);
                    $price   = (float)($priceArr[$index] ?? 0);
                    
                    $vid = isset($vIds[$index]) ? trim($vIds[$index]) : null;
                    if ($vid && $variantsDict->has($vid)) {
                        $v = $variantsDict->get($vid);
                        $name .= ' (' . ($v->size_label ?: $v->variant_name) . ')';
                        
                        // Check weight-based conversion
                        if ($product && strtolower($product->unit_type) === 'kg' && $v->size_value > 0) {
                            $multiplier = 1;
                            $sUnit = strtolower($v->size_unit ?? 'kg');
                            if ($sUnit === 'kg') {
                                $multiplier = floatval($v->size_value);
                            } elseif ($sUnit === 'gm' || $sUnit === 'grams' || $sUnit === 'gram') {
                                $multiplier = floatval($v->size_value) / 1000;
                            }
                            
                            $qty = $qty * $multiplier;
                            if ($multiplier > 0) {
                                $price = $price / $multiplier;
                            }
                            $unit = 'KG';
                        } else {
                            $unit = 'PIECE';
                        }
                    }

                    $finalNames[]   = $name;
                    $finalCats[]    = $product->category_name ?? '-';
                    $finalSubCats[] = $product->subcategory_name ?? '-';
                    $finalQtys[]    = $qty;
                    $finalPrices[]  = $price;
                    $finalTotals[]  = (float)($totalArr[$index] ?? 0);
                    $finalUnits[]   = $unit;
                }

                if (empty($finalNames)) continue;

                $sale->product_names  = implode('|', $finalNames);
                $sale->categories     = implode(', ', array_unique($finalCats));
                $sale->subcategories  = implode(', ', array_unique($finalSubCats));
                $sale->filtered_qty   = implode('|', $finalQtys);
                $sale->filtered_price = implode('|', $finalPrices);
                $sale->filtered_total = implode('|', $finalTotals);
                $sale->filtered_unit  = implode('|', $finalUnits); 
                $sale->filtered_net   = array_sum($finalTotals);

                $sale->returns = DB::table('sales_returns')->where('sale_id', $sale->id)->get();
                $finalSales[] = $sale;
            }

            return response()->json($finalSales);
        }

        return view('admin_panel.reporting.sale_report_category'); // Should not be reached if handled by first method, but keeping safe
    }



    public function customer_ledger_report()
    {
        $customers = DB::table('customers')->select('id', 'customer_name')->get();

        return view('admin_panel.reporting.customer_ledger_report', compact('customers'));
    }

    public function fetch_customer_ledger(Request $request)
    {
        $customerId = $request->customer_id;
        $start = $request->start_date;
        $end = $request->end_date . ' 23:59:59';

        // Customer info
        $customer = DB::table('customers')->where('id', $customerId)->first();

        // ---------------- CALCULATE OPENING BALANCE DYNAMICALLY ----------------
        // 1. Initial Opening from Customer
        $initial = $customer->opening_balance ?? 0;

        // 2. Prior Sales (Net of Returns as stored in DB)
        $prevSales = DB::table('sales')
            ->where('customer', $customerId)
            ->where('created_at', '<', $start)
            ->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));

        // 3. Prior Payments
        $prevPayments = DB::table('customer_payments')
            ->where('customer_id', $customerId)
            ->where('payment_date', '<', $start)
            ->sum('amount');

        // 4. Returns that happened ON/AFTER StartDate, but belong to Prior Sales.
        // These need to be added back because the Prior Sales Sum is already reduced by them,
        // but the credit event hasn't happened yet in this timeline.
        $addBackReturns = DB::table('sales_returns')
            ->join('sales', 'sales_returns.sale_id', '=', 'sales.id')
            ->where('sales_returns.customer', $customerId)
            ->where('sales_returns.created_at', '>=', $start)
            ->where('sales.created_at', '<', $start)
            ->sum('sales_returns.total_net');

        $opening = $initial + $prevSales - $prevPayments + $addBackReturns;

        // ---------------- FETCH ALL SALE RETURNS FIRST ----------------
        $allSaleReturns = DB::table('sales_returns')
            ->where('customer', $customerId)
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy('sale_id'); // group by sale_id for easy lookup

        // ---------------- SALES (Debit) ----------------
        $sales = DB::table('sales')
            ->where('customer', $customerId)
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->map(function ($s) use ($allSaleReturns) {
                $fullSaleAmount = $s->total_net ?? $s->total_bill_amount;

                // Check if this sale has any returns
                $returnTotal = 0;
                if (isset($allSaleReturns[$s->id])) {
                    $returnTotal = $allSaleReturns[$s->id]->sum('total_net');
                }

                // Sale debit = original sale + total of related returns
                $debitAmount = $fullSaleAmount + $returnTotal;

                return [
                    'date' => $s->created_at,
                    'sort_type' => 1,
                    'invoice' => $s->invoice_no,
                    'reference' => $s->reference,
                    'description' => 'To Sale A/c',
                    'debit' => $debitAmount,
                    'credit' => 0,
                    'original_sale_id' => $s->id
                ];
            });

        // ---------------- SALE RETURNS (Credit) ----------------
        $saleReturns = collect();
        foreach ($allSaleReturns as $saleId => $returns) {
            foreach ($returns as $r) {
                $saleReturns->push([
                    'date' => $r->created_at,
                    'sort_type' => 3,
                    'invoice' => 'SR-' . $r->sale_id,
                    'reference' => $r->reference,
                    'description' => 'By Sale Return',
                    'debit' => 0,
                    'credit' => $r->total_net,
                    'original_sale_id' => $r->sale_id
                ]);
            }
        }

        // ---------------- PAYMENTS (Credit) ----------------
        $payments = DB::table('customer_payments')
            ->where('customer_id', $customerId)
            ->whereBetween('payment_date', [$start, $end])
            ->get()
            ->map(function ($p) {
                return [
                    'date' => $p->payment_date . ' 23:59:59',
                    'sort_type' => 2,
                    'invoice' => $p->received_no,
                    'reference' => $p->payment_method,
                    'description' => $p->note ?? 'Payment Received',
                    'debit' => 0,
                    'credit' => $p->amount,
                ];
            });

        // ---------------- MERGE + SORT ----------------
        $transactions = collect()
            ->merge($sales)
            ->merge($saleReturns)
            ->merge($payments)
            ->sort(function ($a, $b) {
                $dateA = strtotime($a['date']);
                $dateB = strtotime($b['date']);
                if ($dateA != $dateB) return $dateA <=> $dateB;

                // Sale → Sale Return → Payment
                $order = [1 => 1, 3 => 2, 2 => 3];
                return $order[$a['sort_type']] <=> $order[$b['sort_type']];
            })
            ->values()
            ->all();

        // ---------------- RUNNING BALANCE ----------------
        $balance = $opening;
        foreach ($transactions as $key => $t) {
            $balance += $t['debit'];
            $balance -= $t['credit'];
            $transactions[$key]['balance'] = $balance;
        }

        return response()->json([
            'customer' => $customer,
            'opening_balance' => $opening,
            'transactions' => $transactions,
        ]);
    }





    public function vendor_ledger_report()
    {
        $vendors = DB::table('vendors')->select('id', 'name')->get();

        return view('admin_panel.reporting.vendor_ledger_report', compact('vendors'));
    }

    public function fetch_vendor_ledger(Request $request)
    {
        $vendorId = $request->vendor_id;
        $start = $request->start_date;
        $end = $request->end_date . ' 23:59:59';

        $vendor = DB::table('vendors')->where('id', $vendorId)->first();
        // ---------------- CALCULATE OPENING BALANCE DYNAMICALLY ----------------
        // 1. Initial Opening from Vendor
        $initial = $vendor->opening_balance ?? 0;

        // 2. Prior Purchases (Debit: We owe more)
        $prevPurchases = DB::table('purchases')
            ->where('vendor_id', $vendorId)
            ->where('purchase_date', '<', $start)
            ->sum('net_amount');

        // 2b. Prior Inwards (Debit: We owe more)
        $prevInwards = DB::table('inward_gatepasses')
            ->where('vendor_id', $vendorId)
            ->where('bill_status', 'billed')
            ->where('gatepass_date', '<', $start)
            ->sum('net_amount');

        // 3. Prior Returns (Credit: We owe less)
        $prevReturns = DB::table('purchase_returns')
            ->where('vendor_id', $vendorId)
            ->where('return_date', '<', $start)
            ->sum('net_amount');

        // 4. Prior Payments (Credit: We owe less)
        $prevPayments = DB::table('vendor_payments')
            ->where('vendor_id', $vendorId)
            ->where('payment_date', '<', $start)
            ->sum('amount');

        // 5. Prior Bilties (Debit: We owe more)
        $prevBilties = DB::table('vendor_bilties')
            ->where('vendor_id', $vendorId)
            ->where('delivery_date', '<', $start)
            ->sum('amount');

        $opening = $initial + $prevPurchases + $prevInwards + $prevBilties - $prevReturns - $prevPayments;

        // 🔹 1. Purchases → Debit (we owe vendor)
        $purchases = DB::table('purchases')
            ->where('vendor_id', $vendorId)
            ->whereBetween('purchase_date', [$start, $end])
            ->select('purchase_date', 'invoice_no', 'net_amount', 'note') // Explicitly select note
            ->get()
            ->map(function ($p) {
                return [
                    'date' => $p->purchase_date,
                    'invoice' => $p->invoice_no,
                    'description' => $p->note ?: 'Purchase Invoice', // Use note if available
                    'debit' => $p->net_amount,
                    'credit' => 0,
                    'sort_date' => $p->purchase_date
                ];
            });

        // 🔹 1b. Inward Bills → Debit (we owe vendor)
        $inwards = DB::table('inward_gatepasses')
            ->where('vendor_id', $vendorId)
            ->where('bill_status', 'billed')
            ->whereBetween('gatepass_date', [$start, $end])
            ->get()
            ->map(function ($i) {
                return [
                    'date' => $i->gatepass_date,
                    'invoice' => $i->invoice_no . ' (' . $i->gatepass_no . ')',
                    'description' => 'Inward Bill - ' . ($i->remarks ?? ''),
                    'debit' => $i->net_amount,
                    'credit' => 0,
                    'sort_date' => $i->gatepass_date
                ];
            });

        // 🔹 2. Purchase Returns → Credit (reduces vendor balance)
        $returns = DB::table('purchase_returns')
            ->where('vendor_id', $vendorId)
            ->whereBetween('return_date', [$start, $end])
            ->get()
            ->map(function ($r) {
                return [
                    'date' => $r->return_date,
                    'invoice' => $r->return_invoice,
                    'description' => 'Purchase Return',
                    'debit' => 0,
                    'credit' => $r->net_amount,
                    'sort_date' => $r->return_date
                ];
            });

        // 🔹 3. Vendor Payments → Credit (we paid vendor)
        $payments = DB::table('vendor_payments')
            ->where('vendor_id', $vendorId)
            ->whereBetween('payment_date', [$start, $end])
            ->get()
            ->map(function ($v) {
                return [
                    'date' => $v->payment_date,
                    'invoice' => $v->payment_no,
                    'reference' => $v->payment_method,
                    'description' => $v->note ?? 'Cash Given',
                    'debit' => 0,
                    'credit' => $v->amount,
                    'sort_date' => $v->payment_date
                ];
            });

        // 🔹 4. Vendor Bilties → Debit (we owe vendor for freight/logistics)
        $bilties = DB::table('vendor_bilties')
            ->where('vendor_id', $vendorId)
            ->whereBetween('delivery_date', [$start, $end])
            ->get()
            ->map(function ($b) {
                return [
                    'date' => $b->delivery_date,
                    'invoice' => $b->bilty_no ?: 'Bilty',
                    'description' => $b->note ?: 'Bilty Amount',
                    'debit' => $b->amount,
                    'credit' => 0,
                    'sort_date' => $b->delivery_date
                ];
            });

        // 🔹 Merge all
        $transactions = $purchases
            ->merge($inwards)
            ->merge($returns)
            ->merge($payments)
            ->merge($bilties)
            ->sortBy('sort_date')
            ->values()
            ->all();

        // 🔹 Running Balance Calculation (Debit increases, Credit decreases)

        $balance = $opening;

        foreach ($transactions as $key => $t) {

            $debit  = (float) ($t['debit'] ?? 0);
            $credit = (float) ($t['credit'] ?? 0);

            $balance = $balance + $debit - $credit;

            $transactions[$key]['balance'] = round($balance, 2);
        }


        return response()->json([
            'vendor' => $vendor,
            'opening_balance' => $opening,
            'transactions' => $transactions,
        ]);
    }

    public function vendor_ledger_pdf(Request $request)
    {
        $vendorId = $request->vendor_id;
        $start = $request->start_date;
        $end = $request->end_date;

        if (!$vendorId || !$start || !$end) {
            return redirect()->route('report.vendor.ledger')->with('error', 'Please select vendor and dates');
        }

        $request->merge(['end_date' => $end]);
        $response = $this->fetch_vendor_ledger($request);
        $data = $response->getData();

        $pdf = Pdf::loadView('admin_panel.reporting.vendor_ledger_pdf', [
            'vendor' => $data->vendor,
            'opening_balance' => $data->opening_balance,
            'transactions' => $data->transactions,
            'start_date' => $start,
            'end_date' => $end,
        ]);

        return $pdf->download('Vendor_Ledger_' . $vendorId . '.pdf');
    }

    public function cashbook(Request $request)
    {
        $selectedDate = $request->get('date', Carbon::today()->toDateString());
        $today = $selectedDate;
        $startDate = $request->get('start_date', Carbon::today()->subDays(30)->toDateString());
        $startTime = $request->start_time ?? '00:00';
        $endTime   = $request->end_time   ?? '23:59';

        $startDT = $selectedDate . ' ' . $startTime . ':00';
        $endDT   = $selectedDate . ' ' . $endTime   . ':59';

        /* ================= OPENING BALANCE ================= */
        $previousSales = Sale::where('created_at', '>=', $startDate . ' 00:00:00')
            ->where('created_at', '<', $startDT)->sum('total_net');
        $previousCustomerRecoveries = CustomerPayment::where('payment_date', '>=', $startDate . ' 00:00:00')
            ->where('payment_date', '<', $startDT)->sum('amount');
        $previousVendorPayments = VendorPayment::where('payment_date', '>=', $startDate . ' 00:00:00')
            ->where('payment_date', '<', $startDT)->sum('amount');
        $previousExpenses = ExpenseVoucher::where('date', '>=', $startDate . ' 00:00:00')
            ->where('date', '<', $startDT)->sum('total_amount');
        $openingBalance = ($previousSales + $previousCustomerRecoveries) - ($previousVendorPayments + $previousExpenses);

        /* ================= TODAY'S DATA (with time filter) ================= */
        $allSales = Sale::where('created_at', '>=', $startDT)
            ->where('created_at', '<=', $endDT)->get();
        $customerRecoveries = CustomerPayment::with('customer')
            ->where('payment_date', '>=', $startDT)
            ->where('payment_date', '<=', $endDT)->get();
        $vendorPayments = VendorPayment::with('vendor')
            ->where('payment_date', '>=', $startDT)
            ->where('payment_date', '<=', $endDT)->get();
        $expenseVouchers = ExpenseVoucher::where('date', '>=', $startDT)
            ->where('date', '<=', $endDT)->get();

        /* ================= RECEIPTS BREAKDOWN ================= */
        $totalSaleCash = $allSales->sum('cash');
        $totalSaleCard = $allSales->sum('card');
        $totalChange   = $allSales->sum('change');
        $totalSaleNet  = $allSales->sum('total_net');
        $saleCount     = $allSales->count();

        $recoveryByMethod = [];
        foreach ($customerRecoveries as $cr) {
            $m = $cr->payment_method ?? 'Other';
            if (!isset($recoveryByMethod[$m])) $recoveryByMethod[$m] = 0;
            $recoveryByMethod[$m] += $cr->amount;
        }
        $totalRecoveries = $customerRecoveries->sum('amount');
        $totalReceipts = $totalSaleNet + $totalRecoveries;

        /* ================= PAYMENTS BREAKDOWN ================= */
        $vendorPayByMethod = [];
        foreach ($vendorPayments as $vp) {
            $m = $vp->payment_method ?? 'Other';
            if (!isset($vendorPayByMethod[$m])) $vendorPayByMethod[$m] = 0;
            $vendorPayByMethod[$m] += $vp->amount;
        }
        $totalVendorPayments = $vendorPayments->sum('amount');
        $totalExpenses = $expenseVouchers->sum('total_amount');
        $totalPayments = $totalVendorPayments + $totalExpenses;
        $closingBalance = $openingBalance + $totalReceipts - $totalPayments;

        /* ================= DETAILED ENTRIES ================= */
        $receipts = [];
        foreach ($allSales as $sale) {
            $receipts[] = ['title' => 'Sale', 'ref' => '#' . $sale->invoice_no, 'amount' => $sale->total_net];
        }
        foreach ($customerRecoveries as $cr) {
            $receipts[] = ['title' => 'Recovery', 'ref' => ($cr->customer->customer_name ?? '-') . ' (' . ($cr->payment_method ?? 'N/A') . ')', 'amount' => $cr->amount];
        }

        $payments = [];
        foreach ($vendorPayments as $vp) {
            $payments[] = ['title' => 'Vendor Pay', 'ref' => ($vp->vendor->name ?? '-') . ' (' . ($vp->payment_method ?? 'N/A') . ')', 'amount' => $vp->amount];
        }
        foreach ($expenseVouchers as $exp) {
            $remarks = is_array($exp->remarks) ? implode(', ', $exp->remarks) : ($exp->remarks ?? '');
            $payments[] = ['title' => 'Expense', 'ref' => $remarks ?: 'Voucher #' . $exp->evid, 'amount' => $exp->total_amount];
        }

        $maxRows = max(count($receipts), count($payments));

        return view('admin_panel.reporting.CashBook', compact(
            'receipts', 'payments', 'maxRows',
            'totalReceipts', 'totalPayments',
            'openingBalance', 'closingBalance',
            'selectedDate', 'startDate', 'startTime', 'endTime',
            'totalSaleCash', 'totalSaleCard', 'totalChange', 'totalSaleNet', 'saleCount',
            'recoveryByMethod', 'totalRecoveries',
            'vendorPayByMethod', 'totalVendorPayments',
            'totalExpenses', 'allSales', 'customerRecoveries', 'vendorPayments', 'expenseVouchers'
        ));
    }




    public function expense_vocher(Request $request)
    {
        $accountHeads = \App\Models\AccountHead::where('status', 1)->get();
        $accounts     = \App\Models\Account::where('status', 1)->get();

        $vouchers = collect();
        $grandTotal = 0;

        if ($request->hasAny(['account_heads', 'accounts', 'start_date', 'end_date'])) {

            $query = \App\Models\ExpenseVoucher::query();
            
            // 🛡️ Restrict non-admin users to their own expenses
            if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
                $query->where('user_id', auth()->id());
            }

            // Account Head filter (type = account_head_id)
            if ($request->filled('account_heads') && !in_array('all', $request->account_heads)) {
                $query->whereIn('type', $request->account_heads);
            }

            // Account filter (party_id = account_id)
            if ($request->filled('accounts')) {
                $query->whereIn('party_id', $request->accounts);
            }

            // Date filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('date', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $vouchers = $query->latest()->get();

            // Grand Total
            $grandTotal = $vouchers->sum('total_amount');
        }

        return view(
            'admin_panel.reporting.expense_vocher',
            compact('accountHeads', 'accounts', 'vouchers', 'grandTotal')
        );
    }

    public function expenseVoucherAjax(Request $request)
    {
        $query = \App\Models\ExpenseVoucher::query();

        // 🛡️ Restrict non-admin users to their own expenses
        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $query->where('user_id', auth()->id());
        }

        // Account Head (type)
        if ($request->filled('account_heads') && !in_array('all', $request->account_heads)) {
            $query->whereIn('type', $request->account_heads);
        }

        // Accounts (party_id)
        if ($request->filled('accounts')) {
            $query->whereIn('party_id', $request->accounts);
        }

        // Date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $vouchers = $query->latest()->get();

        $data = $vouchers->map(function ($v) {

            // remarks decode (JSON safe)
            $remarks = json_decode($v->remarks, true);

            return [
                'evid'    => $v->evid,
                'date'    => \Carbon\Carbon::parse($v->date)->format('d-m-Y'),
                'head'    => optional(\App\Models\AccountHead::find($v->type))->name,
                'account' => optional(\App\Models\Account::find($v->party_id))->title,
                'remarks' => is_array($remarks) ? implode(', ', $remarks) : ($v->remarks ?? '-'),
                'amount'  => number_format($v->total_amount, 2),
            ];
        });

        return response()->json([
            'rows' => $data,
            'total' => number_format($vouchers->sum('total_amount'), 2)
        ]);
    }

    public function sale_closing_report()
    {
        $users = \App\Models\User::all();
        return view('admin_panel.reporting.sale_closing_report', compact('users'));
    }

    public function fetchSaleClosingReport(Request $request)
    {
        $start = $request->start_date ?? date('Y-m-d');
        $end   = $request->end_date   ?? date('Y-m-d');
        
        $start = str_replace('T', ' ', $start);
        $end = str_replace('T', ' ', $end);
        
        if (strlen($start) == 10) $start .= ' 00:00:00';
        if (strlen($end) == 10) $end .= ' 23:59:59';
        if (strlen($start) == 16) $start .= ':00';
        if (strlen($end) == 16) $end .= ':59';

        $userId = $request->user_id;

        // 1. Fetch Sales
        $salesQuery = DB::table('sales')
            ->whereBetween('created_at', [$start, $end]);

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $salesQuery->where('user_id', auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $salesQuery->where('user_id', $userId);
        }

        $sales = $salesQuery->select('id', 'invoice_no', 'total_net', 'created_at', 'cash', 'card', 'change', 'per_price', 'qty', 'per_total', 'total_bill_amount', 'total_extradiscount')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalSale = 0;
        $totalCash = 0;
        $totalCard = 0;
        $totalDiscount = 0;
        
        foreach ($sales as $s) {
            $net = floatval($s->total_net);
            $crd = floatval($s->card);
            
            // Card taken cannot exceed the net bill
            $actualCard = min($net, $crd);
            
            // The rest of the bill is assumed to be paid in cash
            $actualCash = $net - $actualCard;
            
            $totalSale += $net;
            $totalCard += $actualCard;
            $totalCash += $actualCash;

            // Compute total discount for this sale
            $prices = explode(',', $s->per_price ?? '');
            $qtys = explode(',', $s->qty ?? '');
            $totals = explode(',', $s->per_total ?? '');
            $grossTotal = 0;
            $itemTotal = 0;
            $maxIdx = max(count($prices), count($qtys), count($totals));
            for ($i = 0; $i < $maxIdx; $i++) {
                $grossTotal += (float)($prices[$i] ?? 0) * (float)($qtys[$i] ?? 0);
                $itemTotal += (float)($totals[$i] ?? 0);
            }
            $itemDiscount = $grossTotal - $itemTotal;
            $extraDiscount = (float)($s->total_extradiscount ?? 0);
            $totalDiscount += $itemDiscount + $extraDiscount;
        }

        // 2. Fetch Expenses
        $expenseQuery = DB::table('expense_vouchers')
            ->whereBetween('created_at', [$start, $end]);

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $expenseQuery->where('user_id', auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $expenseQuery->where('user_id', $userId);
        }

        $expenses = $expenseQuery->select('id', 'evid', 'amount', 'date', 'type', 'party_id')
            ->orderBy('date', 'asc')
            ->get();

        $processedExpenses = [];
        $totalExpense = 0;
        foreach ($expenses as $ex) {
            $amounts = json_decode($ex->amount, true) ?: [];
            $sum = array_sum($amounts);
            $totalExpense += $sum;
            
            $ex->total_amount = $sum;
            $processedExpenses[] = $ex;
        }

        // 3. Fetch Returns
        $returnsQuery = DB::table('sales_returns')
            ->join('sales', 'sales_returns.sale_id', '=', 'sales.id')
            ->whereBetween('sales_returns.created_at', [$start, $end]);

        $userCol = \Illuminate\Support\Facades\Schema::hasColumn('sales_returns', 'user_id') ? 'sales_returns.user_id' : 'sales.user_id';

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $returnsQuery->where($userCol, auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $returnsQuery->where($userCol, $userId);
        }

        $returns = $returnsQuery->select(
                'sales_returns.id', 
                'sales_returns.sale_id', 
                'sales_returns.total_net', 
                'sales_returns.created_at', 
                'sales_returns.cash', 
                'sales_returns.card',
                'sales.invoice_no as original_invoice_no',
                'sales.created_at as original_sale_date'
            )
            ->orderBy('sales_returns.created_at', 'asc')
            ->get();

        $totalReturn = 0;
        $totalReturnCash = 0;
        $totalReturnCard = 0;

        foreach ($returns as $r) {
            $net = floatval($r->total_net);
            $crd = floatval($r->card);
            
            $actualCard = min($net, $crd);
            $actualCash = $net - $actualCard;
            
            $totalReturn += $net;
            $totalReturnCard += $actualCard;
            $totalReturnCash += $actualCash;
        }

        return response()->json([
            'sales' => $sales,
            'expenses' => $processedExpenses,
            'returns' => $returns,
            'total_sale' => $totalSale,
            'total_cash' => $totalCash,
            'total_card' => $totalCard,
            'total_expense' => $totalExpense,
            'total_return' => $totalReturn,
            'total_return_cash' => $totalReturnCash,
            'total_return_card' => $totalReturnCard,
            'net_amount' => $totalSale - $totalReturn - $totalExpense,
            'total_discount' => $totalDiscount,
            'start_date' => $start,
            'end_date' => $end
        ]);
    }

    public function printSaleClosingReport(Request $request)
    {
        $start = $request->start_date ?? date('Y-m-d');
        $end   = $request->end_date   ?? date('Y-m-d');
        
        $start = str_replace('T', ' ', $start);
        $end = str_replace('T', ' ', $end);
        
        if (strlen($start) == 10) $start .= ' 00:00:00';
        if (strlen($end) == 10) $end .= ' 23:59:59';
        if (strlen($start) == 16) $start .= ':00';
        if (strlen($end) == 16) $end .= ':59';

        $userId = $request->user_id;

        $salesQuery = DB::table('sales')
            ->whereBetween('created_at', [$start, $end]);

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $salesQuery->where('user_id', auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $salesQuery->where('user_id', $userId);
        }

        $sales = $salesQuery->select('id', 'invoice_no', 'total_net', 'created_at', 'cash', 'card', 'change', 'per_price', 'qty', 'per_total', 'total_bill_amount', 'total_extradiscount')
            ->get();

        $totalSale = 0;
        $totalCash = 0;
        $totalCard = 0;
        $totalDiscount = 0;
        
        foreach ($sales as $s) {
            $net = floatval($s->total_net);
            $crd = floatval($s->card);
            
            $actualCard = min($net, $crd);
            $actualCash = $net - $actualCard;
            
            $totalSale += $net;
            $totalCard += $actualCard;
            $totalCash += $actualCash;

            // Compute total discount for this sale
            $prices = explode(',', $s->per_price ?? '');
            $qtys = explode(',', $s->qty ?? '');
            $totals = explode(',', $s->per_total ?? '');
            $grossTotal = 0;
            $itemTotal = 0;
            $maxIdx = max(count($prices), count($qtys), count($totals));
            for ($i = 0; $i < $maxIdx; $i++) {
                $grossTotal += (float)($prices[$i] ?? 0) * (float)($qtys[$i] ?? 0);
                $itemTotal += (float)($totals[$i] ?? 0);
            }
            $itemDiscount = $grossTotal - $itemTotal;
            $extraDiscount = (float)($s->total_extradiscount ?? 0);
            $totalDiscount += $itemDiscount + $extraDiscount;
        }

        $expenseQuery = DB::table('expense_vouchers')
            ->whereBetween('created_at', [$start, $end]);

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $expenseQuery->where('user_id', auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $expenseQuery->where('user_id', $userId);
        }

        $expenses = $expenseQuery->get();

        $totalExpense = 0;
        foreach ($expenses as $ex) {
            $amounts = json_decode($ex->amount, true) ?: [];
            $totalExpense += array_sum($amounts);
        }

        // Fetch Returns
        $returnsQuery = DB::table('sales_returns')
            ->join('sales', 'sales_returns.sale_id', '=', 'sales.id')
            ->whereBetween('sales_returns.created_at', [$start, $end]);

        $userCol = \Illuminate\Support\Facades\Schema::hasColumn('sales_returns', 'user_id') ? 'sales_returns.user_id' : 'sales.user_id';

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $returnsQuery->where($userCol, auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $returnsQuery->where($userCol, $userId);
        }

        $returns = $returnsQuery->get();

        $totalReturn = 0;
        $totalReturnCash = 0;
        $totalReturnCard = 0;

        foreach ($returns as $r) {
            $net = floatval($r->total_net);
            $crd = floatval($r->card);
            
            $actualCard = min($net, $crd);
            $actualCash = $net - $actualCard;
            
            $totalReturn += $net;
            $totalReturnCard += $actualCard;
            $totalReturnCash += $actualCash;
        }

        $userName = 'All Users';
        if ($userId && $userId !== 'all') {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $userName = $user->name;
            }
        }

        return view('admin_panel.reporting.sale_closing_print', [
            'totalSale' => $totalSale,
            'totalCash' => $totalCash,
            'totalCard' => $totalCard,
            'totalDiscount' => $totalDiscount,
            'totalExpense' => $totalExpense,
            'totalReturn' => $totalReturn,
            'totalReturnCash' => $totalReturnCash,
            'totalReturnCard' => $totalReturnCard,
            'netAmount' => $totalSale - $totalReturn - $totalExpense,
            'startDate' => $start,
            'endDate' => $end,
            'salesCount' => $sales->count(),
            'expensesCount' => $expenses->count(),
            'returnsCount' => $returns->count(),
            'userName' => $userName
        ]);
    }

    /**
     * Print Closing Report (Black Copper / thermal receipt)
     * Business day: 7am to 3am next day
     */
    public function printClosing(Request $request)
    {
        $productIds = $request->input('product_id', 'all');
        $startTime  = $request->start_time ?? '07:00:00';
        $endTime    = $request->end_time   ?? '03:00:00';

        // Auto-compute closing period if not provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate   = $request->end_date;
        } else {
            $today = now();
            $startDate = $today->copy()->setHour(7)->setMinute(0)->setSecond(0)->format('Y-m-d');
            $endDate   = $today->copy()->addDay()->setHour(3)->setMinute(0)->setSecond(0)->format('Y-m-d');
        }

        $startDT = $startDate . ' ' . $startTime;
        $endDT   = $endDate   . ' ' . $endTime;

        $request->merge([
            'product_id' => $productIds,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ]);

        $response = $this->fetchItemStock($request);
        $json = $response->getData();

        $rows = $json->data ?? [];
        $total = $json->total ?? 0;

        $dateLabel = \Carbon\Carbon::parse($startDate)->format('d-M-Y');
        $timeLabel = $startTime . ' to ' . $endTime;

        return view('admin_panel.reporting.closing_print', compact('rows', 'total', 'startDate', 'endDate', 'dateLabel', 'timeLabel'));
    }
}
