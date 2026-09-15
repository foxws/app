<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Shared by ProjectController and DocumentController — both accept the same
 * optional `?version=` query string to browse a project's docs under a
 * specific registered version instead of its default.
 */
final class ViewDocsRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'version' => ['nullable', 'string'],
        ];
    }

    public function version(): ?string
    {
        return $this->validated('version');
    }
}
