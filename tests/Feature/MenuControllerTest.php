<?php

namespace Tests\Feature;

use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_displays_available_menu_items_and_prices(): void
    {
        $this->seed(MenuSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Soto Betawi Daging Sapi')
            ->assertSee('Rp35.000')
            ->assertSee('Pilihan Menu');
    }

    public function test_seeded_menu_images_from_public_images_are_displayed(): void
    {
        $this->seed(MenuSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('src="'.asset('images/soto-betawi-daging.jpg').'"', false)
            ->assertSee('src="'.asset('images/soto-betawi-ayam.jpg').'"', false)
            ->assertSee('src="'.asset('images/soto-betawi-jeroan.jpg').'"', false)
            ->assertSee('src="'.asset('images/nasi-putih.jpg').'"', false)
            ->assertSee('src="'.asset('images/esjeruk-esteh.jpg').'"', false);
    }

    public function test_customer_can_search_menu_by_name(): void
    {
        $this->seed(MenuSeeder::class);

        $this->get('/?search=Nasi')
            ->assertOk()
            ->assertSee('Nasi Putih')
            ->assertDontSee('Soto Betawi Ayam');
    }

    public function test_customer_can_filter_menu_by_category(): void
    {
        $this->seed(MenuSeeder::class);

        $this->get('/?kategori=Minuman')
            ->assertOk()
            ->assertSee('Es Jeruk / Teh Manis')
            ->assertDontSee('Nasi Putih');
    }

    public function test_customer_can_select_food_category_and_submit_search_form(): void
    {
        $this->seed(MenuSeeder::class);

        $this->get('/?kategori='.urlencode('Makanan'))
            ->assertOk()
            ->assertSee('kategori <strong>Makanan</strong>', false)
            ->assertSee('Soto Betawi Daging Sapi')
            ->assertSee('Soto Betawi Ayam')
            ->assertSee('Soto Betawi Campur (Jeroan)')
            ->assertSee('Nasi Putih')
            ->assertDontSee('Es Jeruk / Teh Manis');
    }

    public function test_customer_search_form_exposes_category_filter_and_keeps_selection(): void
    {
        $this->seed(MenuSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('method="GET" action="'.route('menus.index').'"', false)
            ->assertSee('name="kategori"', false)
            ->assertSee('Cari Menu');

        $this->get('/?kategori='.urlencode('Makanan'))
            ->assertSee('<option value="Makanan" selected>', false);
    }

    public function test_unknown_category_does_not_return_all_menu_items(): void
    {
        $this->seed(MenuSeeder::class);

        $this->get('/?kategori='.urlencode('Kategori Tidak Ada'))
            ->assertOk()
            ->assertSee('0 menu ditemukan')
            ->assertDontSee('Soto Betawi Daging Sapi');
    }

    public function test_seller_menu_management_routes_are_not_public(): void
    {
        $this->get('/admin/tambah-menu')->assertNotFound();
        $this->post('/admin/tambah-menu')->assertNotFound();
    }

    public function test_homepage_shows_menu_records_already_in_database(): void
    {
        DB::table('menus')->insert([
            'nama_menu' => 'Menu Pilihan Pembeli',
            'kategori' => 'Makanan',
            'harga' => 27000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Menu Pilihan Pembeli')
            ->assertSee('Rp27.000');
    }

    public function test_customer_can_open_a_single_menu_order_form(): void
    {
        $this->seed(MenuSeeder::class);
        $menu = DB::table('menus')->where('nama_menu', 'Nasi Putih')->first();

        $this->get(route('orders.create', $menu->id))
            ->assertOk()
            ->assertSee('Pesan Nasi Putih')
            ->assertSee('Lanjut ke pembayaran');
    }

    public function test_customer_can_place_a_single_menu_order_and_view_payment_page(): void
    {
        $this->seed(MenuSeeder::class);
        $menu = DB::table('menus')->where('nama_menu', 'Nasi Putih')->first();

        $response = $this->post(route('orders.store', $menu->id), [
            'nama_pelanggan' => 'Pembeli Test',
            'telepon' => '081234567890',
            'jumlah' => 3,
            'catatan' => 'Tidak pakai bawang.',
        ]);

        $order = DB::table('orders')->first();

        $this->assertNotNull($order);
        $this->assertSame($menu->id, $order->menu_id);
        $this->assertSame(18000, $order->total_harga);
        $this->assertSame('menunggu_pembayaran', $order->status);

        $response->assertRedirect(route('orders.payment', $order->kode_pesanan));

        $this->get(route('orders.payment', $order->kode_pesanan))
            ->assertOk()
            ->assertSee('Instruksi Pembayaran')
            ->assertSee('Rp18.000')
            ->assertSee('Menunggu pembayaran')
            ->assertSee('Informasi rekening belum diatur');
    }

    public function test_payment_page_displays_configured_bank_transfer_details(): void
    {
        $this->seed(MenuSeeder::class);
        $menu = DB::table('menus')->where('nama_menu', 'Nasi Putih')->first();

        $this->post(route('orders.store', $menu->id), [
            'nama_pelanggan' => 'Pembeli Test',
            'telepon' => '081234567890',
            'jumlah' => 1,
        ]);

        $order = DB::table('orders')->first();
        config()->set('payment.bank_transfer', [
            'bank_name' => 'Bank Contoh',
            'account_name' => 'Pemilik Toko',
            'account_number' => '1234567890',
        ]);

        $this->get(route('orders.payment', $order->kode_pesanan))
            ->assertOk()
            ->assertSee('Bank Contoh')
            ->assertSee('Pemilik Toko')
            ->assertSee('1234567890');
    }

    public function test_order_rejects_invalid_customer_data(): void
    {
        $this->seed(MenuSeeder::class);
        $menu = DB::table('menus')->where('nama_menu', 'Nasi Putih')->first();

        $this->from(route('orders.create', $menu->id))
            ->post(route('orders.store', $menu->id), [
                'nama_pelanggan' => '',
                'telepon' => 'not-a-phone-number',
                'jumlah' => 0,
            ])
            ->assertRedirect(route('orders.create', $menu->id))
            ->assertSessionHasErrors(['nama_pelanggan', 'telepon', 'jumlah']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_customer_cannot_order_a_missing_menu_or_view_a_missing_order(): void
    {
        $this->get('/pesan/999999')->assertNotFound();
        $this->post('/pesan/999999', [
            'nama_pelanggan' => 'Pembeli Test',
            'telepon' => '081234567890',
            'jumlah' => 1,
        ])->assertNotFound();
        $this->get('/pembayaran/00000000-0000-0000-0000-000000000000')->assertNotFound();
    }

    public function test_seeder_can_be_run_more_than_once_without_duplicates(): void
    {
        $this->seed(MenuSeeder::class);

        DB::table('menus')
            ->where('nama_menu', 'Soto Betawi Daging Sapi')
            ->update([
                'created_at' => '2020-01-01 00:00:00',
                'gambar' => 'menus/soto-betawi.jpg',
            ]);

        $this->seed(MenuSeeder::class);

        $this->assertDatabaseCount('menus', 5);
        $this->assertSame(
            '2020-01-01 00:00:00',
            DB::table('menus')
                ->where('nama_menu', 'Soto Betawi Daging Sapi')
                ->value('created_at')
        );
        $this->assertSame(
            'menus/soto-betawi.jpg',
            DB::table('menus')
                ->where('nama_menu', 'Soto Betawi Daging Sapi')
                ->value('gambar')
        );
    }
}
