<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /*
 *         video: this.currentVideo.uuid,
email: this.email,
screens: this.cart.map(screen => screen.id),
days: this.period
 */
        return [
            'video' => 'required|uuid|exists:videos,uuid',
            'screens' => 'required|array',
            'days' => 'required|array',
            'email' => 'required|email:rfc,dns',
        ];
    }
}
