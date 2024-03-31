function formatTanggalIndo(inputDate) {
    // Daftar nama hari dalam Bahasa Indonesia
    var namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    // Daftar nama bulan dalam Bahasa Indonesia
    var namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    var tanggal = new Date(inputDate);
    var hari = namaHari[tanggal.getDay()];
    var tanggalAngka = tanggal.getDate();
    var bulan = namaBulan[tanggal.getMonth()];
    var tahun = tanggal.getFullYear();

    return hari + ', ' + (tanggalAngka < 10 ? '0' + tanggalAngka : tanggalAngka) + ' ' + bulan + ' ' + tahun;
}

function betweenWorkingDate(start_date, end_date) {
    var start = new Date(start_date);
    var end = new Date(end_date);

    var total_day = 0;
    while (start <= end) {
        if (start.getDay() !== 6 && start.getDay() !== 0) { // 6 = Sabtu, 0 = Minggu
            total_day++;
        }
        // Tambah 1 hari
        start.setDate(start.getDate() + 1);
    }

    return total_day;
}

function betweenTime(start, end) {
    var startTime = new Date('1970-01-01T' + start + ':00');
    var endTime = new Date('1970-01-01T' + end + ':00');

    var timeDiff = endTime - startTime;

    var hours = Math.floor(timeDiff / 3600000);
    var minutes = Math.floor((timeDiff % 3600000) / 60000);
    var seconds = Math.floor((timeDiff % 60000) / 1000);

    var formattedHours = ('0' + hours).slice(-2);
    var formattedMinutes = ('0' + minutes).slice(-2);

    return formattedHours + ':' + formattedMinutes;
}

