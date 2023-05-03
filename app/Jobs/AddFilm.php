<?php

namespace App\Jobs;

use App\Services\FilmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class AddFilm implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public function __construct(private readonly string $imdb_id)
    {
    }

    /**
     * Количество попыток выполнения задания.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Метод, описывающий задачу по добавлению фильма в базу из внешнего источника
     *
     * @param FilmService $filmService
     * @return void
     * @throws Throwable
     */
    public function handle(FilmService $filmService): void
    {
        $filmService->addFilm($this->imdb_id);
    }
}
