<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'session_id',
        'user_id',
        'name',
        'email',
        'category',
        'subject',
        'message',
        'priority',
        'status',
        'reopen_requested',
        'reopen_reason',
        'reopen_requested_at',
        'reopen_declined_reason',
        'reopen_declined_at',
        'resolution_notes',
        'resolution_reason',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'reopen_requested' => 'boolean',
        'reopen_requested_at' => 'datetime',
        'reopen_declined_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Resolve route binding by either numeric id or ticket_id string (e.g. VLX-TKT-...)
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('ticket_id', $value)
            ->first() ?? parent::resolveRouteBinding($value, $field);
    }

    /**
     * Generate unique reference ID (e.g. VLX-TKT-82419)
     */
    public static function generateTicketId(): string
    {
        do {
            $id = 'VLX-TKT-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        } while (static::where('ticket_id', $id)->exists());

        return $id;
    }
}
