<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Label Barang</title>
    <style>
        @page {
            margin: 0px; 
            padding: 0px;
        }
        
        body {
            margin: 0px;
            padding: 0px;
            font-family: sans-serif;
        }

        .label-container {
            width: 100%;
            height: 100%; 
            position: relative;
            page-break-after: always;
            overflow: hidden;
        }

        .label-container:last-child {
            page-break-after: auto;
        }

        .content-wrapper {
            padding: 5px;
            display: block;
        }

        .qr-code {
            float: left;
            width: 35%;
            padding-top: 5px;
        }

        .text-info {
            float: right;
            width: 63%;
            font-size: 8px; /* Font kecil agar muat */
            line-height: 1.2;
            padding-top: 2px;
        }

        .item-name {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 2px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .meta-data {
            margin-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .location-info {
            margin-top: 2px;
            display: flex;
            align-items: center;
        }

        .rack-box {
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #000;
            padding: 1px 3px;
            display: inline-block;
        }
        
        .warehouse-name {
            font-size: 7px;
            color: #333;
            margin-bottom: 1px;
            font-style: italic;
        }
    </style>
</head>
<body>
    @foreach($itemLabels as $label)
    <div class="label-container">
        <div class="content-wrapper">
            <div class="qr-code">
                <img src="data:image/svg+xml;base64, {{ base64_encode(QrCode::format('svg')->size(80)->margin(0)->generate($label->qr_code)) }}" style="width: 100%; height: auto;">
            </div>

            <div class="text-info">
                <span class="item-name">{{ Str::limit($label->item_name, 22) }}</span>
                
                <div class="meta-data">Kode: <strong>{{ $label->item_code }}</strong></div>
                <div class="meta-data">Qty: {{ $label->quantity }}</div>

                <div class="warehouse-name">
                    {{ Str::limit($label->location->name ?? 'Gudang Umum', 25) }}
                </div>

                <div class="location-info">
                    @if($label->rack)
                        {{-- Jika disimpan di Rak --}}
                        <span class="rack-box">RAK: {{ $label->rack->code }}</span>
                    @elseif($label->pallet)
                        {{-- Jika disimpan di Pallet --}}
                        <span class="rack-box">PLT: {{ $label->pallet->code }}</span>
                    @else
                        {{-- Fallback jika keduanya kosong --}}
                        <span class="rack-box">FLOOR</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>