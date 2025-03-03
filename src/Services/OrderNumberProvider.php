<?php
// src/Service/OrderNumberProvider.php
namespace App\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class OrderNumberProvider
{
    private $filePath;
    private $filesystem;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->filesystem = new Filesystem();
    }

    /**
     * Lire le dernier numéro de commande à partir du fichier
     * 
     * @return int Le dernier numéro utilisé
     */
    private function readLastOrderNumber(): int
    {
        if ($this->filesystem->exists($this->filePath)) {
            // Lire le fichier et retourner le dernier numéro
            $lastOrderNumber = file_get_contents($this->filePath);
            return (int) $lastOrderNumber;
        }

        // Si le fichier n'existe pas, on commence à 0
        return 0;
    }

    /**
     * Écrire le nouveau numéro dans le fichier
     */
    private function writeNewOrderNumber(int $newOrderNumber): void
    {
        try {
            // Sauvegarder le nouveau numéro dans le fichier
            file_put_contents($this->filePath, (string) $newOrderNumber);
        } catch (IOExceptionInterface $exception) {
            // Gérer l'exception si le fichier ne peut pas être écrit
            throw new \RuntimeException('Erreur lors de l\'écriture du fichier de numéro de commande.');
        }
    }

    /**
     * Générer le prochain numéro de commande
     * 
     * @return string Le nouveau numéro de commande
     */
    public function generateOrderNumber(): string
    {
        $lastOrderNumber = $this->readLastOrderNumber();
        $newOrderNumber = $lastOrderNumber + 1;  // Incrémenter le dernier numéro

        // Sauvegarder le nouveau numéro dans le fichier
        $this->writeNewOrderNumber($newOrderNumber);

        // Retourner le nouveau numéro sous forme de chaîne
        return str_pad($newOrderNumber, 6, '0', STR_PAD_LEFT); // Exemple: 000001, 000002, ...
    }
}
