<div class="border rounded my-1">

    <div class="alert alert-secondary p-1 border-0">
        Movimientos Bancarios Percibidos <span class="text-muted small">Últimos 8 Meses</span>
    </div>

    <div class="p-1" style="min-width: 320px;"><canvas id="payments"></canvas></div>

</div>

@section('scripts')
    @parent
    <script src="{{ asset('vendor/chartjs/4.2.1/chart.umd.js') }}"></script>

    <script type="text/javascript">
        (async function() {

            const urlApi =
                '{{ route('representants.bancos.ingresoxmonth.chart', ['range' => '8', 'limit' => '8', 'tipo' => 'line']) }}';

            fetch(urlApi)
                .then(response => response.json())
                .then(data => {
                    // console.log(data);

                    const labels = data.labels; //console.log(labels);
                    const datasets = data.datasets; //console.log(datasets);

                    new Chart(
                        document.getElementById('payments'), {
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
