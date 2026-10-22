<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran {{ $order->kode_pesanan }} | {{ config('app.name', 'Menu Soto Betawi') }}</title>
    <style>
        :root { font-family: Arial, sans-serif; color: #29251f; background: #fbf8f2; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 36px 18px; }
        main { max-width: 720px; margin: 0 auto; }
        a { color: #70451f; }
        .card { margin-top: 20px; padding: 28px; background: #fff; border: 1px solid #eee8df; border-radius: 12px; box-shadow: 0 8px 24px #3527140d; }
        .status { display: inline-block; padding: 7px 11px; color: #80530e; background: #fff2d2; border-radius: 20px; font-size: .9rem; font-weight: 700; }
        .order-code { overflow-wrap: anywhere; font-family: monospace; }
        .summary { width: 100%; margin: 22px 0; border-collapse: collapse; }
        .summary th, .summary td { padding: 11px 0; border-bottom: 1px solid #eee8df; text-align: left; vertical-align: top; }
        .summary th { color: #706961; font-weight: 400; }
        .summary td { text-align: right; }
        .total td { color: #70451f; font-size: 1.2rem; font-weight: 700; }
        .instructions { margin-top: 18px; padding: 18px; background: #f8f3eb; border-radius: 9px; line-height: 1.6; }
        .warning { padding: 14px; color: #704510; background: #fff3d9; border-radius: 7px; line-height: 1.5; }
        .notice { color: #706961; font-size: .92rem; line-height: 1.6; }
        .button { display: inline-block; margin-top: 14px; padding: 11px 16px; color: #fff; background: #70451f; border-radius: 7px; font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>
<main>
    <a href="{{ route('menus.index') }}">&larr; Kembali ke menu</a>
    <section class="card">
        <p class="status">Menunggu pembayaran</p>
        <h1>Instruksi Pembayaran</h1>
        <p>Simpan kode pesanan ini untuk konfirmasi pembayaran:</p>
        <p class="order-code"><strong>{{ $order->kode_pesanan }}</strong></p>

        <table class="summary">
            <tr><th>Menu</th><td>{{ $order->nama_menu }}</td></tr>
            <tr><th>Harga per porsi</th><td>Rp{{ number_format($order->harga_satuan, 0, ',', '.') }}</td></tr>
            <tr><th>Jumlah</th><td>{{ $order->jumlah }} porsi</td></tr>
            <tr class="total"><th>Total pembayaran</th><td>Rp{{ number_format($order->total_harga, 0, ',', '.') }}</td></tr>
        </table>

        <h2>Transfer bank</h2>
        @if ($bankTransfer['bank_name'] && $bankTransfer['account_name'] && $bankTransfer['account_number'])
            <div class="instructions">
                <p>Silakan transfer sesuai total pembayaran ke rekening berikut:</p>
                <p>
                    Bank: <strong>{{ $bankTransfer['bank_name'] }}</strong><br>
                    Nama pemilik: <strong>{{ $bankTransfer['account_name'] }}</strong><br>
                    Nomor rekening: <strong>{{ $bankTransfer['account_number'] }}</strong>
                </p>
                <p>Cantumkan kode pesanan pada berita transfer, lalu hubungi penjual dengan bukti pembayaran.</p>
            </div>
        @else
            <div class="warning" role="status">
                Informasi rekening belum diatur. Jangan transfer sebelum mendapat detail rekening resmi dari penjual.
                Pemilik website dapat mengisi <code>PAYMENT_BANK_NAME</code>,
                <code>PAYMENT_ACCOUNT_NAME</code>, dan <code>PAYMENT_ACCOUNT_NUMBER</code> di file <code>.env</code>.
            </div>
        @endif

        <p class="notice">Pembayaran manual belum diverifikasi otomatis. Status pesanan akan tetap menunggu konfirmasi penjual.</p>
        <a class="button" href="{{ route('menus.index') }}">Kembali melihat menu</a>
    </section>
</main>
</body>
</html>
