<?php

namespace App\Models;

use App\Enums\TagType;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;


class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'type',
        'description',
        'img_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        'share_code',
    ];

    // Allow casting type to enum
    protected $casts = [
        'type' => TagType::class,
    ];

    // Specify that the primary key is a string
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id';

    public function getLocations(): array {
        $pingLocations = [];
        if ($this->pings) {
            foreach ($this->pings as $ping) {
                if ($ping->hasLocation) {
                    $pingLocations[] = [(float) $ping->lat, (float) $ping->lon, $ping->user->name . "\n" . $ping->loc_name . "\n" . $ping->created_at->diffForHumans()];
                }
            }
        }
        return $pingLocations;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pings(): HasMany
    {
        return $this->hasMany(Ping::class)->orderBy('created_at', 'desc');
    }

    // Assign random ID and share code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            // $tag->id = Str::random(8);
            $tag->share_code = Str::random(8);
        });
    }
}
