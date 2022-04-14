<?php

namespace App\Transformers\Company;

use Carbon\Carbon;
use App\Models\Company\CompanyPrograms;
use League\Fractal\TransformerAbstract;

/**
 * Class MembersTransformer.
 *
 * @package namespace App\Transformers\CompanyPrograms;
 */
class CompanyProgramsTransformer extends TransformerAbstract
{
    /**
     * Transform the CompanyPrograms.
     *
     * @param App\Models\Company\CompanyProgramsTransformer $model
     *
     * @return array
     */
    public function transform(CompanyPrograms $model)
    {
        return [
            'id'                => $model->id,
            'name'               => $model->name,
        ];
    }
}
