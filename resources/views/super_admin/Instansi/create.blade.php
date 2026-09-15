@extends('layouts.app')

@section('content')

<!-- LEAFLET CSS -->
<link rel="stylesheet"
      href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<!-- LEAFLET JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<div class="bg-white rounded-2xl shadow p-8">

    <div class="mb-8">

        <h1 class="text-3xl font-bold text-gray-800">
            Tambah Instansi
        </h1>

        <p class="text-gray-500 mt-2">
            Tambahkan data instansi magang beserta lokasi GPS kantor.
        </p>

    </div>

    <form action="{{ route('instansi.store') }}"
          method="POST">

        @csrf

        <!-- NAMA INSTANSI -->
        <div class="mb-6">

            <label class="block font-semibold mb-2">
                Nama Instansi
            </label>

            <input type="text"
                   name="nama_instansi"
                   class="w-full border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Masukkan nama instansi"
                   required>

        </div>

        <!-- JENIS INSTANSI -->
        <div class="mb-6">

            <label class="block font-semibold mb-2">
                Jenis Instansi
            </label>

            <select name="jenis_instansi"
                    class="w-full border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>

                <option value="">
                    -- Pilih Jenis --
                </option>

                <option value="kantor">
                    Kantor / Pemerintahan
                </option>

                <option value="lapangan">
                    Lapangan / Teknisi
                </option>

            </select>

        </div>

        <!-- NO TELEPON -->
        <div class="mb-6">

            <label class="block font-semibold mb-2">
                Nomor Telepon Instansi
            </label>

            <input type="text"
                   name="no_telp"
                   class="w-full border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Masukkan nomor telepon instansi">

        </div>

        <!-- ALAMAT -->
        <div class="mb-6">

            <label class="block font-semibold mb-2">
                Alamat Instansi
            </label>

            <textarea name="alamat"
                      rows="4"
                      class="w-full border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Masukkan alamat instansi"></textarea>

        </div>

        <!-- MAP -->
        <div class="mb-6">

            <label class="block font-semibold mb-3">
                Pilih Titik Lokasi Kantor
            </label>

            <div id="map"
                 class="rounded-2xl"
                 style="height: 450px;"></div>

        </div>

        <!-- LATITUDE -->
        <div class="mb-6">

            <label class="block font-semibold mb-2">
                Latitude
            </label>

            <input type="text"
                   name="latitude"
                   id="latitude"
                   readonly
                   class="w-full border rounded-xl px-4 py-3 bg-gray-100">

        </div>

        <!-- LONGITUDE -->
        <div class="mb-6">

            <label class="block font-semibold mb-2">
                Longitude
            </label>

            <input type="text"
                   name="longitude"
                   id="longitude"
                   readonly
                   class="w-full border rounded-xl px-4 py-3 bg-gray-100">

        </div>

        <!-- RADIUS -->
        <div class="mb-6">

            <label class="block font-semibold mb-2">
                Radius Geofence (Meter)
            </label>

            <input type="number"
                   name="radius"
                   value="10"
                   class="w-full border rounded-xl px-4 py-3">

        </div>

        <!-- BUTTON -->
        <button type="submit"
                class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-3 rounded-xl transition">

            Simpan Instansi

        </button>

    </form>

</div>

<script>

    /*
    |--------------------------------------------------------------------------
    | LEAFLET MAP
    |--------------------------------------------------------------------------
    */

    // DEFAULT MAP KE BENGKALIS
    var map = L.map('map').setView([1.4896, 102.0794], 13);

    /*
    |--------------------------------------------------------------------------
    | TILE OPENSTREETMAP
    |--------------------------------------------------------------------------
    */

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {

        attribution: '&copy; OpenStreetMap Contributors'

    }).addTo(map);

    /*
    |--------------------------------------------------------------------------
    | MARKER VARIABLE
    |--------------------------------------------------------------------------
    */

    var marker;

    /*
    |--------------------------------------------------------------------------
    | CLICK MAP
    |--------------------------------------------------------------------------
    */

    map.on('click', function(e) {

        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        // INPUT VALUE
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // REMOVE OLD MARKER
        if (marker) {

            map.removeLayer(marker);

        }

        // ADD MARKER
        marker = L.marker([lat, lng]).addTo(map);

        // POPUP
        marker.bindPopup(

            "Lokasi Instansi Dipilih"

        ).openPopup();

    });

</script>

@endsection