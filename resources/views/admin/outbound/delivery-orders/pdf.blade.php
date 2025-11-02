<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Order - {{ $deliveryOrder->delivered_no }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }
        .header .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-in-delivery {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DELIVERY ORDER</h1>
        <div class="subtitle">TRAX WMS - Warehouse Management System</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">DO Number:</div>
            <div class="info-value">{{ $deliveryOrder->delivered_no }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Sales Order:</div>
            <div class="info-value">{{ $deliveryOrder->packingList?->salesOrder?->so_number ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Driver Name:</div>
            <div class="info-value">{{ $deliveryOrder->driver_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Delivery Date:</div>
            <div class="info-value">
                {{ $deliveryOrder->delivery_date ? \Carbon\Carbon::parse($deliveryOrder->delivery_date)->format('d F Y H:i') : 'N/A' }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                @if($deliveryOrder->status === 'Delivered')
                    <span class="status-badge status-delivered">{{ $deliveryOrder->status }}</span>
                @else
                    <span class="status-badge status-in-delivery">{{ $deliveryOrder->status }}</span>
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Created At:</div>
            <div class="info-value">{{ $deliveryOrder->created_at->format('d F Y H:i:s') }}</div>
        </div>
    </div>

    <div class="section-title">Delivery Information</div>
    <div class="info-section">
        <p><strong>Packing List ID:</strong> {{ $deliveryOrder->packingList?->id ?? 'N/A' }}</p>
        <p><strong>Packed By:</strong> {{ $deliveryOrder->packingList?->packed_by ?? 'N/A' }}</p>
        @if($deliveryOrder->packingList && $deliveryOrder->packingList->packed_at)
            <p><strong>Packed At:</strong> {{ \Carbon\Carbon::parse($deliveryOrder->packingList->packed_at)->format('d F Y H:i') }}</p>
        @endif
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Driver Signature
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Receiver Signature
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Generated on:</strong> {{ now()->format('d F Y H:i:s') }}</p>
        <p>This is a system-generated document from TRAX WMS.</p>
    </div>
</body>
</html>

