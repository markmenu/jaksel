<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Superadmin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Ringkasan Sistem</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Card Total Pengguna -->
                        <div class="bg-indigo-500 text-white p-6 rounded-lg shadow-md">
                            <h4 class="text-sm font-medium opacity-80">Total Pengguna</h4>
                            <p class="text-3xl font-bold mt-2">{{ $totalUsers }}</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold mb-4">Distribusi Peran Pengguna</h3>
                    <div id="roleChart"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var roleLabels = @json($roleLabels);
        var roleSeries = @json($roleSeries);

        var options = {
          series: [{
            name: 'Jumlah Pengguna',
            data: roleSeries
          }],
          chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false },
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: '55%',
              endingShape: 'rounded'
            },
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
          },
          xaxis: {
            categories: roleLabels,
            labels: {
                style: {
                    colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                }
            }
          },
          yaxis: {
            title: {
              text: 'Jumlah'
            },
            labels: {
                style: {
                    colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                }
            }
          },
          fill: {
            opacity: 1
          },
          tooltip: {
            y: {
              formatter: function (val) {
                return val + " pengguna"
              }
            }
          }
        };

        var chart = new ApexCharts(document.querySelector("#roleChart"), options);
        chart.render();
    </script>
    @endpush
</x-app-layout>
