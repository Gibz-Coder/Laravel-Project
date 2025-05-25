<div>
    <div class="card custom-card main-card-item" style="cursor: pointer;" wire:click="showLotDetails">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between mb-2 flex-wrap">
                <div>
                    <span class="d-block mb-1 fw-bold fs-18 text-muted">Endtime</span>
                    <h2 class="fw-bold text-primary lh-1 mb-0" id="endtime-total">{{ number_format($total / 1000000, 1) }}M</h2>
                </div>
                <div class="text-end">
                    <div class="mb-2">
                        <span class="avatar avatar-md bg-primary svg-white avatar-rounded">
                            <i class="ri-run-line fs-20"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="text-muted fw-medium fs-13">
                    Count: <span class="text-info fw-semibold" id="endtime-count">{{ $count }}</span> lot(s)
                </div>
                <div class="d-flex align-items-center">
                    <span class="fw-semibold {{ $percentage >= 100 ? 'text-success' : ($percentage >= 90 ? 'text-warning' : 'text-danger') }}" id="endtime-percentage">
                        <i class="{{ $percentage >= 100 ? 'ti ti-trending-up' : 'ti ti-trending-down' }} me-1"></i>
                        {{ $percentage }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
