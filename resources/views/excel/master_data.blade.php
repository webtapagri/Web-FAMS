<table>
    <thead>
        <tr style="background-color:#ccc;">
            <th>KODE MATERIAL</th>
            <th>NAMA MATERIAL</th>
            <th>BA PEMILIK ASSET</th>
            <th>LOKASI ASSET</th>
            <th>NAMA ASSET</th>
            <th>KODE ASSET SAP</th>
            <th>KODE ASSET AMS</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $data)
        <tr>
            <td>{{ $data->kode_material }}</td>
            <td>{{ $data->nama_material }}</td>
            <td>{{ $data->ba_pemilik_asset }}</td>
            <td>{{ $data->lokasi_ba_description }}</td>
            <td>{{ $data->nama_asset }}</td>
            <td>{{ $data->kode_asset_sap }}</td>
            <td>{{ $data->kode_asset_ams }}</td>
        </tr>
    @endforeach
    </tbody>
</table>