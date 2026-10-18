<?php

use CodeIgniter\I18n\Time;

if (!function_exists('current_locale')) {
    function current_locale(): string
    {
        return service('request')->getLocale() ?: session()->get('user_locale') ?: 'id';
    }
}

if (!function_exists('trans')) {
    function trans(string $line, array $args = []): string
    {
        return lang($line, $args);
    }
}

if (!function_exists('translate_status')) {
    function translate_status(?string $status): string
    {
        if (empty($status)) return '-';
        $key = match(strtolower(trim($status))) {
            'baru', 'new'                     => 'WorkOrder.status_new',
            'accepted', 'disetujui'           => 'WorkOrder.status_accepted',
            'ditolak', 'rejected'             => 'WorkOrder.status_rejected',
            'ditugaskan', 'assigned'          => 'WorkOrder.status_assigned',
            'proses', 'in progress'           => 'WorkOrder.status_process',
            'selesai', 'completed', 'done'    => 'WorkOrder.status_completed',
            'menunggu konfirmasi', 'pending'  => 'WorkOrder.status_pending',
            'escalated', 'dieskalasi'         => 'WorkOrder.status_escalated',
            default                           => null,
        };

        return $key ? lang($key) : $status;
    }
}

if (!function_exists('translate_priority')) {
    function translate_priority(?string $priority): string
    {
        if (empty($priority)) return '-';
        $key = match(strtolower(trim($priority))) {
            'emergency', 'darurat'  => 'WorkOrder.priority_emergency',
            'high', 'tinggi'        => 'WorkOrder.priority_high',
            'medium', 'sedang'      => 'WorkOrder.priority_medium',
            'low', 'rendah'         => 'WorkOrder.priority_low',
            default                 => null,
        };

        return $key ? lang($key) : $priority;
    }
}

if (!function_exists('translate_category')) {
    function translate_category(?string $category): string
    {
        if (empty($category)) return '-';
        $key = match(strtolower(trim($category))) {
            'maintenance'                       => 'WorkOrder.cat_maintenance',
            'it support'                        => 'WorkOrder.cat_it_support',
            'housekeeping service', 'layanan housekeeping' => 'WorkOrder.cat_housekeeping',
            'general repair', 'perbaikan umum'  => 'WorkOrder.cat_general_repair',
            default                             => null,
        };

        return $key ? lang($key) : $category;
    }
}

if (!function_exists('translate_condition')) {
    function translate_condition(?string $condition): string
    {
        if (empty($condition)) return '-';
        $key = match(strtolower(trim($condition))) {
            'baik', 'good'                      => 'Aset.cond_good',
            'rusak ringan', 'light damage'      => 'Aset.cond_light_damage',
            'rusak berat', 'heavy damage'       => 'Aset.cond_heavy_damage',
            default                             => null,
        };

        return $key ? lang($key) : $condition;
    }
}

if (!function_exists('format_date_locale')) {
    function format_date_locale(?string $datetime, string $format = 'd/m/Y H:i'): string
    {
        if (empty($datetime)) return '-';
        $time = strtotime($datetime);
        if (!$time) return '-';

        $locale = current_locale();
        if ($locale === 'en') {
            return date('M d, Y H:i', $time);
        }

        // Indonesian formatting
        $bulanIndo = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        $d = date('d', $time);
        $m = (int)date('m', $time);
        $y = date('Y', $time);
        $hi = date('H:i', $time);

        return "{$d} {$bulanIndo[$m]} {$y} {$hi}";
    }
}
