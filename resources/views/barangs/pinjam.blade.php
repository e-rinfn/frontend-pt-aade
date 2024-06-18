<form action="{{ route('barangs.pinjam', $barang['id']) }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="jumlah_pinjam">Jumlah Pinjam</label>
        <input type="number" name="jumlah_pinjam" class="form-control" id="jumlah_pinjam" required>
    </div>
    <button type="submit" class="btn btn-primary">Pinjam Barang</button>
</form>
