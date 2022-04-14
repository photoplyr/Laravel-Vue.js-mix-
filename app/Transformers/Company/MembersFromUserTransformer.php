<?php

namespace App\Transformers\Company;

use Carbon\Carbon;
use App\Models\Users\User;
use App\Models\Company\CheckinHistory;
use League\Fractal\TransformerAbstract;

/**
 * Class MembersFromUserTransformer.
 *
 * @package namespace App\Transformers\Company;
 */
class MembersFromUserTransformer extends TransformerAbstract
{
    /**
     * Transform the member from User model.
     *
     * @param App\Models\Company\User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        $checkin = CheckinHistory::where('user_id', $model->id)->orderBy('timestamp', 'DESC')->first();

        return [
            'id'                => $model->id,
            'photo'             => $model->photo,
            'displayName'       => $model->display_name,
            'isEligible'        => $model->isEligible() ? true : false,
            'latestCheckin'     => $checkin->timestamp ?? null,
            'latestCheckinDate' => isset($checkin->timestamp) && Carbon::parse($checkin->timestamp) ?? null,
            'program'           => $model->program ? $model->program->name : '',
            'membership'        => $model->member_program ? $model->member_program->membership:null,
        ];
    }

    /**
     * Transform the member.
     *
     * @param array $members  [Members list]
     * @param array $checkins [Members latest checkins]
     *
     * @return array
     */
    public static function combineWithCheckins($members, $checkins)
    {
        foreach ($members as $key => $member) {
            if (isset($checkins[$member['id']])) {
                $members[$key]['latestCheckin']     = Carbon::parse($checkins[$member['id']]->timestamp)->format('m/d/Y h:ia') ?? null;
                $members[$key]['latestCheckinDate'] = Carbon::parse($checkins[$member['id']]->timestamp) ?? null;
            }
        }

        return array_values(collect($members)->sortByDesc('latestCheckinDate')->toArray());
    }
}
