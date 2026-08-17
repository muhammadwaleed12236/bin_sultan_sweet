<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawMaterial;
use App\Models\RawMaterialVendor;
use App\Models\RawMaterialPurchase;
use App\Models\RawMaterialPurchaseItem;
use App\Models\RawMaterialVendorLedger;
use App\Models\RawMaterialOut;
use App\Models\RawMaterialOutItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'materials');

        // Summary Stats
        $totalMaterials = RawMaterial::count();
        $alertMaterials = RawMaterial::whereColumn('stock_qty', '<=', 'alert_qty')->count();
        $totalPurchaseAmount = RawMaterialPurchase::sum('net_amount');
        $totalVendors = RawMaterialVendor::count();
        $totalPayableBalance = RawMaterialVendor::sum('closing_balance');
        $totalOutsCount = RawMaterialOut::count();

        // Data lists
        $materials = RawMaterial::orderBy('id', 'desc')->get();
        $vendors = RawMaterialVendor::with('ledgers')->orderBy('id', 'desc')->get();
        $outs = RawMaterialOut::with(['items.rawMaterial', 'creator'])->orderBy('out_date', 'desc')->orderBy('id', 'desc')->get();
        
        $purchasesQuery = RawMaterialPurchase::with(['vendor', 'items.rawMaterial'])->orderBy('purchase_date', 'desc')->orderBy('id', 'desc');
        if ($request->filled('date_from')) {
            $purchasesQuery->whereDate('purchase_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $purchasesQuery->whereDate('purchase_date', '<=', $request->date_to);
        }
        if ($request->filled('vendor_id')) {
            $purchasesQuery->where('vendor_id', $request->vendor_id);
        }
        $purchases = $purchasesQuery->get();

        // Vendor Ledger Data
        $selectedVendorId = $request->get('ledger_vendor_id');
        $ledgerDateFrom = $request->get('ledger_date_from');
        $ledgerDateTo = $request->get('ledger_date_to');

        $ledgerQuery = RawMaterialVendorLedger::with('vendor')->orderBy('date', 'asc')->orderBy('id', 'asc');

        if ($selectedVendorId) {
            $ledgerQuery->where('vendor_id', $selectedVendorId);
        }
        if ($ledgerDateFrom) {
            $ledgerQuery->whereDate('date', '>=', $ledgerDateFrom);
        }
        if ($ledgerDateTo) {
            $ledgerQuery->whereDate('date', '<=', $ledgerDateTo);
        }

        $ledgers = $ledgerQuery->get();
        $selectedVendor = $selectedVendorId ? RawMaterialVendor::find($selectedVendorId) : null;

        // Raw Material Stock Report Data & Filtering
        $stockDateFrom = $request->get('stock_date_from');
        $stockDateTo = $request->get('stock_date_to');
        $stockMaterialId = $request->get('stock_material_id');

        $stockMaterialsQuery = RawMaterial::orderBy('name', 'asc');
        if ($stockMaterialId) {
            $stockMaterialsQuery->where('id', $stockMaterialId);
        }
        $stockReportMaterials = $stockMaterialsQuery->get();

        $stockReportData = $stockReportMaterials->map(function($rm) use ($stockDateFrom, $stockDateTo) {
            $purchQuery = DB::table('raw_material_purchase_items')
                ->join('raw_material_purchases', 'raw_material_purchase_items.raw_material_purchase_id', '=', 'raw_material_purchases.id')
                ->where('raw_material_purchase_items.raw_material_id', $rm->id);

            $outQuery = DB::table('raw_material_out_items')
                ->join('raw_material_outs', 'raw_material_out_items.raw_material_out_id', '=', 'raw_material_outs.id')
                ->where('raw_material_out_items.raw_material_id', $rm->id);

            if ($stockDateFrom) {
                $purchQuery->whereDate('raw_material_purchases.purchase_date', '>=', $stockDateFrom);
                $outQuery->whereDate('raw_material_outs.out_date', '>=', $stockDateFrom);
            }
            if ($stockDateTo) {
                $purchQuery->whereDate('raw_material_purchases.purchase_date', '<=', $stockDateTo);
                $outQuery->whereDate('raw_material_outs.out_date', '<=', $stockDateTo);
            }

            $purchasedQty = (float) $purchQuery->sum('raw_material_purchase_items.qty');
            $outQty = (float) $outQuery->sum('raw_material_out_items.qty');
            $stockQty = (float) $rm->stock_qty;

            if ($stockDateFrom || $stockDateTo) {
                $allTimePurch = (float) DB::table('raw_material_purchase_items')->where('raw_material_id', $rm->id)->sum('qty');
                $allTimeOut = (float) DB::table('raw_material_out_items')->where('raw_material_id', $rm->id)->sum('qty');
                $openingStockAll = $stockQty - $allTimePurch + $allTimeOut;

                $purchBefore = $stockDateFrom ? (float) DB::table('raw_material_purchase_items')
                    ->join('raw_material_purchases', 'raw_material_purchase_items.raw_material_purchase_id', '=', 'raw_material_purchases.id')
                    ->where('raw_material_purchase_items.raw_material_id', $rm->id)
                    ->whereDate('raw_material_purchases.purchase_date', '<', $stockDateFrom)
                    ->sum('raw_material_purchase_items.qty') : 0;

                $outBefore = $stockDateFrom ? (float) DB::table('raw_material_out_items')
                    ->join('raw_material_outs', 'raw_material_out_items.raw_material_out_id', '=', 'raw_material_outs.id')
                    ->where('raw_material_out_items.raw_material_id', $rm->id)
                    ->whereDate('raw_material_outs.out_date', '<', $stockDateFrom)
                    ->sum('raw_material_out_items.qty') : 0;

                $initialStock = $openingStockAll + $purchBefore - $outBefore;
                $closingStockPeriod = $initialStock + $purchasedQty - $outQty;
            } else {
                $initialStock = $stockQty - $purchasedQty + $outQty;
                $closingStockPeriod = $stockQty;
            }

            $unitPrice = (float) $rm->unit_price;
            $stockValue = $closingStockPeriod * $unitPrice;

            return (object)[
                'id' => $rm->id,
                'item_code' => $rm->item_code ?? ('RM-' . str_pad($rm->id, 4, '0', STR_PAD_LEFT)),
                'name' => $rm->name,
                'unit' => $rm->unit,
                'initial_stock' => $initialStock,
                'purchased_qty' => $purchasedQty,
                'out_qty' => $outQty,
                'stock_qty' => $closingStockPeriod,
                'unit_price' => $unitPrice,
                'stock_value' => $stockValue,
                'alert_qty' => $rm->alert_qty,
            ];
        });

        return view('admin_panel.raw_material.index', compact(
            'activeTab',
            'totalMaterials',
            'alertMaterials',
            'totalPurchaseAmount',
            'totalVendors',
            'totalPayableBalance',
            'totalOutsCount',
            'materials',
            'vendors',
            'purchases',
            'outs',
            'ledgers',
            'selectedVendorId',
            'selectedVendor',
            'ledgerDateFrom',
            'ledgerDateTo',
            'stockReportData',
            'stockDateFrom',
            'stockDateTo',
            'stockMaterialId'
        ));
    }

    public function printStockReport(Request $request)
    {
        $stockDateFrom = $request->get('date_from');
        $stockDateTo = $request->get('date_to');
        $materialIds = $request->get('ids');

        $query = RawMaterial::orderBy('name', 'asc');
        if ($materialIds) {
            $idsArray = is_array($materialIds) ? $materialIds : explode(',', $materialIds);
            $query->whereIn('id', array_filter($idsArray));
        }
        $materials = $query->get();

        $reportData = $materials->map(function($rm) use ($stockDateFrom, $stockDateTo) {
            $purchQuery = DB::table('raw_material_purchase_items')
                ->join('raw_material_purchases', 'raw_material_purchase_items.raw_material_purchase_id', '=', 'raw_material_purchases.id')
                ->where('raw_material_purchase_items.raw_material_id', $rm->id);

            $outQuery = DB::table('raw_material_out_items')
                ->join('raw_material_outs', 'raw_material_out_items.raw_material_out_id', '=', 'raw_material_outs.id')
                ->where('raw_material_out_items.raw_material_id', $rm->id);

            if ($stockDateFrom) {
                $purchQuery->whereDate('raw_material_purchases.purchase_date', '>=', $stockDateFrom);
                $outQuery->whereDate('raw_material_outs.out_date', '>=', $stockDateFrom);
            }
            if ($stockDateTo) {
                $purchQuery->whereDate('raw_material_purchases.purchase_date', '<=', $stockDateTo);
                $outQuery->whereDate('raw_material_outs.out_date', '<=', $stockDateTo);
            }

            $purchasedQty = (float) $purchQuery->sum('raw_material_purchase_items.qty');
            $outQty = (float) $outQuery->sum('raw_material_out_items.qty');
            $stockQty = (float) $rm->stock_qty;

            if ($stockDateFrom || $stockDateTo) {
                $allTimePurch = (float) DB::table('raw_material_purchase_items')->where('raw_material_id', $rm->id)->sum('qty');
                $allTimeOut = (float) DB::table('raw_material_out_items')->where('raw_material_id', $rm->id)->sum('qty');
                $openingStockAll = $stockQty - $allTimePurch + $allTimeOut;

                $purchBefore = $stockDateFrom ? (float) DB::table('raw_material_purchase_items')
                    ->join('raw_material_purchases', 'raw_material_purchase_items.raw_material_purchase_id', '=', 'raw_material_purchases.id')
                    ->where('raw_material_purchase_items.raw_material_id', $rm->id)
                    ->whereDate('raw_material_purchases.purchase_date', '<', $stockDateFrom)
                    ->sum('raw_material_purchase_items.qty') : 0;

                $outBefore = $stockDateFrom ? (float) DB::table('raw_material_out_items')
                    ->join('raw_material_outs', 'raw_material_out_items.raw_material_out_id', '=', 'raw_material_outs.id')
                    ->where('raw_material_out_items.raw_material_id', $rm->id)
                    ->whereDate('raw_material_outs.out_date', '<', $stockDateFrom)
                    ->sum('raw_material_out_items.qty') : 0;

                $initialStock = $openingStockAll + $purchBefore - $outBefore;
                $closingStockPeriod = $initialStock + $purchasedQty - $outQty;
            } else {
                $initialStock = $stockQty - $purchasedQty + $outQty;
                $closingStockPeriod = $stockQty;
            }

            $unitPrice = (float) $rm->unit_price;
            $stockValue = $closingStockPeriod * $unitPrice;

            return (object)[
                'id' => $rm->id,
                'item_code' => $rm->item_code ?? ('RM-' . str_pad($rm->id, 4, '0', STR_PAD_LEFT)),
                'name' => $rm->name,
                'unit' => $rm->unit,
                'initial_stock' => $initialStock,
                'purchased_qty' => $purchasedQty,
                'out_qty' => $outQty,
                'stock_qty' => $closingStockPeriod,
                'unit_price' => $unitPrice,
                'stock_value' => $stockValue,
            ];
        });

        $totalInitial = $reportData->sum('initial_stock');
        $totalPurchased = $reportData->sum('purchased_qty');
        $totalOut = $reportData->sum('out_qty');
        $totalStock = $reportData->sum('stock_qty');
        $totalValue = $reportData->sum('stock_value');

        return view('admin_panel.raw_material.stock_report_print', compact(
            'reportData', 'totalInitial', 'totalPurchased', 'totalOut', 'totalStock', 'totalValue', 'stockDateFrom', 'stockDateTo'
        ));
    }

    public function storeMaterial(Request $request)
    {
        // Single Edit Mode
        if ($request->filled('id')) {
            $name = $request->input('single_name') ?: $request->input('name');
            $unit = $request->input('single_unit') ?: $request->input('unit');
            $stock = $request->input('single_stock_qty') !== null ? $request->input('single_stock_qty') : $request->input('stock_qty');
            $price = $request->input('single_unit_price') !== null ? $request->input('single_unit_price') : $request->input('unit_price');
            $alert = $request->input('single_alert_qty') !== null ? $request->input('single_alert_qty') : $request->input('alert_qty');
            $note = $request->input('single_note') ?: $request->input('note');

            if (empty($name)) {
                return back()->with('error', 'Material name is required.');
            }

            $material = RawMaterial::findOrFail($request->id);
            $material->update([
                'name'        => trim($name),
                'unit'        => trim($unit) ?: 'KG',
                'stock_qty'   => floatval($stock ?? 0),
                'unit_price'  => floatval($price ?? 0),
                'alert_qty'   => floatval($alert ?? 10),
                'note'        => !empty($note) ? trim($note) : null,
            ]);

            if (empty($material->item_code)) {
                $material->item_code = 'RM-' . str_pad($material->id, 4, '0', STR_PAD_LEFT);
                $material->save();
            }

            return redirect()->route('raw_materials.index', ['tab' => 'materials'])->with('success', 'Raw material updated successfully!');
        }

        // Bulk / Multi-row Create Mode
        $names = $request->input('name');
        if (is_array($names)) {
            $units  = $request->input('unit', []);
            $stocks = $request->input('stock_qty', []);
            $prices = $request->input('unit_price', []);
            $alerts = $request->input('alert_qty', []);
            $notes  = $request->input('note', []);

            $createdCount = 0;
            DB::beginTransaction();
            try {
                foreach ($names as $i => $rawName) {
                    $name = trim((string)$rawName);
                    if ($name === '') {
                        continue;
                    }

                    $unit  = trim((string)($units[$i] ?? 'KG')) ?: 'KG';
                    $stock = floatval($stocks[$i] ?? 0);
                    $price = floatval($prices[$i] ?? 0);
                    $alert = isset($alerts[$i]) && is_numeric($alerts[$i]) ? floatval($alerts[$i]) : 10;
                    $note  = !empty($notes[$i]) ? trim((string)$notes[$i]) : null;

                    $mat = RawMaterial::create([
                        'name'       => $name,
                        'unit'       => $unit,
                        'stock_qty'  => $stock,
                        'unit_price' => $price,
                        'alert_qty'  => $alert,
                        'note'       => $note,
                    ]);

                    $mat->item_code = 'RM-' . str_pad($mat->id, 4, '0', STR_PAD_LEFT);
                    $mat->save();
                    $createdCount++;
                }

                if ($createdCount === 0) {
                    DB::rollBack();
                    return back()->with('error', 'Please enter at least one valid raw material name.');
                }

                DB::commit();
                $msg = $createdCount > 1 
                    ? "{$createdCount} raw materials added successfully!" 
                    : "Raw material added successfully!";
                return redirect()->route('raw_materials.index', ['tab' => 'materials'])->with('success', $msg);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error creating raw materials: ' . $e->getMessage());
            }
        }

        // Fallback single create (if non-array name is submitted)
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'unit'        => 'required|string|max:50',
            'unit_price'  => 'nullable|numeric|min:0',
            'stock_qty'   => 'nullable|numeric|min:0',
            'alert_qty'   => 'nullable|numeric|min:0',
            'note'        => 'nullable|string',
        ]);

        $material = RawMaterial::create([
            'name'        => $validated['name'],
            'unit'        => $validated['unit'],
            'unit_price'  => $validated['unit_price'] ?? 0,
            'stock_qty'   => $validated['stock_qty'] ?? 0,
            'alert_qty'   => $validated['alert_qty'] ?? 10,
            'note'        => $validated['note'] ?? null,
        ]);
        $material->item_code = 'RM-' . str_pad($material->id, 4, '0', STR_PAD_LEFT);
        $material->save();

        return redirect()->route('raw_materials.index', ['tab' => 'materials'])->with('success', 'Raw material created successfully!');
    }

    public function deleteMaterial($id)
    {
        $material = RawMaterial::findOrFail($id);
        $material->delete();

        return redirect()->route('raw_materials.index', ['tab' => 'materials'])->with('success', 'Raw material deleted successfully!');
    }

    public function storeVendor(Request $request)
    {
        $validated = $request->validate([
            'id'              => 'nullable|exists:raw_material_vendors,id',
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:100',
            'address'         => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
        ]);

        $openingBal = (float)($request->opening_balance ?? 0);

        if ($request->id) {
            $vendor = RawMaterialVendor::findOrFail($request->id);
            $diff = $openingBal - (float)$vendor->opening_balance;
            
            $vendor->update([
                'name'            => $validated['name'],
                'phone'           => $validated['phone'] ?? null,
                'email'           => $validated['email'] ?? null,
                'address'         => $validated['address'] ?? null,
                'opening_balance' => $openingBal,
                'closing_balance' => $vendor->closing_balance + $diff,
            ]);

            // Update initial opening balance ledger entry if exists
            $firstLedger = RawMaterialVendorLedger::where('vendor_id', $vendor->id)->where('type', 'opening_balance')->first();
            if ($firstLedger) {
                $firstLedger->update([
                    'credit' => $openingBal,
                    'running_balance' => $openingBal
                ]);
            }

            $message = 'Raw material vendor updated successfully!';
        } else {
            $vendor = RawMaterialVendor::create([
                'name'            => $validated['name'],
                'phone'           => $validated['phone'] ?? null,
                'email'           => $validated['email'] ?? null,
                'address'         => $validated['address'] ?? null,
                'opening_balance' => $openingBal,
                'closing_balance' => $openingBal,
            ]);

            if ($openingBal != 0) {
                RawMaterialVendorLedger::create([
                    'vendor_id'       => $vendor->id,
                    'date'            => now()->format('Y-m-d'),
                    'description'     => 'Opening Balance',
                    'reference_no'    => 'OB-' . $vendor->id,
                    'type'            => 'opening_balance',
                    'credit'          => $openingBal > 0 ? $openingBal : 0,
                    'debit'           => $openingBal < 0 ? abs($openingBal) : 0,
                    'running_balance' => $openingBal,
                    'created_by'      => Auth::id(),
                ]);
            }

            $message = 'Raw material vendor created successfully!';
        }

        return redirect()->route('raw_materials.index', ['tab' => 'vendors'])->with('success', $message);
    }

    public function deleteVendor($id)
    {
        $vendor = RawMaterialVendor::findOrFail($id);
        $vendor->delete();

        return redirect()->route('raw_materials.index', ['tab' => 'vendors'])->with('success', 'Raw material vendor deleted successfully!');
    }

    public function storePurchase(Request $request)
    {
        $request->validate([
            'vendor_id'      => 'required|exists:raw_material_vendors,id',
            'purchase_date'  => 'required|date',
            'raw_material_id' => 'required|array|min:1',
            'raw_material_id.*' => 'required|exists:raw_materials,id',
            'qty'            => 'required|array|min:1',
            'qty.*'          => 'required|numeric|gt:0',
            'unit_price'     => 'required|array|min:1',
            'unit_price.*'   => 'required|numeric|gte:0',
            'discount'       => 'nullable|numeric|min:0',
            'extra_cost'     => 'nullable|numeric|min:0',
            'paid_amount'    => 'nullable|numeric|min:0',
            'note'           => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $purchaseNo = RawMaterialPurchase::generatePurchaseNo();
            $discount = (float)($request->discount ?? 0);
            $extraCost = (float)($request->extra_cost ?? 0);
            $paidAmount = (float)($request->paid_amount ?? 0);

            $purchase = RawMaterialPurchase::create([
                'purchase_no'    => $purchaseNo,
                'vendor_id'      => $request->vendor_id,
                'purchase_date'  => $request->purchase_date,
                'subtotal'       => 0,
                'discount'       => $discount,
                'extra_cost'     => $extraCost,
                'net_amount'     => 0,
                'paid_amount'    => $paidAmount,
                'due_amount'     => 0,
                'payment_status' => 'unpaid',
                'note'           => $request->note,
                'created_by'     => Auth::id(),
            ]);

            $subtotal = 0;

            foreach ($request->raw_material_id as $index => $matId) {
                $qty = (float)$request->qty[$index];
                $price = (float)$request->unit_price[$index];
                $lineTotal = $qty * $price;
                $subtotal += $lineTotal;

                $material = RawMaterial::findOrFail($matId);

                RawMaterialPurchaseItem::create([
                    'raw_material_purchase_id' => $purchase->id,
                    'raw_material_id'          => $matId,
                    'unit'                     => $material->unit,
                    'qty'                      => $qty,
                    'unit_price'               => $price,
                    'line_total'               => $lineTotal,
                ]);

                // Update stock of raw material
                $material->stock_qty += $qty;
                $material->unit_price = $price; // update latest purchase price
                $material->save();
            }

            $netAmount = max(0, ($subtotal - $discount) + $extraCost);
            $dueAmount = max(0, $netAmount - $paidAmount);
            $paymentStatus = $dueAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');

            $purchase->update([
                'subtotal'       => $subtotal,
                'net_amount'     => $netAmount,
                'due_amount'     => $dueAmount,
                'payment_status' => $paymentStatus,
            ]);

            // Update Vendor Ledger & Closing Balance
            $vendor = RawMaterialVendor::findOrFail($request->vendor_id);
            $previousBalance = (float)$vendor->closing_balance;

            // 1. Credit Bill Amount
            $runningBalance = $previousBalance + $netAmount;
            RawMaterialVendorLedger::create([
                'vendor_id'       => $vendor->id,
                'date'            => $request->purchase_date,
                'description'     => 'Raw Material Purchase #' . $purchaseNo,
                'reference_no'    => $purchaseNo,
                'type'            => 'purchase',
                'credit'          => $netAmount,
                'debit'           => 0,
                'running_balance' => $runningBalance,
                'created_by'      => Auth::id(),
            ]);

            // 2. Debit Paid Amount if any
            if ($paidAmount > 0) {
                $runningBalance -= $paidAmount;
                RawMaterialVendorLedger::create([
                    'vendor_id'       => $vendor->id,
                    'date'            => $request->purchase_date,
                    'description'     => 'Paid for Purchase #' . $purchaseNo,
                    'reference_no'    => $purchaseNo,
                    'type'            => 'payment',
                    'credit'          => 0,
                    'debit'           => $paidAmount,
                    'running_balance' => $runningBalance,
                    'created_by'      => Auth::id(),
                ]);
            }

            $vendor->closing_balance = $runningBalance;
            $vendor->save();

            // Sync vendor purchase invoice payment statuses
            self::syncVendorPurchasesPaymentStatus($vendor->id);

            DB::commit();
            return redirect()->route('raw_materials.index', ['tab' => 'purchases'])->with('success', 'Raw material purchase saved successfully! Invoice #: ' . $purchaseNo);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error saving purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function deletePurchase($id)
    {
        try {
            DB::beginTransaction();

            $purchase = RawMaterialPurchase::with('items')->findOrFail($id);
            $vendorId = $purchase->vendor_id;

            // Revert Raw Material Stock
            foreach ($purchase->items as $item) {
                $material = RawMaterial::find($item->raw_material_id);
                if ($material) {
                    $material->stock_qty = max(0, $material->stock_qty - $item->qty);
                    $material->save();
                }
            }

            // Revert Vendor Closing Balance
            if ($vendorId) {
                $vendor = RawMaterialVendor::find($vendorId);
                if ($vendor) {
                    $netBalanceChange = $purchase->net_amount - $purchase->paid_amount;
                    $vendor->closing_balance -= $netBalanceChange;
                    $vendor->save();
                }

                // Delete associated ledgers
                RawMaterialVendorLedger::where('reference_no', $purchase->purchase_no)->delete();
            }

            $purchase->items()->delete();
            $purchase->delete();

            if ($vendorId) {
                self::syncVendorPurchasesPaymentStatus($vendorId);
            }

            DB::commit();
            return redirect()->route('raw_materials.index', ['tab' => 'purchases'])->with('success', 'Purchase deleted and stock/ledger reverted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error deleting purchase: ' . $e->getMessage());
        }
    }

    public function storeVendorPayment(Request $request)
    {
        $validated = $request->validate([
            'vendor_id'      => 'required|exists:raw_material_vendors,id',
            'date'           => 'required|date',
            'amount'         => 'required|numeric|gt:0',
            'payment_method' => 'nullable|string',
            'note'           => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $vendor = RawMaterialVendor::findOrFail($validated['vendor_id']);
            $amount = (float)$validated['amount'];
            $newBalance = $vendor->closing_balance - $amount;

            $refNo = 'PAY-' . date('Ymd-His');
            $method = $validated['payment_method'] ?? 'Cash';
            $note = $validated['note'] ? (' (' . $validated['note'] . ')') : '';

            RawMaterialVendorLedger::create([
                'vendor_id'       => $vendor->id,
                'date'            => $validated['date'],
                'description'     => 'Vendor Payment via ' . $method . $note,
                'reference_no'    => $refNo,
                'type'            => 'payment',
                'credit'          => 0,
                'debit'           => $amount,
                'running_balance' => $newBalance,
                'created_by'      => Auth::id(),
            ]);

            $vendor->closing_balance = $newBalance;
            $vendor->save();

            // Sync vendor purchase invoice payment statuses
            self::syncVendorPurchasesPaymentStatus($vendor->id);

            DB::commit();
            return redirect()->route('raw_materials.index', ['tab' => 'ledger', 'ledger_vendor_id' => $vendor->id])
                ->with('success', 'Payment of Rs ' . number_format($amount, 2) . ' recorded successfully for vendor ' . $vendor->name);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error recording payment: ' . $e->getMessage());
        }
    }

    public function purchaseInvoice($id)
    {
        $purchase = RawMaterialPurchase::with(['vendor', 'items.rawMaterial'])->findOrFail($id);
        if ($purchase->vendor_id) {
            self::syncVendorPurchasesPaymentStatus($purchase->vendor_id);
            $purchase->refresh();
        }
        return view('admin_panel.raw_material.invoice', compact('purchase'));
    }

    public static function syncVendorPurchasesPaymentStatus($vendorId)
    {
        if (!$vendorId) return;

        $totalPayments = (float) DB::table('raw_material_vendor_ledgers')
            ->where('vendor_id', $vendorId)
            ->where('type', 'payment')
            ->sum('debit');

        $purchases = RawMaterialPurchase::where('vendor_id', $vendorId)
            ->orderBy('purchase_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $remainingPool = $totalPayments;

        foreach ($purchases as $p) {
            $net = (float) $p->net_amount;
            $allocatedPaid = min($remainingPool, $net);
            $due = max(0, $net - $allocatedPaid);
            $status = $due <= 0 ? 'paid' : ($allocatedPaid > 0 ? 'partial' : 'unpaid');

            $p->update([
                'paid_amount'    => $allocatedPaid,
                'due_amount'     => $due,
                'payment_status' => $status,
            ]);

            $remainingPool -= $allocatedPaid;
        }
    }

    public function printVendorLedger(Request $request)
    {
        $vendorId = $request->get('ledger_vendor_id');
        $dateFrom = $request->get('ledger_date_from');
        $dateTo = $request->get('ledger_date_to');

        $selectedVendor = $vendorId ? RawMaterialVendor::find($vendorId) : null;

        $ledgerQuery = RawMaterialVendorLedger::with('vendor')->orderBy('date', 'asc')->orderBy('id', 'asc');
        if ($vendorId) {
            $ledgerQuery->where('vendor_id', $vendorId);
        }
        if ($dateFrom) {
            $ledgerQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $ledgerQuery->whereDate('date', '<=', $dateTo);
        }

        $ledgers = $ledgerQuery->get();

        return view('admin_panel.raw_material.vendor_ledger_print', compact('selectedVendor', 'ledgers', 'dateFrom', 'dateTo'));
    }

    public function storeOut(Request $request)
    {
        $request->validate([
            'out_date'        => 'required|date',
            'location'        => 'required|string|max:255',
            'taken_by'        => 'required|string|max:255',
            'raw_material_id' => 'required|array|min:1',
            'raw_material_id.*' => 'required|exists:raw_materials,id',
            'qty'            => 'required|array|min:1',
            'qty.*'          => 'required|numeric|gt:0',
            'item_note'      => 'nullable|array',
            'notes'          => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $issueNo = RawMaterialOut::generateIssueNo();

            $out = RawMaterialOut::create([
                'issue_no'   => $issueNo,
                'out_date'   => $request->out_date,
                'location'   => $request->location,
                'taken_by'   => $request->taken_by,
                'notes'      => $request->notes,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->raw_material_id as $index => $matId) {
                $qty = (float)$request->qty[$index];
                $itemNote = $request->item_note[$index] ?? null;

                $material = RawMaterial::findOrFail($matId);

                RawMaterialOutItem::create([
                    'raw_material_out_id' => $out->id,
                    'raw_material_id'     => $matId,
                    'unit'                => $material->unit,
                    'qty'                 => $qty,
                    'item_note'           => $itemNote,
                ]);

                // Deduct stock from raw_materials
                $material->stock_qty = max(0, $material->stock_qty - $qty);
                $material->save();
            }

            DB::commit();
            return redirect()->route('raw_materials.index', ['tab' => 'out'])->with('success', 'Raw material issued out successfully! DC #: ' . $issueNo);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error issuing raw material out: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteOut($id)
    {
        try {
            DB::beginTransaction();

            $out = RawMaterialOut::with('items')->findOrFail($id);

            // Revert Raw Material Stock
            foreach ($out->items as $item) {
                $material = RawMaterial::find($item->raw_material_id);
                if ($material) {
                    $material->stock_qty += $item->qty;
                    $material->save();
                }
            }

            $out->items()->delete();
            $out->delete();

            DB::commit();
            return redirect()->route('raw_materials.index', ['tab' => 'out'])->with('success', 'Material Out DC deleted and stock reverted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error deleting Material Out DC: ' . $e->getMessage());
        }
    }

    public function outChallan($id)
    {
        $out = RawMaterialOut::with(['items.rawMaterial', 'creator'])->findOrFail($id);
        return view('admin_panel.raw_material.out_challan', compact('out'));
    }
}
