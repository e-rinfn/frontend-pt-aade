<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>
    <h1>Daftar Barang</h1>
    <a href="{{ route('barangs.create') }}">Tambah Barang</a>
    @if (isset($error))
        <p style="color: red;">{{ $error }}</p>
    @endif
    @if (!empty($barangs))
        <table>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
            @foreach ($barangs as $barang)
                <tr>
                    <td>{{ $barang['id'] }}</td>
                    <td>{{ $barang['nama'] }}</td>
                    <td>{{ $barang['harga'] }}</td>
                    <td>{{ $barang['stok'] }}</td>
                    <td>
                        <a href="{{ route('barangs.show', $barang['id']) }}">Lihat</a>
                        <a href="{{ route('barangs.edit', $barang['id']) }}">Edit</a>
                        <form action="{{ route('barangs.destroy', $barang['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <table>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
            {{-- @foreach ($barangs as $barang) --}}
            <tr>
                {{-- <td>{{ $barang['id'] }}</td>
                <td>{{ $barang['nama'] }}</td>
                <td>{{ $barang['harga'] }}</td>
                <td>{{ $barang['stok'] }}</td>
                <td>
                    <a href="{{ route('barangs.show', $barang['id']) }}">Lihat</a>
                    <a href="{{ route('barangs.edit', $barang['id']) }}">Edit</a>
                    <form action="{{ route('barangs.destroy', $barang['id']) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td> --}}


                <td>ID</td>
                <td>Nama</td>
                <td>Harga</td>
                <td>Stok</td>
                <td>Aksi</td>
            </tr>
            {{-- @endforeach --}}
        </table>
    @endif
</body>

</html>
