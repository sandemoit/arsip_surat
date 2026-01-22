<?php

namespace App\Services;

use App\Models\User;

/**
 * RoleService - Service untuk mengelola role user
 * 
 * Roles yang tersedia:
 * - admin: Akses penuh (kelola user, kategori, arsip)
 * - staf: Akses terbatas (upload, cari, disposisi arsip)
 */
class RoleService
{
    /**
     * Role constants
     */
    public const ADMIN = 'admin';
    public const STAF = 'staf';

    /**
     * Daftar semua role yang valid
     */
    public const ROLES = [
        self::ADMIN,
        self::STAF,
    ];

    /**
     * Deskripsi role untuk UI
     */
    public const ROLE_LABELS = [
        self::ADMIN => 'Administrator',
        self::STAF => 'Staff',
    ];

    /**
     * Cek apakah user adalah admin
     */
    public static function isAdmin(?User $user): bool
    {
        return $user && $user->role === self::ADMIN;
    }

    /**
     * Cek apakah user adalah staf
     */
    public static function isStaf(?User $user): bool
    {
        return $user && $user->role === self::STAF;
    }

    /**
     * Cek apakah user memiliki role tertentu
     */
    public static function hasRole(?User $user, string $role): bool
    {
        return $user && $user->role === $role;
    }

    /**
     * Cek apakah user memiliki salah satu dari role yang diberikan
     */
    public static function hasAnyRole(?User $user, array $roles): bool
    {
        return $user && in_array($user->role, $roles);
    }

    /**
     * Ambil label role untuk tampilan
     */
    public static function getRoleLabel(string $role): string
    {
        return self::ROLE_LABELS[$role] ?? ucfirst($role);
    }

    /**
     * Validasi apakah role valid
     */
    public static function isValidRole(string $role): bool
    {
        return in_array($role, self::ROLES);
    }

    /**
     * Ambil default role untuk user baru
     */
    public static function getDefaultRole(): string
    {
        return self::STAF;
    }
}
