<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EmailSetting extends Model
{
    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'is_active',
        'tested_at',
        'test_result'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tested_at' => 'datetime'
    ];

    protected $hidden = [
        'mail_password'
    ];

    public function setMailPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['mail_password'] = Crypt::encryptString($value);
        }
    }

    public function getMailPasswordAttribute($value)
    {
        if ($value) {
            return Crypt::decryptString($value);
        }
        return null;
    }

    public static function getActiveSettings()
    {
        return static::where('is_active', true)->first();
    }
}
