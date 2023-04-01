<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\FilmGenre
 *
 * @property int $id
 * @property int $film_id
 * @property int $genre_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Film $film
 * @property-read Genre $genre
 * @method static Builder|FilmGenre newModelQuery()
 * @method static Builder|FilmGenre newQuery()
 * @method static Builder|FilmGenre query()
 * @method static Builder|FilmGenre whereCreatedAt($value)
 * @method static Builder|FilmGenre whereFilmId($value)
 * @method static Builder|FilmGenre whereGenreId($value)
 * @method static Builder|FilmGenre whereId($value)
 * @method static Builder|FilmGenre whereUpdatedAt($value)
 * @mixin Eloquent
 */
class FilmGenre extends Model
{
    use HasFactory;
    protected $table = 'film_genre';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

}
