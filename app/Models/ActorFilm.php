<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ActorFilm
 *
 * @property int $id
 * @property int $film_id
 * @property int $actor_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Actor $actor
 * @property-read Film $film
 * @method static Builder|ActorFilm newModelQuery()
 * @method static Builder|ActorFilm newQuery()
 * @method static Builder|ActorFilm query()
 * @method static Builder|ActorFilm whereActorId($value)
 * @method static Builder|ActorFilm whereCreatedAt($value)
 * @method static Builder|ActorFilm whereFilmId($value)
 * @method static Builder|ActorFilm whereId($value)
 * @method static Builder|ActorFilm whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ActorFilm extends Model
{
    use HasFactory;
    protected $table = 'actor_film';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

}
