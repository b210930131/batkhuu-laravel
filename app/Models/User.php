<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'blocked_at'])]  // ✅ role, is_active, blocked_at нэмэх
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser  // ✅ FilamentUser интерфейс нэмэх
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked_at' => 'datetime',  // ✅ blocked_at-г datetime болгон cast хийх
            'is_active' => 'boolean',    // ✅ is_active-г boolean болгон cast хийх
        ];
    }

    public function generatedImages()
    {
        return $this->hasMany(GeneratedImage::class);
    }

    // ✅ Filament панелд хандах эрхийг шалгах
    // public function canAccessPanel(Panel $panel): bool
    //     {
    //         if ($panel->getId() === 'admin') {
    //             return $this->role === 'admin';
    //         }
            
    //         if ($panel->getId() === 'customer') {
    //             return $this->role === 'customer';
    //         }
            
    //         return false;
    //     }

    public function canAccessPanel(Panel $panel): bool
{
    // Админ панель руу зөвхөн админ орно
    if ($panel->getId() === 'admin') {
        return $this->role === 'admin' && $this->is_active;
    }

    // Customer панель руу админ болон хэрэглэгч хоёулаа орж болно
    if ($panel->getId() === 'customer') {
        return in_array($this->role, ['admin', 'customer']) && $this->is_active;
    }

    return false;
}
}