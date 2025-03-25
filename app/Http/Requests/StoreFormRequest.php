<?php

    // app/Http/Requests/StoreFormRequest.php
    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class StoreFormRequest extends FormRequest
    {
        public function authorize()
        {
            return true; // Engedélyezett minden felhasználó számára
        }

        public function rules()
        {
            return [
                'platform_id' => 'required|exists:platforms,platform_id',
                'domain' => 'required|unique:stores,domain|max:255',
                'lost_package_cost' => 'nullable|numeric',
                'company_name' => 'required|string|max:100',
                'tax_id' => 'required|string|max:50',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:50',
                'subscription_id' => 'required|exists:subscriptions,subscription_id',
            ];
        }
    }
