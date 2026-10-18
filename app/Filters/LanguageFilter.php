<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LanguageFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Load custom language helper globally
        helper(['language', 'cookie']);

        $session = session();
        $supported = config('App')->supportedLocales ?? ['id', 'en'];
        $locale = null;

        // 1. Check Session
        if ($session->has('user_locale')) {
            $sessLocale = $session->get('user_locale');
            if (in_array($sessLocale, $supported, true)) {
                $locale = $sessLocale;
            }
        }

        // 2. Check Cookie if session is empty
        if (!$locale) {
            $cookieLocale = get_cookie('user_locale');
            if ($cookieLocale && in_array($cookieLocale, $supported, true)) {
                $locale = $cookieLocale;
                $session->set('user_locale', $locale);
            }
        }

        // 3. Check logged in user's preferred_language
        if (!$locale && $session->has('user_id')) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($session->get('user_id'));
            if ($user && !empty($user['preferred_language']) && in_array($user['preferred_language'], $supported, true)) {
                $locale = $user['preferred_language'];
                $session->set('user_locale', $locale);
            }
        }

        // 4. Fallback to default
        if (!$locale) {
            $locale = config('App')->defaultLocale ?? 'id';
            $session->set('user_locale', $locale);
        }

        // Apply locale to CodeIgniter request
        $request->setLocale($locale);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
