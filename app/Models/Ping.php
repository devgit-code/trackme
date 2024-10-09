<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;
use App\Events\PingCreated;


class Ping extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tag_id',
        'user_id',
        'comment',
        'lat',
        'lon',
        'loc_name',
        'accuracy',
        'ip_address',
        'is_approved',
        'img_url',
        ];

        /**
        * The attributes that should be hidden for serialization.
        *
        * @var array<int, string>
        */
        protected $hidden = [
            // 'tag_id',
            // 'user_id',
            'loc_name',
            'ip_address'
        ];

    protected $dispatchesEvents = [
        'created' => PingCreated::class,
    ];

    // Assign random ID and share code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ping) {
            if($ping->hasLocation) {
                $response = Http::get('https://nominatim.openstreetmap.org/reverse?lat='.$ping->lat.'&lon='.$ping->lon.'&format=json&zoom=14&email=isaac@isaacs.site');
                $ping->loc_name = $response->json('name');
            } else {
                $ping->loc_name = null;
            }
        });
    }

    #[Computed]
    public function getHasLocationAttribute(): bool {
        return ($this->lon && $this->lat && $this->accuracy);
    }

    #[Computed]
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    #[Computed]
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
    #[Computed]
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class)->orderBy('created_at', 'desc');
    }
}
