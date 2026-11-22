<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SearchDocsRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2', 'max:100'],
            'filter' => ['nullable', 'array:project'],
            'filter.project' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * The slug as sent: ScoutBuilder's filters would cast "123" to a number and split "a,b".
     */
    public function project(): ?string
    {
        return $this->validated('filter.project');
    }
}
