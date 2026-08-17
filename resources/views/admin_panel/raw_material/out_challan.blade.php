<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Raw Material Out Gate Pass - {{ $out->issue_no }}</title>
<style>
    *, *::before, *::after { box-sizing: border-box; }
    
    html, body {
        margin: 0;
        padding: 0;
        background: #f1f5f9;
        font-family: 'Arial', sans-serif;
        font-size: 11px;
        color: #000 !important;
    }
    
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 260px;
        margin: 10px auto;
        padding: 0 4px;
    }
    
    .btn {
        display: inline-block;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-dark { background: #0f172a; color: #fff; border: none; }
    .btn-dark:hover { background: #1e293b; }
    .btn-outline { background: #fff; color: #475569; border: 1px solid #cbd5e1; }
    .btn-outline:hover { background: #f8fafc; }

    .receipt {
        width: 100%;
        max-width: 260px;
        margin: 0 auto 20px auto;
        padding: 8px 12px;
        background: #fff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .center { text-align: center; }
    .bold { font-weight: 900 !important; }
    
    .line { border-top: 1px dashed #000; margin: 4px 0; }
    .dbl-line { border-top: 1.5px solid #000; border-bottom: 1.5px solid #000; height: 3px; margin: 4px 0; }
    
    .brand { font-size: 15px; font-weight: 900; margin-bottom: 1px; text-transform: uppercase; letter-spacing: 0.3px; }
    .address { font-size: 9.5px; font-weight: 900; line-height: 1.2; }
    .title { font-size: 11.5px; font-weight: 900; margin: 5px 0; text-transform: uppercase; letter-spacing: 0.5px; }
    
    table { width: 100%; border-collapse: collapse; margin: 4px 0; table-layout: fixed; }
    th { text-align: left; font-size: 10px; font-weight: 900; border-bottom: 1.5px solid #000; padding: 3px 2px; text-transform: uppercase; }
    td { font-size: 10px; font-weight: 900; padding: 3px 2px; vertical-align: top; border-bottom: 1px dotted #000; word-wrap: break-word; }
    
    table tbody tr:last-child td { border-bottom: none; }
    
    .r { text-align: right; }
    .info-row { display: flex; justify-content: space-between; font-size: 10px; margin: 2px 0; font-weight: 900; }
    .info-row .lbl { color: #000; text-transform: uppercase; }
    .info-row .val { color: #000; text-transform: uppercase; font-weight: 900; text-align: right; }
    
    .sig-area { display: flex; justify-content: space-between; margin-top: 22px; font-size: 8.5px; font-weight: 900; padding: 0 1px; }
    .sig-box { text-align: center; width: 31%; border-top: 1px dashed #000; padding-top: 3px; text-transform: uppercase; }
    
    .item-name { font-size: 10px; font-weight: 900; line-height: 1.2; margin-bottom: 1px; word-break: break-word; }
    .item-code { font-size: 8.5px; font-weight: 900; }

    @media print {
        @page {
            size: 80mm auto;
            margin: 0;
        }
        
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            width: 80mm !important;
            height: auto !important;
            max-height: max-content !important;
            overflow: hidden !important;
        }
        
        .no-print { display: none !important; }
        
        .receipt {
            width: 72mm !important;
            max-width: 72mm !important;
            margin: 0 auto !important;
            padding: 3mm 3mm !important; /* Side padding inside thermal slip */
            border: none !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
            overflow: hidden !important;
        }

        table, tr, td, th, tbody, thead {
            page-break-inside: avoid !important;
        }
    }
</style>
</head>
<body>

<div class="action-bar no-print">
    <a href="{{ route('raw_materials.index', ['tab' => 'out']) }}" class="btn btn-outline">← Back</a>
    <button class="btn btn-dark" onclick="window.print()">🖨️ Print DC</button>
</div>

<div class="receipt">
    <div class="center">
        <div class="brand">BIN SULTAN</div>
        <div class="address" style="font-size:13px; font-weight:900; margin-bottom: 2px;">SWEETS & BAKERS</div>
        <div class="address">Latifabad No 6, Near Shadman Hall, Hyderabad</div>
        <div class="address">Ph: 022 2786661</div>
    </div>

    <div class="dbl-line" style="margin-top:6px;"></div>
    <div class="center title">RAW MATERIAL OUT (DC)</div>
    <div class="dbl-line" style="margin-bottom:6px;"></div>

    <div class="info-row"><span class="lbl">DC NO:</span><span class="val">{{ $out->issue_no }}</span></div>
    <div class="info-row"><span class="lbl">DATE:</span><span class="val">{{ \Carbon\Carbon::parse($out->out_date)->format('d-M-Y') }}</span></div>
    <div class="info-row"><span class="lbl">LOCATION / DEPT:</span><span class="val" style="font-size:11px;">{{ $out->location }}</span></div>
    <div class="info-row"><span class="lbl">TAKEN BY (PERSON):</span><span class="val" style="font-size:11px;">{{ $out->taken_by }}</span></div>
    <div class="info-row"><span class="lbl">ISSUED BY:</span><span class="val">{{ $out->creator->name ?? 'SUPER ADMIN' }}</span></div>

    <div class="line" style="margin-top:5px;"></div>

    <table>
        <thead>
            <tr>
                <th style="width:10%; text-align:left;">#</th>
                <th style="width:46%; text-align:left;">MATERIAL ITEM</th>
                <th style="width:20%; text-align:right;">QTY</th>
                <th style="width:24%; text-align:right;">TOTAL (RS)</th>
            </tr>
        </thead>
        <tbody>
            @php $printTotal = 0; @endphp
            @foreach($out->items as $index => $item)
            @php
                $p = (float)($item->unit_price ?: ($item->rawMaterial?->unit_price ?? 0));
                $line = (float)($item->line_total ?: ($item->qty * $p));
                $printTotal += $line;
            @endphp
            <tr>
                <td style="text-align:left;">{{ $index + 1 }}</td>
                <td style="text-align:left;">
                    <div class="item-name">{{ $item->rawMaterial->name ?? 'Material' }}</div>
                    <div class="item-code">[{{ $item->rawMaterial->item_code ?? 'RM-'.str_pad($item->raw_material_id, 3, '0', STR_PAD_LEFT) }}] @if($p > 0) @ Rs {{ number_format($p, 0) }} @endif</div>
                    @if($item->item_note)
                        <div style="font-size:8.5px; font-weight:900;">Note: {{ $item->item_note }}</div>
                    @endif
                </td>
                <td class="r" style="font-size:11.5px; font-weight:900; vertical-align:top; text-align:right;">
                    {{ number_format($item->qty, ($item->qty == floor($item->qty) ? 0 : 2)) }}<br><small style="font-size:9px;">{{ $item->unit }}</small>
                </td>
                <td class="r" style="font-size:11.5px; font-weight:900; vertical-align:top; text-align:right;">
                    Rs {{ number_format($line, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>
    <div class="info-row" style="font-size:11px;">
        <span>TOTAL ITEMS:</span>
        <span>{{ $out->items->count() }} Item(s)</span>
    </div>
    <div class="info-row" style="font-size:12px; font-weight:900;">
        <span>TOTAL VALUE:</span>
        <span>Rs {{ number_format($out->total_amount ?: $printTotal, 2) }}</span>
    </div>

    @if($out->notes)
    <div class="line"></div>
    <div class="bold" style="font-size:10px; line-height:1.2;">
        REMARKS: {{ $out->notes }}
    </div>
    @endif

    <div class="line" style="margin-bottom:5px;"></div>
    <div class="center bold" style="font-size:9px;">
        PRINTED: {{ \Carbon\Carbon::now()->format('d-M-Y h:i A') }}
    </div>

    <div class="sig-area">
        <div class="sig-box">ISSUED BY</div>
        <div class="sig-box">TAKEN BY</div>
        <div class="sig-box">RECEIVED BY</div>
    </div>
</div>

<script>
    window.onload = function() {
        if (!window.matchMedia('print').matches) {
            window.print();
        }
    };
</script>
</body>
</html>
