@extends('admin.layouts.index')

@section('content')

<div class="container" style="margin-top:30px;">

```
<!-- TIÊU ĐỀ -->
<div style="
    background: linear-gradient(90deg, #005BAC, #00AEEF);
    padding:15px;
    border-radius:12px;
    margin-bottom:25px;
    box-shadow:0 4px 10px rgba(0,0,0,0.15);
">

    <h3 style="
        margin:0;
        color:white;
        text-align:center;
        font-weight:bold;
        letter-spacing:1px;
    ">
        📊 THỐNG KÊ NHÂN VIÊN THEO PHÒNG BAN
    </h3>

</div>

<!-- BIỂU ĐỒ -->
<div style="
    display:flex;
    gap:30px;
    flex-wrap:wrap;
">

    <!-- BIỂU ĐỒ CỘT -->
    <div style="
        flex:1;
        background:white;
        padding:20px;
        border-radius:15px;
        box-shadow:0 4px 12px rgba(0,0,0,0.1);
    ">

        <h4 style="
            text-align:center;
            color:#005BAC;
            font-weight:bold;
            margin-bottom:20px;
        ">
            📈 Biểu đồ cột
        </h4>

        <canvas id="barChart"></canvas>

    </div>

    <!-- BIỂU ĐỒ TRÒN -->
    <div style="
        flex:1;
        background:white;
        padding:20px;
        border-radius:15px;
        box-shadow:0 4px 12px rgba(0,0,0,0.1);
    ">

        <h4 style="
            text-align:center;
            color:#28a745;
            font-weight:bold;
            margin-bottom:20px;
        ">
            🥧 Biểu đồ tròn
        </h4>

        <canvas id="pieChart"></canvas>

    </div>

</div>

<br><br>

<!-- BẢNG -->
<div style="
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
">

    <h4 style="
        text-align:center;
        color:#ff6600;
        font-weight:bold;
        margin-bottom:20px;
    ">
        📋 Bảng thống kê
    </h4>

    <table class="table table-bordered table-hover text-center">

        <thead style="
            background:linear-gradient(90deg,#005BAC,#00AEEF);
            color:white;
        ">

            <tr>
                <th style="text-align:center;">Tên phòng ban</th>
                <th style="text-align:center;">Số lượng nhân viên</th>
            </tr>

        </thead>

        <tbody>

            @foreach($thongke as $tk)

            <tr style="font-size:16px;">

                <td>
                    {{ $tk->phongban->TENPHONG ?? 'Chưa có phòng ban' }}
                </td>

                <td>

                    <span style="
                        background:#ff5722;
                        color:white;
                        padding:6px 14px;
                        border-radius:20px;
                        font-weight:bold;
                    ">

                        {{ $tk->total }}

                    </span>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>
```

</div>

<!-- Chart.js -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const labels = [

    @foreach($thongke as $tk)

        "{{ $tk->phongban->TENPHONG ?? 'Chưa có phòng ban' }}",

    @endforeach

];

const data = [

    @foreach($thongke as $tk)

        {{ $tk->total }},

    @endforeach

];

const colors = [
    '#005BAC',
    '#00AEEF',
    '#4BC0C0',
    '#FFCE56',
    '#9966FF',
    '#FF6384',
    '#8BC34A',
    '#FF9F40'
];

// BIỂU ĐỒ CỘT
new Chart(document.getElementById('barChart'), {

    type: 'bar',

    data: {

        labels: labels,

        datasets: [{

            label: 'Số lượng nhân viên',

            data: data,

            backgroundColor: colors,

            borderRadius: 10

        }]

    },

    options: {

        responsive: true,

        scales: {

            y: {

                beginAtZero: true

            }

        }

    }

});

// BIỂU ĐỒ TRÒN
new Chart(document.getElementById('pieChart'), {

    type: 'pie',

    data: {

        labels: labels,

        datasets: [{

            data: data,

            backgroundColor: colors

        }]

    },

    options: {

        responsive: true,

        plugins: {

            legend: {

                position: 'bottom'

            }

        }

    }

});

</script>

@endsection
