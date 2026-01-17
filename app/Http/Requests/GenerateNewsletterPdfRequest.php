<?php

namespace App\Http\Requests;

use App\Models\Newsletter;
use App\PdfType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Context;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class GenerateNewsletterPdfRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'newsletter_id' => ['required', 'integer', Rule::exists(Newsletter::class, 'id')],
            'pdf_type' => ['required', 'string', Rule::enum(PdfType::class)],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isEmpty() && $this->has('newsletter_id')) {
                $newsletter = Newsletter::find($this->input('newsletter_id'));

                if (! $newsletter) {
                    $validator->errors()->add('newsletter_id', 'The selected newsletter does not exist.');

                    return;
                }

                Context::add('newsletter', $newsletter);
            }
        });
    }
}
