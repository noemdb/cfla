@php
    $chart_id = 'incidents-' . $estudiant->id;
@endphp

<div class="border rounded my-1">

    <div class="alert alert-secondary p-1 border-0">Incidencias registradas por mes. [<span class="text-muted small">Últimos 8</span>]</div>

    <div class="p-1" style="min-width: 320px;"><canvas id="{{ $chart_id ?? null }}"></canvas></div>

</div>

@section('scripts')
    @parent
    <script src="{{ asset('vendor/chartjs/4.2.1/chart.umd.js') }}"></script>

    <script type="text/javascript">
        (async function() {

            const urlApi =
                '{{ route('representants.incidents.month.chart', ['range' => '8', 'limit' => '8', 'estudiant_id' => $estudiant->id]) }}';

            fetch(urlApi)
                .then(response => response.json())
                .then(data => {
                    // console.log(data);
                    const labels = data.labels; //console.log(labels);
                    const datasets = data.datasets; //console.log(datasets);
                    new Chart(
                        document.getElementById('{{ $chart_id ?? null }}'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: datasets
                            },
                            options: {
                                elements: {
                                    line: {
                                        tension: 0.4
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        }
                    );
                })
                .catch(error => {
                    console.error('Error al obtener los datos:', error);
                });

        })();
    </script>
@endsection
