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
        font-size: 8px; /* Ukuran font diperkecil agar muat */
    }
    .label {
        /* Buat div label mengisi seluruh area stiker */
        width: 100%;
        height: 100%;
        padding: 5px; /* Beri sedikit padding di dalam stiker */
        box-sizing: border-box;
        page-break-after: always; /* Setiap label akan berada di halaman baru */
    }
    .label:last-child {
        page-break-after: auto; /* Hentikan page break setelah label terakhir */
    }
    .item-name {
        font-size: 9px;
        font-weight: bold;
        margin: 0 0 3px 0;
        white-space: nowrap;
        overflow: hidden;
    }
    .item-details {
        float: left;
        width: 65%;
    }
    .qr-code {
        float: right;
        width: 35%;
        text-align: right;
    }
    .info { margin: 0; line-height: 1.2; }
</style>
</head>
<body>
    @foreach ($itemLabels as $label)
        <div class="label">
            <div class="item-details">
                <h3 class="item-name">{{ Str::limit($label->item_name, 30) }}</h3>
                <p class="info">Kode: <strong>{{ $label->item_code }}</strong></p>
                <p class="info">Qty: <strong>{{ $label->quantity }}</strong></p>
                <p class="info">Lokasi: <strong>{{ $label->rack->code }}</strong></p>
            </div>
            <div class="qr-code">
                {{-- Generate QR Code dari UUID yang kita simpan --}}
                {!! QrCode::size(70)->generate($label->qr_code) !!}
            </div>
        </div>
    @endforeach
    <div class="clearfix"></div>
</body>
</html>