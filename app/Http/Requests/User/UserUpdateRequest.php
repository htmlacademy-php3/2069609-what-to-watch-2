<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequestApi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends BaseRequestApi
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                $this->getUniqRule()
            ],
            'password' => 'string|min:8',
            'avatar' => 'image|max:10240',
            'password_confirmation' => 'required_with:password|same:password'
        ];
    }
    private function getUniqRule()
    {
        $rule = Rule::unique(User::class);

        if ($this->isMethod('patch') && Auth::check()) {
            return $rule->ignore(Auth::user());
        }
        return $rule;
    }

}
