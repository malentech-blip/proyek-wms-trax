<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Item Labels</title>
    <style>
        @page {
            /* Hapus semua margin dari halaman/stiker */
            margin: 0;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
            /* Ukuran font diperkecil agar muat */
        }

        .label {
            /* Buat div label mengisi seluruh area stiker */
            width: 100%;
            height: 100%;
            padding: 5px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            /* Beri sedikit padding di dalam stiker */
            box-sizing: border-box;
            page-break-after: always;
            /* Setiap label akan berada di halaman baru */
        }

        .label:last-child {
            page-break-after: auto;
            /* Hentikan page break setelah label terakhir */
        }

        .item-name {
            font-size: 9px;
            font-weight: bold;
            margin: 0 0 3px 0;
            white-space: nowrap;
            overflow: hidden;
        }

        .item-details {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 10px;
        }

        .qr-code {
            float: right;
            width: 35%;
            text-align: right;
        }

        .info {
            margin: 0;
            line-height: 1.2;
        }
    </style>
</head>

<body>
    <div class="label-box">
        @foreach ($itemLabels as $label)
            <div class="label">
                <div class="item-details">
                    <h3 class="item-name">{{ Str::limit($label->item_name, 30) }}</h3>
                    <p class="info">Kode: <strong>{{ $label->item_code }}</strong></p>
                    <p class="info">Qty: <strong>{{ $label->quantity }}</strong></p>
                    <p class="info">Lokasi: <strong>{{ $label->rack->code }}</strong></p>
                </div>
                <div>
                    {!! DNS1D::getBarcodeHTML($label->barcode, 'C128', 2, 50) !!}
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
