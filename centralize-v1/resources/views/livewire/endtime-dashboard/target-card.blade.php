<div>
    <div class="card custom-card">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between mb-2 flex-wrap">
                <div>
                    <span class="d-block mb-1 fw-bold fs-18 text-muted">Target</span>
                    <h2 class="fw-bold text-info lh-1 mb-0" id="target-total">{{ number_format($total / 1000000, 1) }}M</h2>
                </div>
                <div class="text-end">
                    <div class="mb-2">
                        <span class="avatar avatar-md bg-info svg-white avatar-rounded">
                            <i class="ri-crosshair-2-line fs-20"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <a class="text-muted fw-bold fs-13">{{ $count }} Machines</a>
                <div class="spinner-border text-info spinner-border-sm me-4" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
