<div>
    <div class="card custom-card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="card-title">Submitted per Cutoff</div>
                <div wire:loading class="spinner-border spinner-border-sm text-primary ms-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-cutoff mb-0 text-nowrap" style="font-size: 0.9rem;">
                    <thead>
                        <tr>
                            <th class="text-center bg-primary-light text-primary">Line</th>
                            <th class="text-center bg-primary-light text-primary">Target</th>
                            @php $index = 0; @endphp
                            @foreach($cutoffNames as $cutoffTime => $cutoffDisplay)
                                @php $isEven = $index % 2 == 0; $index++; @endphp
                                <th class="text-center cutoff-border-start bg-primary-light text-primary{{ $isEven ? 'cutoff-section' : '' }}">{{ $cutoffDisplay }} Qty</th>
                                <th class="text-center cutoff-border-end bg-primary-light text-primary{{ $isEven ? 'cutoff-section' : '' }}">{{ $cutoffDisplay }} %</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lineData as $line)
                        <tr class="hover-row">
                            <td class="text-center">{{ $line['line'] }}</td>
                            <td class="text-center">{{ number_format($line['target'] / 1000000, 2) }}M</td>

                            @php $index = 0; @endphp
                            @foreach($cutoffNames as $cutoffTime => $cutoffDisplay)
                                @php $isEven = $index % 2 == 0; $index++; @endphp
                                <td class="text-center cutoff-border-start {{ $isEven ? 'cutoff-section' : '' }}">{{ $line['cutoffs'][$cutoffTime]['qty'] > 0 ? number_format($line['cutoffs'][$cutoffTime]['qty'] / 1000000, 2) . 'M' : '0M' }}</td>
                                <td class="text-center cutoff-border-end {{ $isEven ? 'cutoff-section' : '' }} {{ $line['cutoffs'][$cutoffTime]['percentage'] >= 100 ? 'text-success' : ($line['cutoffs'][$cutoffTime]['percentage'] >= 90 ? 'text-secondary' : 'text-danger') }}">
                                    {{ $line['cutoffs'][$cutoffTime]['percentage'] > 0 ? $line['cutoffs'][$cutoffTime]['percentage'] : '0.0' }}%
                                </td>
                            @endforeach
                        </tr>
                        @endforeach

                        <!-- Total Row -->
                        <tr class="fw-bold bg-light">
                            <td class="text-center">Total</td>
                            <td class="text-center">{{ number_format($totalTarget / 1000000, 2) }}M</td>

                            @php $index = 0; @endphp
                            @foreach($cutoffNames as $cutoffTime => $cutoffDisplay)
                                @php $isEven = $index % 2 == 0; $index++; @endphp
                                <td class="text-center cutoff-border-start {{ $isEven ? 'cutoff-section' : '' }}">{{ $totalCutoffs[$cutoffTime]['qty'] > 0 ? number_format($totalCutoffs[$cutoffTime]['qty'] / 1000000, 2) . 'M' : '0M' }}</td>
                                <td class="text-center cutoff-border-end {{ $isEven ? 'cutoff-section' : '' }} {{ $totalCutoffs[$cutoffTime]['percentage'] >= 100 ? 'text-success' : ($totalCutoffs[$cutoffTime]['percentage'] >= 90 ? 'text-secondary' : 'text-danger') }}">
                                    {{ $totalCutoffs[$cutoffTime]['percentage'] > 0 ? $totalCutoffs[$cutoffTime]['percentage'] : '0.0' }}%
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .hover-row:hover {
            background-color: var(--bs-secondary-bg) !important;
        }
        .cutoff-border-start {
            border-left: 2px solid #495057 !important;
        }
        .cutoff-border-end {
            border-right: 2px solid #495057 !important;
        }
        .table-cutoff {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-cutoff th, .table-cutoff td {
            border: 1px solid #495057;
        }

        /* Add background color to separate cutoff sections */
        .cutoff-section {
            background-color: rgba(52, 58, 64, 0.3);
        }
    </style>
</div>
