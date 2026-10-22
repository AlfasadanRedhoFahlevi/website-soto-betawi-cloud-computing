<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesan {{ $menu->nama_menu }} | {{ config('app.name', 'Menu Soto Betawi') }}</title>
    <style>
        :root { font-family: Arial, sans-serif; color: #29251f; background: #fbf8f2; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 36px 18px; }
        main { max-width: 720px; margin: 0 auto; }
        a { color: #70451f; }
        .card { margin-top: 20px; padding: 28px; background: #fff; border: 1px solid #eee8df; border-radius: 12px; box-shadow: 0 8px 24px #3527140d; }
        .menu-price { color: #70451f; font-size: 1.2rem; font-weight: 700; }
        label { display: block; margin: 18px 0 7px; font-weight: 700; }
        input, textarea { width: 100%; padding: 11px 12px; border: 1px solid #d8d1c7; border-radius: 7px; font: inherit; }
        textarea { min-height: 90px; resize: vertical; }
        .errors { padding: 12px 16px; color: #8b1d16; background: #fff0ee; border-radius: 7px; }
        button { margin-top: 22px; padding: 12px 18px; color: #fff; background: #70451f; border: 0; border-radius: 7px; font: inherit; font-weight: 700; cursor: pointer; }
        .hint { color: #706961; font-size: .9rem; }
    </style>
</head>
<body>
<main>
    <a href="{{ route('menus.index') }}">&larr; Kembali ke menu</a>
    <section class="card">
        <p>{{ $menu->kategori }}</p>
        <h1>Pesan {{ $menu->nama_menu }}</h1>
        @if ($menu->deskripsi)
            <p>{{ $menu->deskripsi }}</p>
        @endif
        <p class="menu-price">Rp{{ number_format($menu->harga, 0, ',', '.') }} <span class="hint">/ porsi</span></p>
        <p class="hint">Satu pesanan berisi satu jenis menu. Untuk menu lain, buat pesanan terpisah.</p>

        @if ($errors->any())
            <div class="errors" role="alert">
                <strong>Mohon periksa kembali pesanan Anda:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('orders.store', $menu->id) }}">
            @csrf
            <label for="nama_pelanggan">Nama pemesan</label>
            <input id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" maxlength="255" autocomplete="name" required>

            <label for="telepon">Nomor telepon</label>
            <input id="telepon" name="telepon" type="tel" value="{{ old('telepon') }}" maxlength="30" autocomplete="tel" placeholder="Contoh: 081234567890" required>

            <label for="jumlah">Jumlah porsi</label>
            <input id="jumlah" name="jumlah" type="number" min="1" max="50" value="{{ old('jumlah', 1) }}" required>

            <label for="catatan">Catatan (opsional)</label>
            <textarea id="catatan" name="catatan" maxlength="1000">{{ old('catatan') }}</textarea>

            <button type="submit">Lanjut ke pembayaran</button>
        </form>
    </section>
</main>
</body>
</html>
