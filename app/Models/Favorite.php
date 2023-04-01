<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Favorite
 *
 * @property int $id
 * @property int $user_id
 * @property int $film_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Film> $films
 * @property-read int|null $films_count
 * @property-read User $user
 * @method static Builder|Favorite newModelQuery()
 * @method static Builder|Favorite newQuery()
 * @method static Builder|Favorite query()
 * @method static Builder|Favorite whereCreatedAt($value)
 * @method static Builder|Favorite whereFilmId($value)
 * @method static Builder|Favorite whereId($value)
 * @method static Builder|Favorite whereUpdatedAt($value)
 * @method static Builder|Favorite whereUserId($value)
 * @mixin Eloquent
 */
class Favorite extends Model
{
    use HasFactory;
    protected $table = 'favorites';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
