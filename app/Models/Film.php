<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Film
 *
 * @property int $id
 * @property string $name
 * @property string|null $poster_image
 * @property string|null $preview_image
 * @property string|null $background_image
 * @property string|null $background_color
 * @property string|null $video_link
 * @property string|null $preview_video_link
 * @property string|null $description
 * @property string|null $director
 * @property int|null $run_time
 * @property int|null $released
 * @property string $imdb_id
 * @property string $status
 * @property int|null $scores_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Actor> $actors
 * @property-read int|null $actors_count
 * @property-read Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Collection<int, Genre> $genres
 * @property-read int|null $genres_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static Builder|Film newModelQuery()
 * @method static Builder|Film newQuery()
 * @method static Builder|Film query()
 * @method static Builder|Film whereBackgroundColor($value)
 * @method static Builder|Film whereBackgroundImage($value)
 * @method static Builder|Film whereCreatedAt($value)
 * @method static Builder|Film whereDeletedAt($value)
 * @method static Builder|Film whereDescription($value)
 * @method static Builder|Film whereDirector($value)
 * @method static Builder|Film whereId($value)
 * @method static Builder|Film whereImdbId($value)
 * @method static Builder|Film whereName($value)
 * @method static Builder|Film wherePosterImage($value)
 * @method static Builder|Film wherePreviewImage($value)
 * @method static Builder|Film wherePreviewVideoLink($value)
 * @method static Builder|Film whereReleased($value)
 * @method static Builder|Film whereRunTime($value)
 * @method static Builder|Film whereScoresCount($value)
 * @method static Builder|Film whereStatus($value)
 * @method static Builder|Film whereUpdatedAt($value)
 * @method static Builder|Film whereVideoLink($value)
 * @mixin Eloquent
 */
class Film extends Model
{
    use HasFactory;
    /**
     * @var string
     */
    protected $table = 'films';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'imdb_id',
        'status',
        'name',
        'description',
        'director',
        'run_time',
        'released',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'film_id', 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }


    /**
     * Метод определения рейтинга фильма, основанный на вычислении среднего значения оценок пользователей
     *
     * @return float
     */
    public function getRating(): float
    {
        return round($this->comments->avg('rating'), 1);
    }
}
