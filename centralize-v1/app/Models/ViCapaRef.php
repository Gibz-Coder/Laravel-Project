<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViCapaRef extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vi_capa_ref';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mc_no',
        'mc_type',
        'line',
        'area',
        'daily_capa',
        'actual_capa',
        'mc_size',
        'mc_condition',
    ];

    /**
     * Scope a query to filter by machine condition.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $worktype
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByWorktype($query, $worktype)
    {
        if ($worktype === 'all') {
            // Include all productive conditions
            return $query->whereIn('mc_condition', ['Normal', 'WH Rework', 'Process Rework', 'R/L Rework']);
        }

        if ($worktype === 'Normal') {
            return $query->where('mc_condition', 'Normal');
        }

        if ($worktype === 'Warehouse') {
            return $query->where('mc_condition', 'WH Rework');
        }

        if ($worktype === 'Process Rework') {
            return $query->whereIn('mc_condition', ['Process Rework', 'R/L Rework']);
        }

        if ($worktype === 'Outgoing NG') {
            return $query->where('mc_condition', 'Process Rework');
        }

        // If no specific worktype matches, return all productive conditions
        return $query->whereIn('mc_condition', ['Normal', 'WH Rework', 'Process Rework', 'R/L Rework']);
    }
}
