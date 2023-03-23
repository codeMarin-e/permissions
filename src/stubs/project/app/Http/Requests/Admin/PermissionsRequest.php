<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class PermissionsRequest extends FormRequest
{

    private $mergeReturn = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $chSite = app()->make('Site');
        $rules = [

        ];

        // @HOOK_REQUEST_RULES

        return $rules;
    }

    public function messages() {
        $return = Arr::dot((array)trans('admin/permissions/validation'));

        // @HOOK_REQUEST_MESSAGES

        return $return;
    }

    public function validationData() {
        $inputBag = 'settings';
        $this->errorBag = $inputBag;
        $inputs = $this->all();
        if(!isset($inputs[$inputBag])) {
            throw new ValidationException(trans('admin/permissions/validation.no_inputs') );
        }

        // @HOOK_REQUEST_PREPARE

        $this->replace($inputs);
        request()->replace($inputs); //global request should be replaced, too
        return $inputs[$inputBag];
    }

    public function validated($key = null, $default = null) {
        $validatedData = parent::validated($key, $default);

        // @HOOK_REQUEST_VALIDATED

        if(is_null($key)) {

            // @HOOK_REQUEST_AFTER_VALIDATED

            return array_merge($validatedData, $this->mergeReturn);
        }

        // @HOOK_REQUEST_AFTER_VALIDATED_KEY

        return $validatedData;
    }
}
