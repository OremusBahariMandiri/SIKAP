<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No. Reg</th>
            <th>Perusahaan</th>
            <th>Kategori</th>
            <th>Jenis</th>
            <th>Peruntukan</th>
            <th>Atas Nama</th>
            <th>Tgl Terbit</th>
            <th>Tgl Berakhir</th>
            <th>Tgl Peringatan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dokLegals as $index => $dokumen)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $dokumen->NoRegDok }}</td>
                <td>{{ $dokumen->DokPerusahaan }}</td>
                <td>{{ $dokumen->KategoriDok }}</td>
                <td>{{ $dokumen->JenisDok }}</td>
                <td>{{ $dokumen->PeruntukanDok }}</td>
                <td>{{ $dokumen->DokAtasNama }}</td>
                <td>
                    @if ($dokumen->TglTerbitDok)
                        {{ \Carbon\Carbon::parse($dokumen->TglTerbitDok)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ($dokumen->TglBerakhirDok)
                        {{ \Carbon\Carbon::parse($dokumen->TglBerakhirDok)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ($dokumen->TglPengingat)
                        {{ \Carbon\Carbon::parse($dokumen->TglPengingat)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $dokumen->StsBerlakuDok ?: '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($filter)
<table style="margin-top: 20px;">
    <tr>
        <td colspan="2"><strong>Informasi Filter yang Diterapkan:</strong></td>
    </tr>
    @if(isset($filter['noreg']) && $filter['noreg'])
    <tr>
        <td>No. Registrasi</td>
        <td>{{ $filter['noreg'] }}</td>
    </tr>
    @endif
    @if(isset($filter['perusahaan']) && $filter['perusahaan'])
    <tr>
        <td>Perusahaan</td>
        <td>{{ $filter['perusahaan'] }}</td>
    </tr>
    @endif
    @if(isset($filter['kategori']) && $filter['kategori'])
    <tr>
        <td>Kategori</td>
        <td>{{ $filter['kategori'] }}</td>
    </tr>
    @endif
    @if(isset($filter['jenis']) && $filter['jenis'])
    <tr>
        <td>Jenis Dokumen</td>
        <td>{{ $filter['jenis'] }}</td>
    </tr>
    @endif
    @if(isset($filter['peruntukan']) && $filter['peruntukan'])
    <tr>
        <td>Peruntukan</td>
        <td>{{ $filter['peruntukan'] }}</td>
    </tr>
    @endif
    @if(isset($filter['atas_nama']) && $filter['atas_nama'])
    <tr>
        <td>Atas Nama</td>
        <td>{{ $filter['atas_nama'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_terbit_from']) && $filter['tgl_terbit_from'])
    <tr>
        <td>Tanggal Terbit (Dari)</td>
        <td>{{ $filter['tgl_terbit_from'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_terbit_to']) && $filter['tgl_terbit_to'])
    <tr>
        <td>Tanggal Terbit (Sampai)</td>
        <td>{{ $filter['tgl_terbit_to'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_berakhir_from']) && $filter['tgl_berakhir_from'])
    <tr>
        <td>Tanggal Berakhir (Dari)</td>
        <td>{{ $filter['tgl_berakhir_from'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_berakhir_to']) && $filter['tgl_berakhir_to'])
    <tr>
        <td>Tanggal Berakhir (Sampai)</td>
        <td>{{ $filter['tgl_berakhir_to'] }}</td>
    </tr>
    @endif
    @if(isset($filter['sts_berlaku']) && $filter['sts_berlaku'])
    <tr>
        <td>Status</td>
        <td>{{ $filter['sts_berlaku'] }}</td>
    </tr>
    @endif
    <tr>
        <td>Tanggal Export</td>
        <td>{{ now()->format('d/m/Y H:i:s') }}</td>
    </tr>
</table>
@endif