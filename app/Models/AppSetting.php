<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * App\Models\AppSetting
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string $category
 * @property string|null $description
 * @property bool $is_encrypted
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting active()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting category($category)
 * @method static \Database\Factories\AppSettingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereIsEncrypted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppSetting whereValue($value)
 * @mixin \Eloquent
 */
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
        'is_active',
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
