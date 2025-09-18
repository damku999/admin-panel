<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $table = 'notification_templates';

    protected $fillable = [
        'name',
        'type',
        'subject',
        'body',
        'variables',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEmail($query)
    {
        return $query->where('type', 'email');
    }

    public function scopeSms($query)
    {
        return $query->where('type', 'sms');
    }

    public function scopeWhatsApp($query)
    {
        return $query->where('type', 'whatsapp');
    }

    public function scopePush($query)
    {
        return $query->where('type', 'push');
    }

    // Accessors
    public function getIsEmailAttribute()
    {
        return $this->type === 'email';
    }

    public function getIsSmsAttribute()
    {
        return $this->type === 'sms';
    }

    public function getIsWhatsAppAttribute()
    {
        return $this->type === 'whatsapp';
    }

    public function getIsPushAttribute()
    {
        return $this->type === 'push';
    }

    // Methods
    public function renderSubject(array $data = [])
    {
        return $this->replaceVariables($this->subject, $data);
    }

    public function renderBody(array $data = [])
    {
        return $this->replaceVariables($this->body, $data);
    }

    public function getVariableList()
    {
        return $this->variables ?? [];
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    protected function replaceVariables($content, array $data)
    {
        if (empty($content) || empty($data)) {
            return $content;
        }

        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    // Validation rules
    public static function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,sms,whatsapp,push',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ];
    }
}