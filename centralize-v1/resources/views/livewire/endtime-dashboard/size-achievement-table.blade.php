<div>
    <div class="card custom-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title">Achievement per Size</div>
            <div wire:loading class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-cutoff mb-0 text-nowrap">
                    <thead>
                        <tr>
                            <th class="fw-semibold bg-primary-light text-primary">Size</th>
                            @foreach($sizeNames as $index => $size)
                                <th class="fw-semibold bg-primary-light text-primary {{ $index > 0 ? 'cutoff-border-start' : '' }}">{{ $size }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover-row text-info">
                            <td class="fw-semibold">Target</td>
                            @foreach($targetData as $index => $target)
                                <td class="text-info {{ $index > 0 ? 'cutoff-border-start' : '' }}">{{ number_format($target / 1000000, 2) }}M</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row text-primary">
                            <td class="fw-semibold">Endtime</td>
                            @foreach($endtimeData as $index => $endtime)
                                <td class="text-primary {{ $index > 0 ? 'cutoff-border-start' : '' }}">{{ number_format($endtime / 1000000, 2) }}M</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row text-success">
                            <td class="fw-semibold">Submitted</td>
                            @foreach($submittedData as $index => $submitted)
                                <td class="text-success {{ $index > 0 ? 'cutoff-border-start' : '' }}">{{ number_format($submitted / 1000000, 2) }}M</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row text-danger">
                            <td class="fw-semibold">Remaining</td>
                            @foreach($remainingData as $index => $remaining)
                                <td class="text-danger {{ $index > 0 ? 'cutoff-border-start' : '' }}">{{ number_format($remaining / 1000000, 2) }}M</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row">
                            <td class="fw-semibold">Endtime %</td>
                            @foreach($endtimePercentages as $index => $percentage)
                                <td class="{{ $index > 0 ? 'cutoff-border-start' : '' }} {{ $percentage >= 100 ? 'text-success' : 'text-danger' }}">{{ $percentage }}%</td>
                            @endforeach
                        </tr>
                        <tr class="hover-row">
                            <td class="fw-semibold">Submitted %</td>
                            @foreach($submittedPercentages as $index => $percentage)
                                <td class="{{ $index > 0 ? 'cutoff-border-start' : '' }} {{ $percentage >= 100 ? 'text-success' : 'text-danger' }}">{{ $percentage }}%</td>
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
            font-weight: bold !important;
        }
        .hover-row:hover td {
            font-weight: bold !important;
        }

        /* Keep percentage colors unchanged */
        tr:nth-child(5).hover-row:hover td.text-success,
        tr:nth-child(6).hover-row:hover td.text-success {
            color: rgb(var(--success-rgb)) !important;
        }
        tr:nth-child(5).hover-row:hover td.text-danger,
        tr:nth-child(6).hover-row:hover td.text-danger {
            color: rgb(var(--danger-rgb)) !important;
        }

        /* Grid separation styles */
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
