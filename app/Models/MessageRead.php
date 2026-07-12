<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRead extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'message_reads';

    /**
     * The model does not have an auto-incrementing ID.
     */
    public $incrementing = false;

    /**
     * Composite primary keys are not natively supported by Eloquent.
     * We disable the default primary key behavior.
     */
    protected $primaryKey = null;

    /**
     * The table does not use created_at and updated_at.
     */
    public $timestamps = false;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'message_id',
        'user_id',
        'read_at',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The message that was read.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * The user who read the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}