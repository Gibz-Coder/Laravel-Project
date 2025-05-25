<div>
    <div class="card custom-card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="card-title">{{ $cutoffDisplay }} Submitted</div>
                <div wire:loading class="spinner-border spinner-border-sm text-primary ms-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-cutoff mb-0 text-nowrap">
                    <thead>
                        <tr class="fw-bold bg-primary-light text-primary">
                            <th class="fw-bold bg-primary-light text-primary">Line</th>
                            <th class="fw-bold bg-primary-light text-primary">Target</th>
                            <th class="cutoff-border-start bg-primary-light text-primary">Result</th>
                            <th class="fw-bold bg-primary-light text-primary">Rate %</th>
                            <th class="cutoff-border-start bg-primary-light text-primary">Short</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lineCodes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'VMI'];
                        @endphp

                        @foreach($lineCodes as $index => $lineCode)
                        <tr class="hover-row">
                            <td>{{ $lineCode }}</td>
                            <td>{{ number_format(($targetData[$index] ?? 0) / 1000000, 2) }}M</td>
                            <td class="cutoff-border-start">{{ number_format(($submittedData[$index] ?? 0) / 1000000, 2) }}M</td>
                            <td class="{{ ($percentages[$index] ?? 0) >= 100 ? 'text-success' : (($percentages[$index] ?? 0) >= 90 ? 'text-secondary' : 'text-danger') }}">
                                {{ $percentages[$index] ?? 0 }}%
                            </td>
                            <td class="cutoff-border-start text-danger">{{ number_format(($shortages[$index] ?? 0) / 1000000, 2) }}M</td>
                        </tr>
                        @endforeach

                        @php
                            $totalTarget = array_sum($targetData);
                            $totalSubmitted = array_sum($submittedData);
                            $totalShortage = array_sum($shortages);
                            $totalPercentage = $totalTarget > 0 ? round(($totalSubmitted / $totalTarget) * 100, 1) : 0;
                        @endphp
                        <tr class="hover-row fw-bold">
                            <td>Total</td>
                            <td>{{ number_format($totalTarget / 1000000, 2) }}M</td>
                            <td class="cutoff-border-start">{{ number_format($totalSubmitted / 1000000, 2) }}M</td>
                            <td class="{{ $totalPercentage >= 100 ? 'text-success' : ($totalPercentage >= 90 ? 'text-secondary' : 'text-danger') }}">
                                {{ $totalPercentage }}%
                            </td>
                            <td class="cutoff-border-start text-danger">{{ number_format($totalShortage / 1000000, 2) }}M</td>
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
            border-left: 1px solid #6c757d !important;
        }
        .cutoff-border-end {
            border-right: 1px solid #6c757d !important;
        }
        .table-cutoff {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-cutoff th, .table-cutoff td {
            border: 1px solid #495057;
        }
    </style>
</div>
