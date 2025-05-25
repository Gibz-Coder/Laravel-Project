<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViProdWipRealtime extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vi_prod_wip_realtime';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no',
        'site',
        'facility',
        'major_process',
        'sub_process',
        'lot_status',
        'lot_id',
        'model_id',
        'lot_qty',
        'chip_size',
        'work_type',
        'hold_yn',
        'tat_days',
        'location',
        'lot_details',
        'routing_name',
        'production_team',
        'chip_type',
        'special_code',
        'powder_type',
        'work_equip',
        'rack',
        'facility_2',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lot_qty' => 'integer',
        'tat_days' => 'float',
    ];

    /**
     * Find a lot by its ID
     *
     * @param string $lotId
     * @return self|null
     */
    public static function findByLotId($lotId)
    {
        return self::where('lot_id', $lotId)->first();
    }
}
