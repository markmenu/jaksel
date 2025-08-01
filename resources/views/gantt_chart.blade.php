<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gantt Chart Monitoring</title>

    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
    <link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        /* 2. CSS untuk fitur pengingat deadline */
        .gantt_task_line.overdue {
            background-color: #d9534f; /* Merah untuk lewat deadline */
            border-color: #d43f3a;
        }
        .gantt_task_line.near_deadline {
            background-color: #f0ad4e; /* Oranye untuk mendekati deadline */
            border-color: #eea236;
        }
        .gantt_task_line.overdue .gantt_task_progress,
        .gantt_task_line.near_deadline .gantt_task_progress {
            background-color: #5cb85c; /* Warna progress bar tetap hijau */
        }
    </style>
</head>
<body>

    <div id="gantt_here" style='width:100%; height:100%;'></div>

    <script>
        // 3. Konfigurasi Gantt Chart
        
        // Menentukan kolom yang akan tampil di tabel kiri
        gantt.config.columns = [
            {name: "text", label: "Nama Kegiatan", tree: true, width: '*', resize: true},
            {name: "start_date", label: "Tanggal Mulai", align: "center", width: 120},
            {name: "duration", label: "Durasi (Hari)", align: "center", width: 80},
            {name: "add", width: 44} // Tombol "+" untuk menambah sub-tugas
        ];

        // Menghilangkan menu-menu di atas tabel (sesuai permintaan)
        gantt.config.show_top_info = false;
        gantt.config.show_task_count = false;

        // Skala waktu (timeline)
        gantt.config.scales = [
            {unit: "month", step: 1, format: "%F, %Y"},
            {unit: "week", step: 1, format: function (date) {
                var dateToStr = gantt.date.date_to_str("%d %M");
                var endDate = gantt.date.add(date, 6, "day");
                return dateToStr(date) + " - " + dateToStr(endDate);
            }}
        ];

        // 4. Implementasi Fitur Pengingat Deadline
        gantt.templates.task_class = function(start, end, task){
            var today = new Date();
            var deadline = new Date(task.end_date); // 'end_date' otomatis dihitung oleh DHTMLX
            var threeDaysBefore = new Date(deadline);
            threeDaysBefore.setDate(deadline.getDate() - 3);

            if(task.progress < 1 && today > deadline){
                return "overdue"; // Tambahkan class 'overdue' jika lewat deadline
            }
            if(task.progress < 1 && today >= threeDaysBefore && today <= deadline){
                return "near_deadline"; // Tambahkan class 'near_deadline' jika H-3
            }
            return "";
        };

        // Inisialisasi dan load data dari API
        gantt.init("gantt_here");
        gantt.load("{{ route('gantt.data') }}");

    </script>
</body>
</html>