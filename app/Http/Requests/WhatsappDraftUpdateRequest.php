<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WhatsappDraftUpdateRequest extends FormRequest
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
        return [
            "customer_name" => "required|string",
            "customer_phone" => "required|string",
            "notes" => "nullable|string",
            "date" => "required|date",
            "status" => "required|string|in:accepted,rejected,expired,pending",
            "whatsapp_draft_items" => "required|array",
            "whatsapp_draft_items.*.product_id" => "required|exists:products,id",
            "whatsapp_draft_items.*.quantity" => "required|integer|min:1",
        ];
    }
}
