<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CompanyFilesTypes.
 *
 * @OA\Schema (
 *      description="CompanyFilesTypes",
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),      @OA\Property(
 *          property="deleted_by",
 *          description="deleted_by",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 *
 * @property int                             $id
 * @property string|null                     $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes newQuery()
 * @method static \Illuminate\Database\Query\Builder|CompanyFilesTypes onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFilesTypes whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|CompanyFilesTypes withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CompanyFilesTypes withoutTrashed()
 * @mixin Model
 */
class CompanyFilesTypes extends Model
{
    use SoftDeletes;

    public $table = 'company_files_types';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'nullable|string|max:250',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable',
        'created_by' => 'nullable',
        'updated_by' => 'nullable',
        'deleted_by' => 'nullable',
    ];
}
