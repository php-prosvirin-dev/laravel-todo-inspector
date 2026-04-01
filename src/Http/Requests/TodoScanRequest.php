<?php

namespace Prosvirin\LaravelTodoInspector\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'redirect' => 'nullable|string|url',
        ];
    }
}
