<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function create(int $menu): View
    {
        $menuItem = DB::table('menus')->find($menu);

        abort_if($menuItem === null, 404);

        return view('orders.create', ['menu' => $menuItem]);
    }

    public function store(Request $request, int $menu): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:30', 'regex:/^[0-9+\s().-]+$/'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = DB::transaction(function () use ($menu, $validated) {
            $menuItem = DB::table('menus')->where('id', $menu)->lockForUpdate()->first();

            abort_if($menuItem === null, 404);

            $total = (int) $menuItem->harga * (int) $validated['jumlah'];

            if ($total > 999999999999) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Total pesanan melebihi batas yang dapat diproses.',
                ]);
            }

            $kodePesanan = (string) Str::uuid();

            $orderId = DB::table('orders')->insertGetId([
                'kode_pesanan' => $kodePesanan,
                'menu_id' => $menuItem->id,
                'nama_menu' => $menuItem->nama_menu,
                'harga_satuan' => $menuItem->harga,
                'jumlah' => $validated['jumlah'],
                'total_harga' => $total,
                'nama_pelanggan' => $validated['nama_pelanggan'],
                'telepon' => $validated['telepon'],
                'catatan' => $validated['catatan'] ?? null,
                'metode_pembayaran' => 'transfer_bank',
                'status' => 'menunggu_pembayaran',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return (object) ['id' => $orderId, 'kode_pesanan' => $kodePesanan];
        });

        return redirect()->route('orders.payment', $order->kode_pesanan);
    }

    public function payment(string $kodePesanan): View
    {
        $order = DB::table('orders')
            ->where('kode_pesanan', $kodePesanan)
            ->first();

        abort_if($order === null, 404);

        return view('orders.payment', [
            'order' => $order,
            'bankTransfer' => config('payment.bank_transfer'),
        ]);
    }
}
