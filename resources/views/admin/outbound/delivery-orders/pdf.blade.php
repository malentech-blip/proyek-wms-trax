<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Order - {{ $deliveryOrder->delivery_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-info {
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.4;
        }

        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Info Boxes */
        .info-section {
            margin-bottom: 20px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 5px;
        }

        .info-box {
            border: 1px solid #e5e7eb;
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 4px;
        }

        .info-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 11px;
            color: #111827;
            font-weight: 600;
        }

        .info-value.large {
            font-size: 14px;
            color: #1e40af;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 3px;
        }

        .status-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-in-delivery {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* Table */
        .table-section {
            margin-top: 25px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background-color: #eff6ff;
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            color: #1e40af;
            border: 1px solid #dbeafe;
            text-transform: uppercase;
        }

        td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:hover {
            background-color: #f3f4f6;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-mono {
            font-family: 'Courier New', monospace;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Summary */
        .summary-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .summary-label {
            font-size: 11px;
            color: #374151;
        }

        .summary-value {
            font-size: 11px;
            font-weight: bold;
            color: #1e40af;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-grid {
            display: table;
            width: 100%;
        }

        .signature-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }

        .signature-box {
            border: 1px solid #e5e7eb;
            padding: 15px;
            min-height: 100px;
            background-color: #ffffff;
        }

        .signature-label {
            font-size: 10px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 60px;
        }

        .signature-name {
            border-top: 1px solid #374151;
            padding-top: 5px;
            font-size: 10px;
            color: #6b7280;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
        }

        /* Notes */
        .notes-section {
            margin-top: 20px;
            padding: 12px;
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 4px;
        }

        .notes-title {
            font-size: 10px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }

        .notes-content {
            font-size: 10px;
            color: #78350f;
            font-style: italic;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">PT. NAMA PERUSAHAAN ANDA</div>
                <div class="company-details">
                    Jl. Alamat Perusahaan No. 123, Kota, Provinsi 12345<br>
                    Telp: (021) 1234-5678 | Email: info@perusahaan.com | Website: www.perusahaan.com
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="document-title">DELIVERY ORDER</div>

        <!-- DO Information -->
        <div class="info-section">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-col">
                        <div class="info-box">
                            <div class="info-label">DO Number</div>
                            <div class="info-value large">{{ $deliveryOrder->delivered_no }}</div>
                            <div
                                class="status-badge {{ $deliveryOrder->status === 'Delivered' ? 'status-delivered' : 'status-in-delivery' }}">
                                {{ $deliveryOrder->status }}
                            </div>
                        </div>
                    </div>
                    <div class="info-col">
                        <div class="info-box">
                            <div class="info-label">Tanggal Cetak</div>
                            <div class="info-value">{{ $generatedAt }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-col">
                        <div class="info-box">
                            <div class="info-label">Sales Order</div>
                            <div class="info-value">{{ $salesOrder->so_number }}</div>
                        </div>
                    </div>
                    <div class="info-col">
                        <div class="info-box">
                            <div class="info-label">Tanggal Pengiriman</div>
                            <div class="info-value">
                                {{ $deliveryOrder->delivery_date ? \Carbon\Carbon::parse($deliveryOrder->delivery_date)->format('d M Y, H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer & Delivery Information -->
        <div class="info-section">
            <div class="section-title">Informasi Customer</div>
            <div class="info-box">
                <table style="border: none; width: 100%;">
                    <tr style="border: none;">
                        <td style="border: none; width: 20%; padding: 3px 8px;">
                            <span class="info-label">Customer ID</span>
                        </td>
                        <td style="border: none; width: 30%; padding: 3px 8px;">
                            <span class="info-value">{{ $customer['id'] ?? 'N/A' }}</span>
                        </td>
                        <td style="border: none; width: 20%; padding: 3px 8px;">
                            <span class="info-label">Nama Customer</span>
                        </td>
                        <td style="border: none; width: 30%; padding: 3px 8px;">
                            <span class="info-value">{{ $customer['name'] ?? 'N/A' }}</span>
                        </td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 3px 8px;">
                            <span class="info-label">Alamat</span>
                        </td>
                        <td colspan="3" style="border: none; padding: 3px 8px;">
                            <span class="info-value">{{ $customer['shipStreet'] ?? 'N/A' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="info-section">
            <div class="section-title">Informasi Pengiriman</div>
            <div class="info-box">
                <table style="border: none; width: 100%;">
                    <tr style="border: none;">
                        <td style="border: none; width: 20%; padding: 3px 8px;">
                            <span class="info-label">Driver</span>
                        </td>
                        <td style="border: none; width: 30%; padding: 3px 8px;">
                            <span class="info-value">{{ $deliveryOrder->driver_name }}</span>
                        </td>
                        <td style="border: none; width: 20%; padding: 3px 8px;">
                            <span class="info-label">No. Kendaraan</span>
                        </td>
                        <td style="border: none; width: 30%; padding: 3px 8px;">
                            <span class="info-value">{{ $deliveryOrder->vehicle_number ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 3px 8px;">
                            <span class="info-label">No. Telepon</span>
                        </td>
                        <td style="border: none; padding: 3px 8px;">
                            <span class="info-value">{{ $deliveryOrder->phone_number ?? '-' }}</span>
                        </td>
                        @if ($deliveryOrder->status === 'Delivered')
                            <td style="border: none; padding: 3px 8px;">
                                <span class="info-label">Diterima Pada</span>
                            </td>
                            <td style="border: none; padding: 3px 8px;">
                                <span class="info-value">
                                    {{ $deliveryOrder->delivered_at ? \Carbon\Carbon::parse($deliveryOrder->delivered_at)->format('d M Y, H:i') : '-' }}
                                </span>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-section">
            <div class="section-title">Daftar Item</div>
            <table>
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 15%;">Kode Produk</th>
                        <th style="width: 30%;">Nama Produk</th>
                        <th style="width: 20%;">Label/QR Code</th>
                        <th class="text-center" style="width: 15%;">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQty = 0; @endphp
                    @foreach ($items as $index => $item)
                        @php $totalQty += $item->quantity; @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="font-mono font-bold">{{ $item->finished_good->item->item_code ?? 'N/A' }}</td>
                            <td>{{ $item->finished_good->item->item_name ?? 'N/A' }}</td>                                
                            <td class="font-mono font-bold">
                              {!! DNS1D::getBarcodeHTML($item->finished_good->production_item_label->barcode, 'C128', 1.5, 40) !!}
                            </td>
                            <td class="text-center font-bold">{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #dbeafe;">
                        <td colspan="4" class="text-right font-bold" style="padding: 10px;">
                            <strong>TOTAL ITEMS:</strong>
                        </td>
                        <td class="text-center font-bold" style="padding: 10px; font-size: 12px;">
                            <strong>{{ $totalQty }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes Section -->
        @if ($deliveryOrder->notes)
            <div class="notes-section">
                <div class="notes-title">Catatan:</div>
                <div class="notes-content">{{ $deliveryOrder->notes }}</div>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-grid">
                <div class="signature-col">
                    <div class="signature-box">
                        <div class="signature-label">Disiapkan Oleh,</div>
                        <div class="signature-name">(______________)</div>
                    </div>
                </div>
                <div class="signature-col">
                    <div class="signature-box">
                        <div class="signature-label">Driver,</div>
                        <div class="signature-name">{{ $deliveryOrder->driver_name }}</div>
                    </div>
                </div>
                <div class="signature-col">
                    <div class="signature-box">
                        <div class="signature-label">Diterima Oleh,</div>
                        <div class="signature-name">(______________)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dicetak secara otomatis oleh sistem pada {{ $generatedAt }}</p>
            <p>{{ $deliveryOrder->delivery_no }} | Halaman 1 dari 1</p>
        </div>
    </div>
</body>

</html>
