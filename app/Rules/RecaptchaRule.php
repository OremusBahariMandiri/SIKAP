<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class RecaptchaRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return false;
        }

        try {
            // Disable SSL verification untuk koneksi ke Google
            $response = Http::withOptions([
                'verify' => false,
            ])->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('NOCAPTCHA_SECRET'),
                'response' => $value,
                'remoteip' => request()->ip()
            ]);

            $result = $response->json();

            // Log untuk debugging (opsional)
            \Log::info('reCAPTCHA Response:', $result);

            return isset($result['success']) && $result['success'] === true;

        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('reCAPTCHA Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Verifikasi reCAPTCHA gagal, silakan coba lagi.';
    }
}