<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'description',
        'is_encrypted',
        'is_active'
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the value attribute, decrypting if needed
     */
    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            try {
                return Crypt::decrypt($value);
            } catch (\Exception $e) {
                return $value; // Return original if decryption fails
            }
        }

        if ($this->type === 'json' && $value) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * Set the value attribute, encrypting if needed
     */
    public function setValueAttribute($value)
    {
        if ($this->is_encrypted) {
            $this->attributes['value'] = Crypt::encrypt($value);
        } elseif ($this->type === 'json' && is_array($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
