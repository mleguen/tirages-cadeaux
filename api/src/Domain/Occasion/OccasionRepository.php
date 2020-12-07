<?php
declare(strict_types=1);

namespace App\Domain\Occasion;

use DateTime;

interface OccasionRepository
{
    public function create(
        DateTime $date,
        string $titre
    ): Occasion;

    /**
     * @throws OccasionNotFoundException
     */
    public function read(int $idOccasion): Occasion;

    /**
     * @return Occasion[]
     */
    public function readAll(): array;
    
    /**
     * @return Occasion[]
     */
    public function readByParticipant(int $idParticipant): array;
    
    public function update(Occasion $occasion): Occasion;
}
