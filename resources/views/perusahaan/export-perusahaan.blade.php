{{-- resources/views/perusahaan/export-perusahaan.blade.php --}}
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Perusahaan</th>
            <th>Bidang Usaha</th>
            <th>Izin Usaha</th>
            <th>Golongan Usaha</th>
            <th>Direktur Utama</th>
            <th>Direktur</th>
            <th>Komisaris Utama</th>
            <th>Komisaris</th>
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
                <td>{{ $perusahaan->BidangUsh ?: '-' }}</td>
                <td>{{ $perusahaan->IzinUsh ?: '-' }}</td>
                <td>{{ $perusahaan->GolonganUsh ?: '-' }}</td>
                <td>{{ $perusahaan->DirekturUtm ?: '-' }}</td>
                <td>{{ $perusahaan->Direktur ?: '-' }}</td>
                <td>{{ $perusahaan->KomisarisUtm ?: '-' }}</td>
                <td>{{ $perusahaan->Komisaris ?: '-' }}</td>
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
    @if(isset($filter['bidang']) && $filter['bidang'])
    <tr>
        <td>Bidang Usaha</td>
        <td>{{ $filter['bidang'] }}</td>
    </tr>
    @endif
    @if(isset($filter['izin']) && $filter['izin'])
    <tr>
        <td>Izin Usaha</td>
        <td>{{ $filter['izin'] }}</td>
    </tr>
    @endif
    @if(isset($filter['golongan']) && $filter['golongan'])
    <tr>
        <td>Golongan Usaha</td>
        <td>{{ $filter['golongan'] }}</td>
    </tr>
    @endif
    @if(isset($filter['direktur_utama']) && $filter['direktur_utama'])
    <tr>
        <td>Direktur Utama</td>
        <td>{{ $filter['direktur_utama'] }}</td>
    </tr>
    @endif
    @if(isset($filter['direktur']) && $filter['direktur'])
    <tr>
        <td>Direktur</td>
        <td>{{ $filter['direktur'] }}</td>
    </tr>
    @endif
    @if(isset($filter['komisaris_utama']) && $filter['komisaris_utama'])
    <tr>
        <td>Komisaris Utama</td>
        <td>{{ $filter['komisaris_utama'] }}</td>
    </tr>
    @endif
    @if(isset($filter['komisaris']) && $filter['komisaris'])
    <tr>
        <td>Komisaris</td>
        <td>{{ $filter['komisaris'] }}</td>
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