<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Actor
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Film> $films
 * @property-read int|null $films_count
 * @method static Builder|Actor newModelQuery()
 * @method static Builder|Actor newQuery()
 * @method static Builder|Actor query()
 * @method static Builder|Actor whereCreatedAt($value)
 * @method static Builder|Actor whereId($value)
 * @method static Builder|Actor whereName($value)
 * @method static Builder|Actor whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Actor extends Model
{
    use HasFactory;
    protected $table = 'actors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class);
    }
}
