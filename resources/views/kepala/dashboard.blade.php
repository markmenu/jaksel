<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Monitoring Seluruh Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Timeline Seluruh Kegiatan</h3>
                    
                    {{-- "Kanvas" untuk ApexCharts --}}
                    <div id="chart"></div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Memuat library ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var seriesData = @json($series);
        var progressData = @json($progressData);

        var options = {
          series: [{
            data: seriesData
          }],
          chart: {
            height: 450,
            type: 'rangeBar',
            toolbar: { show: true }
          },
          plotOptions: {
            bar: {
              horizontal: true,
              distributed: true,
              dataLabels: {
                hideOverflowingLabels: false
              }
            }
          },
          dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
              var label = opts.w.globals.labels[opts.dataPointIndex];
              var progress = progressData[label] || 0;
              return label + ': ' + progress + '%'
            },
            style: {
              colors: ['#f8f8f8', '#fff']
            },
            dropShadow: {
              enabled: true,
              blur: 1,
              opacity: 0.5
            }
          },
          xaxis: {
            type: 'datetime',
            labels: {
                style: {
                    colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                }
            }
          },
          yaxis: {
            show: true,
            labels: {
                style: {
                    colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                }
            }
          },
          grid: {
            borderColor: document.documentElement.classList.contains('dark') ? '#4B5563' : '#E5E7EB'
          },
          tooltip: {
            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
          }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
    @endpush
</x-app-layout>
