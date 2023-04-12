<?php

namespace App\Handler;

use App\Models\ActorFilm;
use App\Models\Film;

class GetFilmInfo
{
    public static function getInfo($id) {
        $film = Film::find($id);

        $filmStarring = $film->actors->pluck('name');
        $filmGenres = $film->genres->pluck('title');
        $filmRating = $film->getRating();
        $filmScoresCount = $film->comments->count();

        return [
            'id' => $id,
            'name' => $film->name,
            'poster_image' => $film->poster_image,
            'preview_image' => $film->preview_image,
            'background_image' => $film->background_image,
            'background_color' => $film->background_color,
            'video_link' => $film->video_link,
            'preview_video_link' => $film->preview_video_link,
            'description' => $film->description,
            'rating' => $filmRating,
            'score_count'=> $filmScoresCount,
            'director' => $film->director,
            'starring' => $filmStarring,
            'run_time' => $film->run_time,
            'genre' => $filmGenres,
            'released' => $film->released,
        ];
    }
}
