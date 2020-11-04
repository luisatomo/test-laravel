<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\CompanyFiles.
 *
 * @OA\Schema (
 *      description="CompanyFiles",
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="url",
 *          description="url",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="company_files_type_id",
 *          description="company_files_type_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="month",
 *          description="month",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="year",
 *          description="year",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          description="status",
 *          type="boolean"
 *      ),      @OA\Property(
 *          property="deleted_by",
 *          description="deleted_by",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 *
 * @property int                             $id
 * @property int|null                        $company_id
 * @property string|null                     $name
 * @property string|null                     $url
 * @property int|null                        $company_files_type_id
 * @property string|null                     $month
 * @property string|null                     $year
 * @property bool|null                       $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles newQuery()
 * @method static \Illuminate\Database\Query\Builder|CompanyFiles onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereCompanyFilesTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanyFiles whereYear($value)
 * @method static \Illuminate\Database\Query\Builder|CompanyFiles withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CompanyFiles withoutTrashed()
 * @mixin Model
 */
class CompanyFiles extends Model
{
    use SoftDeletes;

    public $table = 'company_files';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'company_id',
        'name',
        'url',
        'company_files_type_id',
        'month',
        'year',
        'status',
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
        'company_id' => 'integer',
        'name' => 'string',
        'url' => 'string',
        'company_files_type_id' => 'integer',
        'month' => 'string',
        'year' => 'string',
        'status' => 'boolean',
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
        'company_id' => 'nullable|integer',
        'name' => 'nullable|string|max:250',
        'url' => 'nullable|string|max:250',
        'company_files_type_id' => 'nullable|integer',
        'month' => 'nullable|string|max:250',
        'year' => 'nullable|string|max:250',
        'status' => 'nullable|boolean',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable',
        'created_by' => 'nullable',
        'updated_by' => 'nullable',
        'deleted_by' => 'nullable',
    ];

    /**
     * Get cached company files.
     *
     * @return mixed
     */
    public function getCachedCompanyFiles()
    {
        return Cache::rememberForever('companyFiles', function () {
            return $this->pluck('name', 'id')->all();
        });
    }
}
