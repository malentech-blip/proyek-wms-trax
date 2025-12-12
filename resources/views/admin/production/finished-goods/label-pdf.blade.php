<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Production Label - {{ $itemLabels->first()->item_name ?? 'Item' }}</title>
    <style>
        @page {
            margin: 0;
            size: 100mm 70mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 9px;
            line-height: 1.3;
        }

        .label {
            width: 100mm;
            height: 70mm;
            padding: 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-after: always;
            border: 1px solid #000;
        }

        .label:last-child {
            page-break-after: auto;
        }

        /* Header Section */
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .company-name {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .label-type {
            font-size: 8px;
            color: #666;
            font-weight: bold;
        }

        /* Main Content */
        .content {
            flex: 1;
            display: flex;
            gap: 8px;
        }

        .info-section {
            flex: 1;
        }

        .item-name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 6px;
            line-height: 1.2;
            max-height: 26px;
            overflow: hidden;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-size: 8px;
            color: #666;
            padding: 2px 0;
            width: 35%;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 0;
            vertical-align: top;
        }

        .qr-section {
            width: 35mm;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Barcode Section */
        .barcode-section {
            text-align: center;
            padding-top: 4px;
            border-top: 1px solid #ddd;
        }

        .barcode-wrapper {
            margin: 4px 0;
        }

        .barcode-text {
            font-size: 7px;
            color: #666;
            margin-top: 2px;
            letter-spacing: 1px;
        }

        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 7px;
            color: #666;
            padding-top: 4px;
            border-top: 1px solid #ddd;
        }

        .batch-info {
            font-weight: bold;
            color: #000;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            background: #4CAF50;
            color: white;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        /* QR Code styling */
        .qr-code-img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    @foreach ($itemLabels as $label)
        <div class="label">
            <!-- Header -->
            <div class="header">
                <div class="company-name">WMS PRODUCTION</div>
                <div class="label-type">FINISHED GOODS LABEL</div>
            </div>

            <!-- Main Content -->
            <div class="content">
                <div class="info-section">
                    <div class="item-name">{{ Str::limit($label->item->item_name, 40) }}</div>
                    
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">Item Code:</div>
                            <div class="info-value">{{ $label->item->item_code }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Batch No:</div>
                            <div class="info-value">{{ $label->batch_no ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Location:</div>
                            <div class="info-value">{{ $label->location->name ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Rack:</div>
                            <div class="info-value">{{ $label->rack->code ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Pallet:</div>
                            <div class="info-value">{{ $label->pallet->code ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="qr-section">
                    <div style="font-size: 7px; color: #666; margin-bottom: 3px;">SCAN ME</div>
                    <div class="qr-code-img">
                        {!! QrCode::size(80)->generate($label->barcode) !!}
                    </div>
                </div>
            </div>

            <!-- Barcode Section -->
            <div class="barcode-section">
                <div class="barcode-wrapper">
                    {!! DNS1D::getBarcodeHTML($label->barcode, 'C128', 1.5, 35) !!}
                </div>
                <div class="barcode-text">{{ $label->barcode }}</div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div>
                    <span style="color: #999;">Printed:</span> {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    @endforeach
</body>

</html>
