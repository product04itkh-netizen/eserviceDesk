<?php

namespace App\Http\Requests\Backend\Auth\Service;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ManageUserRequest.
 */
class ManageServiceRequestEdit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      dd($this);
        if( $this->user()->isServiceEnginner() && $this->assignto!=auth()->user()->id)
           return false;

        return ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
