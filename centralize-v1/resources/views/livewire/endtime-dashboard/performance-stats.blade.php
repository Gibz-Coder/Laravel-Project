<div>
    <div class="card custom-card">
        <div class="card-header">
            <h6 class="mb-0 fw-bold fs-18" id="performance-date">{{ $displayDate }} Performance Status</h6>
        </div>
        <div class="card-body">
            <div>
                @foreach($stats as $stat)
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <p class="mb-0 fw-bold fs-18 text-muted">{{ $stat['name'] }}</p>
                        </div>
                        <div>
                            <p class="mb-0 fw-bold fs-18 text-{{ $stat['color'] }}">
                                {{ number_format($stat['value'] / 1000000, 1) }}M
                                @if($stat['name'] != 'Target')
                                <span class="fs-14 ms-1 text-{{ $stat['percentage'] >= 100 ? 'success' : ($stat['percentage'] >= 90 ? 'secondary' : 'danger') }}">({{ $stat['percentage'] }}%)</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="progress progress-animate progress-lg" role="progressbar" aria-valuenow="{{ $stat['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar bg-{{ $stat['color'] }}" style="width: {{ $stat['percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach

                @php
                // Get target value from stats
                $targetValue = 0;
                $submittedValue = 0;
                foreach($stats as $stat) {
                    if($stat['name'] == 'Target') {
                        $targetValue = $stat['value'];
                    }
                    if($stat['name'] == 'Submitted') {
                        $submittedValue = $stat['value'];
                    }
                }

                // Calculate ideal based on time of day
                $isPastDate = strtotime($selectedDate) < strtotime(date('Y-m-d'));

                if ($isPastDate) {
                    // For past dates, ideal is the full target
                    $idealValue = $targetValue;
                } else {
                    // For current date, calculate based on current hour
                    $currentHour = (int)date('H');
                    $currentMinute = (int)date('i');
                    $hoursDecimal = $currentHour + ($currentMinute / 60);
                    $idealValue = ($targetValue / 24) * $hoursDecimal;
                }

                // Calculate short value
                $shortValue = max(0, $idealValue - $submittedValue);

                // Determine color for actual (submitted)
                $actualColor = $submittedValue >= $idealValue ? 'success' : 'danger';

                // Determine color for short
                $shortColor = $shortValue > 0 ? 'danger' : 'success';
                @endphp

                <div class="d-flex justify-content-between mt-4 text-center">
                    <div>
                        <div class="fs-14 fw-bold">Ideal</div>
                        <div class="fw-bold fs-16 text-info">{{ number_format($idealValue / 1000000, 1) }}M</div>
                    </div>
                    <div>
                        <div class="fs-14 fw-bold">Actual</div>
                        <div class="fw-bold fs-16 text-{{ $actualColor }}">{{ number_format($submittedValue / 1000000, 1) }}M</div>
                    </div>
                    <div>
                        <div class="fs-14 fw-bold">Short</div>
                        <div class="fw-bold fs-16 text-{{ $shortColor }}">{{ number_format($shortValue / 1000000, 1) }}M</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
