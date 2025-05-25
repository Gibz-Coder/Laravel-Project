<div>
    <div class="card custom-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title">Achievement per Line</div>
            <div wire:loading class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-cutoff mb-0 text-nowrap">
                    <thead>
                        <tr>
                            <th class="fw-bold bg-primary-light text-primary">Line</th>
                            <th class="fw-bold bg-primary-light text-primary">Total</th>
                            <th class="fw-bold bg-primary-light text-primary">A</th>
                            <th class="fw-bold bg-primary-light text-primary">B</th>
                            <th class="fw-bold bg-primary-light text-primary">C</th>
                            <th class="fw-bold bg-primary-light text-primary">D</th>
                            <th class="fw-bold bg-primary-light text-primary">E</th>
                            <th class="fw-bold bg-primary-light text-primary">F</th>
                            <th class="fw-bold bg-primary-light text-primary">G</th>
                            <th class="fw-bold bg-primary-light text-primary">H</th>
                            <th class="fw-bold bg-primary-light text-primary">I</th>
                            <th class="fw-bold bg-primary-light text-primary">J</th>
                            <th class="fw-bold bg-primary-light text-primary">VMI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover-row text-info">
                            <td class="fw-semibold">Target</td>
                            <td>{{ number_format(array_sum($targetData) / 1000000, 2) }}M</td>
                            @foreach($targetData as $target)
                                <td>{{ number_format($target / 1000000, 2) }}M</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row text-primary">
                            <td class="fw-semibold">Endtime</td>
                            <td>{{ number_format(array_sum($endtimeData) / 1000000, 2) }}M</td>
                            @foreach($endtimeData as $endtime)
                                <td>{{ number_format($endtime / 1000000, 2) }}M</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row text-success">
                            <td class="fw-semibold">Submitted</td>
                            <td>{{ number_format(array_sum($submittedData) / 1000000, 2) }}M</td>
                            @foreach($submittedData as $submitted)
                                <td>{{ number_format($submitted / 1000000, 2) }}M</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row text-danger">
                            <td class="fw-semibold">Remaining</td>
                            <td>{{ number_format(array_sum($remainingData) / 1000000, 2) }}M</td>
                            @foreach($remainingData as $remaining)
                                <td>{{ number_format($remaining / 1000000, 2) }}M</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row">
                            <td class="fw-semibold">Endtime %</td>
                            @php
                                $totalTargetSum = array_sum($targetData) ?: 1; // Avoid division by zero
                                $totalEndtimePercentage = round((array_sum($endtimeData) / $totalTargetSum) * 100, 1);
                            @endphp
                            <td class="{{ $totalEndtimePercentage >= 100 ? 'text-success' : 'text-danger' }}">{{ $totalEndtimePercentage }}%</td>
                            @foreach($endtimePercentages as $percentage)
                                <td class="{{ $percentage >= 100 ? 'text-success' : 'text-danger' }}">{{ $percentage }}%</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row">
                            <td class="fw-semibold">Submitted %</td>
                            @php
                                $totalSubmittedPercentage = round((array_sum($submittedData) / $totalTargetSum) * 100, 1);
                            @endphp
                            <td class="{{ $totalSubmittedPercentage >= 100 ? 'text-success' : 'text-danger' }}">{{ $totalSubmittedPercentage }}%</td>
                            @foreach($submittedPercentages as $percentage)
                                <td class="{{ $percentage >= 100 ? 'text-success' : 'text-danger' }}">{{ $percentage }}%</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .bg-primary-light {
            background-color: rgba(var(--primary-rgb), 0.15) !important;
        }

        .hover-row:hover {
            font-weight: bold !important;
        }
        .hover-row:hover td {
            font-weight: bold !important;
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
