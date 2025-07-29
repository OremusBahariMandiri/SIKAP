{{-- resources/views/perusahaan/export-perusahaan.blade.php --}}
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Perusahaan</th>
            <th>Telepon</th>
            <th>Email</th>
            <th>Website</th>
            <th>Alamat</th>
            <th>Tanggal Berdiri</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($perusahaans as $index => $perusahaan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $perusahaan->NamaPrsh }}</td>
                <td>{{ $perusahaan->TelpPrsh }}</td>
                <td>{{ $perusahaan->EmailPrsh }}</td>
                <td>{{ $perusahaan->WebPrsh ?: '-' }}</td>
                <td>{{ $perusahaan->AlamatPrsh }}</td>
                <td>
                    @if ($perusahaan->TglBerdiri)
                        {{ \Carbon\Carbon::parse($perusahaan->TglBerdiri)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($filter)
<table style="margin-top: 20px;">
    <tr>
        <td colspan="2"><strong>Informasi Filter yang Diterapkan:</strong></td>
    </tr>
    @if(isset($filter['nama']) && $filter['nama'])
    <tr>
        <td>Nama Perusahaan</td>
        <td>{{ $filter['nama'] }}</td>
    </tr>
    @endif
    @if(isset($filter['telepon']) && $filter['telepon'])
    <tr>
        <td>Telepon</td>
        <td>{{ $filter['telepon'] }}</td>
    </tr>
    @endif
    @if(isset($filter['email']) && $filter['email'])
    <tr>
        <td>Email</td>
        <td>{{ $filter['email'] }}</td>
    </tr>
    @endif
    @if(isset($filter['website']) && $filter['website'])
    <tr>
        <td>Website</td>
        <td>{{ $filter['website'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_berdiri_from']) && $filter['tgl_berdiri_from'])
    <tr>
        <td>Tanggal Berdiri (Dari)</td>
        <td>{{ $filter['tgl_berdiri_from'] }}</td>
    </tr>
    @endif
    @if(isset($filter['tgl_berdiri_to']) && $filter['tgl_berdiri_to'])
    <tr>
        <td>Tanggal Berdiri (Sampai)</td>
        <td>{{ $filter['tgl_berdiri_to'] }}</td>
    </tr>
    @endif
    <tr>
        <td>Tanggal Export</td>
        <td>{{ now()->format('d/m/Y H:i:s') }}</td>
    </tr>
</table>
@endif