<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViProdEndtimeSubmitted extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vi_prod_endtime_submitted';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lot_id',
        'model_id',
        'lot_qty',
        'qty_class',
        'chip_size',
        'work_type',
        'lot_type',
        'mc_no',
        'line',
        'area',
        'mc_type',
        'inspection_type',
        'lipas_yn',
        'ham_yn',
        'status',
        'week_no',
        'endtime_date',
        'cutoff_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'endtime_date' => 'date',
        'lot_qty' => 'integer',
        'week_no' => 'integer',
    ];

    /**
     * Scope a query to filter by date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('endtime_date', $date);
    }

    /**
     * Scope a query to filter by work type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $workType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByWorkType($query, $workType)
    {
        if ($workType === 'all' || $workType === 'Worktype - all') {
            return $query;
        }

        return $query->where('work_type', $workType);
    }

    /**
     * Scope a query to filter by lot type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $lotType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLotType($query, $lotType)
    {
        if ($lotType === 'all' || $lotType === 'Lottype - all') {
            return $query;
        }

        return $query->where('lot_type', $lotType);
    }

    /**
     * Scope a query to filter by cutoff time.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $cutoff
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCutoff($query, $cutoff)
    {
        if ($cutoff === 'all') {
            return $query;
        }

        if ($cutoff === 'day') {
            return $query->whereIn('cutoff_time', ['07:00~12:00', '12:00~16:00', '16:00~19:00']);
        }

        if ($cutoff === 'night') {
            return $query->whereIn('cutoff_time', ['00:00~04:00', '04:00~07:00', '19:00~00:00']);
        }

        return $query->where('cutoff_time', $cutoff);
    }

    /**
     * Scope a query to filter by status (case insensitive).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $status One or more status values to include
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $status)
    {
        if (is_array($status)) {
            // If multiple statuses are provided, use whereIn with LOWER
            $lowercaseStatuses = array_map('strtolower', $status);
            return $query->whereRaw('LOWER(status) IN (' . implode(',', array_fill(0, count($lowercaseStatuses), '?')) . ')', $lowercaseStatuses);
        }

        // Single status case
        return $query->whereRaw('LOWER(status) = ?', [strtolower($status)]);
    }

    /**
     * Get endtime data with filters.
     *
     * @param string $date
     * @param string $cutoff
     * @param string $workType
     * @param string $lotType
     * @return array
     */
    public static function getEndtimeData($date, $cutoff, $workType, $lotType)
    {
        $query = self::byDate($date)
            ->byWorkType($workType)
            ->byLotType($lotType)
            ->byCutoff($cutoff)
            ->byStatus('pending');

        $total = $query->sum('lot_qty');
        $count = $query->count();

        // Get target capacity for percentage calculation
        // This would typically come from another table or calculation
        // For now, we'll use a placeholder value
        $targetCapacity = 100000; // This should be replaced with actual target logic

        $percentage = $targetCapacity > 0 ? round(($total / $targetCapacity) * 100, 1) : 0;

        return [
            'total' => $total,
            'count' => $count,
            'percentage' => $percentage
        ];
    }
}
