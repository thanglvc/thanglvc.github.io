<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Select a rule based on the HTTP method.
        $requiredRule = $this->isMethod('PUT') ? 'required' : 'sometimes';
        $descriptionRule = $this->isMethod('PUT') ? 'present' : 'sometimes';

        return [
            'category_id' => [$requiredRule, 'integer', 'exists:categories,id'],
            'name' => [$requiredRule, 'string', 'max:255'],
            'price' => [$requiredRule, 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'description' => [$descriptionRule, 'nullable', 'string', 'max:2000'],
        ];
    }
}
