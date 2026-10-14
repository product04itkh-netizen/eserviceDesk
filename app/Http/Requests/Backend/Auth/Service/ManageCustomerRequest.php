<?php

namespace App\Http\Requests\Backend\Auth\Service;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ManageUserRequest.
 */
class ManageCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() || $this->user()->isServiceAdmin() || $this->user()->isStockAdmin();
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
