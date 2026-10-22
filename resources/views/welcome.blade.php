<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu Soto Betawi | {{ config('app.name', 'Menu Soto Betawi') }}</title>
    <style>
        :root { font-family: Arial, sans-serif; color: #29251f; background: #fbf8f2; }
        * { box-sizing: border-box; }
        body { margin: 0; }
        header { padding: 62px 20px 68px; color: #fff; background: linear-gradient(120deg, #4c2d18, #895629); }
        header > div, main { max-width: 1100px; margin: 0 auto; }
        .eyebrow { margin: 0 0 12px; color: #f0cf9a; font-size: .82rem; font-weight: 700; letter-spacing: .13em; text-transform: uppercase; }
        header h1 { max-width: 650px; margin: 0 0 14px; font-family: Georgia, serif; font-size: clamp(2.4rem, 7vw, 4rem); line-height: 1.05; }
        header p:last-child { max-width: 560px; margin: 0; color: #f2e8dc; font-size: 1.05rem; line-height: 1.7; }
        main { padding: 38px 20px 64px; }
        .section-heading { margin-bottom: 22px; }
        .section-heading h2 { margin: 0 0 7px; font-family: Georgia, serif; font-size: 2rem; }
        .section-heading p { margin: 0; color: #726b62; }
        .filters { display: grid; grid-template-columns: minmax(200px, 1fr) minmax(170px, 230px) auto auto; gap: 10px; margin-bottom: 25px; }
        .filters input, .filters select, .filters button, .reset { min-height: 44px; padding: 10px 13px; border: 1px solid #ded5c8; border-radius: 7px; font: inherit; }
        .filters input, .filters select { width: 100%; background: #fff; }
        .filters button { color: #fff; background: #70451f; border-color: #70451f; font-weight: 700; cursor: pointer; }
        .reset { display: inline-flex; align-items: center; justify-content: center; color: #59452f; background: #fff; text-decoration: none; }
        .result-count { margin: 0 0 15px; color: #726b62; font-size: .92rem; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(245px, 1fr)); gap: 20px; }
        .menu-card { overflow: hidden; background: #fff; border: 1px solid #eee8df; border-radius: 12px; box-shadow: 0 8px 24px #3527140d; }
        .menu-card img { display: block; width: 100%; height: 205px; object-fit: cover; background: #eee6da; }
        .image-placeholder { display: grid; height: 150px; place-items: center; color: #8b755c; background: linear-gradient(135deg, #f3eadc, #e8dac5); font-size: 2.5rem; }
        .menu-content { padding: 18px; }
        .category { display: inline-block; margin: 0 0 10px; padding: 5px 9px; color: #714a24; background: #f7eddd; border-radius: 20px; font-size: .75rem; font-weight: 700; }
        .menu-card h3 { margin: 0 0 9px; font-size: 1.18rem; }
        .description { margin: 0 0 18px; color: #706961; font-size: .94rem; line-height: 1.55; }
        .price { margin: 0; color: #70451f; font-size: 1.2rem; font-weight: 700; }
        .order-link { display: inline-block; margin-top: 16px; padding: 10px 15px; color: #fff; background: #70451f; border-radius: 7px; font-weight: 700; text-decoration: none; }
        .empty { padding: 38px 20px; background: #fff; border: 1px solid #eee8df; border-radius: 12px; text-align: center; }
        .empty h3 { margin: 0 0 8px; }
        .empty p { margin: 0; color: #706961; }
        @media (max-width: 680px) {
            header { padding: 46px 20px 50px; }
            .filters { grid-template-columns: 1fr 1fr; }
            .filters input { grid-column: 1 / -1; }
        }
        @media (max-width: 420px) { .filters { grid-template-columns: 1fr; } .filters input { grid-column: auto; } }
    </style>
</head>
<body>
<header>
    <div>
        <p class="eyebrow">Sajian khas Betawi</p>
        <h1>Rasa hangat, pilihan untuk Anda.</h1>
        <p>Lihat pilihan menu makanan dan minuman kami. Cari menu favorit Anda dan cek harganya di sini.</p>
    </div>
</header>
<main>
    <section aria-labelledby="menu-title">
        <div class="section-heading">
            <h2 id="menu-title">Pilihan Menu</h2>
            <p>Temukan hidangan yang cocok untuk menemani hari Anda.</p>
        </div>

        <form class="filters" method="GET" action="{{ route('menus.index') }}">
            <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama atau deskripsi menu..." aria-label="Cari menu">
            <select name="kategori" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <button type="submit">Cari Menu</button>
            @if ($search || $selectedCategory)
                <a class="reset" href="{{ route('menus.index') }}">Hapus filter</a>
            @endif
        </form>

        <p class="result-count">
            {{ $menus->count() }} menu ditemukan
            @if ($selectedCategory !== '')
                untuk kategori <strong>{{ $selectedCategory }}</strong>
            @endif
            @if ($search !== '')
                dengan pencarian <strong>“{{ $search }}”</strong>
            @endif
        </p>

        @if ($menus->isEmpty())
            <div class="empty">
                <h3>Menu belum ditemukan</h3>
                <p>Coba kata pencarian atau kategori lainnya.</p>
            </div>
        @else
            <div class="menu-grid">
                @foreach ($menus as $menu)
                    <article class="menu-card">
                        @if ($menu->gambar)
                            <img src="{{ str_starts_with($menu->gambar, 'images/') ? asset($menu->gambar) : asset('storage/'.$menu->gambar) }}" alt="Gambar {{ $menu->nama_menu }}" loading="lazy">
                        @else
                            <div class="image-placeholder" aria-hidden="true">🍲</div>
                        @endif
                        <div class="menu-content">
                            <p class="category">{{ $menu->kategori }}</p>
                            <h3>{{ $menu->nama_menu }}</h3>
                            @if ($menu->deskripsi)
                                <p class="description">{{ $menu->deskripsi }}</p>
                            @endif
                            <p class="price">Rp{{ number_format($menu->harga, 0, ',', '.') }}</p>
                            <a class="order-link" href="{{ route('orders.create', $menu->id) }}">Pesan menu ini</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</main>
</body>
</html>
