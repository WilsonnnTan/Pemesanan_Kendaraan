<?php

namespace App\Models;

use CodeIgniter\Model;

class SessionModel extends Model
{
    protected $table = 'sessions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required',
        'access_token' => 'required',
        'refresh_token' => 'required',
        'expires_at' => 'required|valid_date'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID harus diisi'
        ],
        'access_token' => [
            'required' => 'Access token harus diisi'
        ],
        'refresh_token' => [
            'required' => 'Refresh token harus diisi'
        ],
        'expires_at' => [
            'required' => 'Expires at harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ]
    ];

    public function isValidSession($token)
    {
        $session = $this->where('access_token', $token)
                       ->where('expires_at > NOW()')
                       ->first();
        
        return $session !== null;
    }

    public function createSession($userId, $accessToken, $refreshToken, $expiresAt)
    {
        return $this->insert([
            'user_id' => $userId,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt
        ]);
    }

    public function deleteSession($token)
    {
        return $this->where('access_token', $token)->delete();
    }

    public function deleteUserSessions($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }

    public function createNewSession($userId)
    {
        // Generate token
        $accessToken = bin2hex(random_bytes(32));
        $refreshToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+2 hours'));

        // Buat session baru
        $sessionData = [
            'user_id' => $userId,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $sessionId = $this->insert($sessionData);

        if (!$sessionId) {
            return false;
        }

        return [
            'id' => $sessionId,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt
        ];
    }

    public function getValidSession($token)
    {
        return $this->where('access_token', $token)
                   ->where('expires_at > NOW()')
                   ->first();
    }

    public function refreshSession($sessionId)
    {
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        return $this->update($sessionId, ['expires_at' => $expiresAt]);
    }
} 