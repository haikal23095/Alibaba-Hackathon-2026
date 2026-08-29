<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserPnr;
use App\Models\AgentChatSession;
use App\Models\CompensationVoucher;
use App\Models\Notification as AppNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['firebase_uid','name', 'email', 'password', 'avatar_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    // Relasi: Satu User bisa memiliki banyak tiket (PNR)
    public function pnrs(): HasMany
    {
        return $this->hasMany(UserPnr::class);
    }

    // Relasi: Satu User bisa memiliki banyak sesi chat dengan AI
    public function chatSessions(): HasMany
    {
        return $this->hasMany(AgentChatSession::class);
    }

    // Relasi: Satu User bisa memiliki banyak voucher kompensasi
    public function compensationVouchers(): HasMany
    {
        return $this->hasMany(CompensationVoucher::class);
    }

    // Relasi: Satu User bisa memiliki banyak notifikasi operasional (tabel notifications).
    // Nama metode sengaja appNotifications() agar tidak menabrak notifications()
    // bawaan trait Notifiable milik Laravel.
    // en: A User can have many operational notifications (notifications table).
    // Named appNotifications() to avoid clashing with Laravel's Notifiable trait notifications().
    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
