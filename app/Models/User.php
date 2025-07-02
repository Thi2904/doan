<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Nếu có enum thì uncomment dòng dưới:
// use App\Enums\UserRole;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Các trường được gán tự động (mass assignable).
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role', // cần thêm dòng này nếu muốn lưu role
    ];

    /**
     * Các trường ẩn khi trả về dữ liệu (ví dụ: JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Các kiểu dữ liệu cần ép kiểu.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
