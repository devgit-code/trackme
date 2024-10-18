<?php

namespace App\Models;

use App\Enums\TagType;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PhpParser\Node\Expr\Cast\Double;

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
        'auto_approve',
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

    /**paramater
    0 default - all user (only approved)
    1 owner
     */
    public function getLocations($mode = 0 ): array {
        $pingLocations = [];
        if ($this->pings) {
            foreach ($this->pings as $ping) {
                if($mode == 0){
                    if($ping->is_approved == 1 && $ping->hasLocation)
                        $pingLocations[] = [(float) $ping->lat, (float) $ping->lon, $ping->user->name . "\n" . $ping->loc_name . "\n" . $ping->created_at->diffForHumans()];
                } 
                else{
                    if ($ping->hasLocation) {
                        $pingLocations[] = [(float) $ping->lat, (float) $ping->lon, $ping->user->name . "\n" . $ping->loc_name . "\n" . $ping->created_at->diffForHumans()];
                    }
                }
            }
        }
        return $pingLocations;
    }

    public function getDistance(): float{
        // Radius of the Earth in kilometers (mean radius)
        $earthRadiusKm = 6371;
        $totalDistance = 0;
        $locations = $this->getLocations();

        // Loop through the locations and calculate distances between consecutive points
        for ($i = 0; $i < count($locations) - 1; $i++) {
            $lat1 = deg2rad($locations[$i][0]);
            $lon1 = deg2rad($locations[$i][1]);
            $lat2 = deg2rad($locations[$i + 1][0]);
            $lon2 = deg2rad($locations[$i + 1][1]);

            // Haversine formula
            $dlat = $lat2 - $lat1;
            $dlon = $lon2 - $lon1;

            $a = sin($dlat / 2) * sin($dlat / 2) +
                cos($lat1) * cos($lat2) *
                sin($dlon / 2) * sin($dlon / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            // Distance between two points
            $distanceKm = $earthRadiusKm * $c;

            // Add the distance to the total distance
            $totalDistance += $distanceKm;
        }

        return $totalDistance;

    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pings(): HasMany
    {
        return $this->hasMany(Ping::class)->orderBy('created_at', 'desc');
    }

    public function latestPing(): HasOne
    {
        return $this->hasOne(Ping::class)->latestOfMany()->withDefault([
            'error' => 'No comments yet',
            'created_at' => null
        ]); // gets the latest comment per post
    }
    
    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class);
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
