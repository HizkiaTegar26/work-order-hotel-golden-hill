<?php

namespace App\Models;

use CodeIgniter\Model;

class ResetPasswordTokenModel extends Model
{
    protected $table = 'reset_password_token';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'email',
        'token',
        'expired_at',
    ];
    protected $returnType = 'array';

    public function generateToken($email)
    {
        // Invalidate old tokens for this email
        $this->where('email', $email)->delete();

        $token = bin2hex(random_bytes(32));
        $expiredAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->insert([
            'email'      => $email,
            'token'      => $token,
            'expired_at' => $expiredAt,
        ]);

        return $token;
    }

    public function isValidToken($token)
    {
        return $this->where('token', $token)
                    ->where('expired_at >=', date('Y-m-d H:i:s'))
                    ->first();
    }
}
