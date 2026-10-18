<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\AuditService;

class Language extends BaseController
{
    public function switchLocale($locale = 'id')
    {
        $supported = config('App')->supportedLocales ?? ['id', 'en'];
        $cleanLocale = strtolower(trim($locale));

        if (!in_array($cleanLocale, $supported, true)) {
            $cleanLocale = config('App')->defaultLocale ?? 'id';
        }

        // 1. Save to Session
        session()->set('user_locale', $cleanLocale);

        // 2. Save to Cookie (30 days)
        helper('cookie');
        set_cookie('user_locale', $cleanLocale, 30 * 86400, '', '/', '', false, true);

        // 3. Update in Database if user is logged in
        if (session()->has('user_id')) {
            $userId = (int) session()->get('user_id');
            $userModel = new UserModel();
            $userModel->update($userId, [
                'preferred_language' => $cleanLocale
            ]);

            // Audit log
            $audit = new AuditService();
            $langName = $cleanLocale === 'id' ? 'Bahasa Indonesia' : 'English';
            $audit->log("Mengubah preferensi bahasa menjadi: {$langName} ({$cleanLocale})", 'Internasionalisasi', $userId);
        }

        // Apply to current request
        service('request')->setLocale($cleanLocale);

        // Redirect back to previous page or dashboard
        $redirectUrl = previous_url() ?: base_url('dashboard');
        
        // Prevent redirect loop if previous URL was /lang/...
        if (str_contains($redirectUrl, '/lang/')) {
            $redirectUrl = base_url('dashboard');
        }

        return redirect()->to($redirectUrl);
    }
}
