<meta name="csrf-token" content="{{ csrf_token() }}">


@if(isset($cartItems) && count($cartItems) > 0)
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                    <tr data-customer-id="{{ $item->customer_id }}">
                        <td>
                            @if($item->type == 'field_booking')
                                <strong>Lapangan: {{ $item->field->name }}</strong><br>
                                <small>{{ \Carbon\Carbon::parse($item->start_time)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}</small>
                            @elseif($item->type == 'rental_item')
                                <strong>Sewa: {{ $item->rentalItem->name }}</strong><br>
                                <small>Jumlah: {{ $item->quantity }} | {{ \Carbon\Carbon::parse($item->start_time)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}</small>
                            @elseif($item->type == 'photographer')
                                <strong>Fotografer: {{ $item->photographer->name }}</strong><br>
                                <small>{{ \Carbon\Carbon::parse($item->start_time)->format('d M Y H:i') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}</small>
                            @elseif($item->type == 'product')
                                <strong>Produk: {{ $item->product->name }}</strong><br>
                                <small>Jumlah: {{ $item->quantity }}</small>
                            @endif
                            <br>
                            <small class="text-muted">{{ $item->notes }}</small>
                        </td>
                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>
                            <button class="btn btn-sm btn-danger remove-cart-item" data-item-id="{{ $item->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="text-end" colspan="2">Rp {{ number_format($cartItems->sum('price'), 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="alert alert-info mb-0">
        <i class="bi bi-cart"></i> Keranjang masih kosong.
    </div>
@endif
